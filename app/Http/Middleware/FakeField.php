<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;

class FakeField
{
    /**
     * Purpose: handles an HTTP request in FakeField middleware.
     *
     * Action: performs request checks or transformations before passing the request to the next handler.
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allFields = $this->setFakeFields($request);
        $this->setOriginalFieldInRequest($request, $allFields);
        return $next($request);
    }

    /**
     * Purpose: handles an HTTP request in FakeField middleware.
     *
     * Action: performs request checks or transformations before passing the request to the next handler.
     *
     * @param $request
     * @return array
     */
    public function setFakeFields(Request $request): array
    {
        $models = $this->getModels();
        $base_request_key = $request->input('base_key');
        if($base_request_key !=null){
            $base_request_key = encode_decode($base_request_key, true);
        }
        if (
            $base_request_key != null) {
            $base_key = $serial = $base_request_key;
        }
        else{
            $base_key = $serial = rand(1, 500);
        }
        $prefix = config('fakefields.prefix');
        $tableKeys = [];
        foreach ($models as $model) {
            $table = '';//$model->getTable().'_';
            $fields = $this->accessProtected($model, 'fakeFields');
            if (empty($fields)) {
                continue;
            }
            foreach ($fields as $field) {
                if(!array_key_exists($field, $tableKeys))
                {
                    $tableKeys[$table . $field] = $prefix . $serial;

                    $serial++;
                }
            }
        }

        $allFields = [
            'table_keys' => $tableKeys,
            'base_key' => $base_key
        ];
        config()->set('fakefields', $allFields);
        return $allFields;
    }

    /**
     * Purpose: handles an HTTP request in FakeField middleware.
     *
     * Action: performs request checks or transformations before passing the request to the next handler.
     *
     */
    public function getModels(): array
    {
        $models = [];
        $modelPath = config('fakefields.model_path');
        $path = base_path($modelPath);
        $namespace = str_replace('/', '\\', Str::studly($modelPath));

        foreach (glob($path . '/*.php') ?: [] as $file) {
            $className = $namespace . '\\' . basename($file, '.php');
            if (class_exists($className)) {
                $models[] = new $className();
            }
        }

        foreach (glob($path . '/*', GLOB_ONLYDIR) ?: [] as $dir) {
            foreach (glob($dir . '/*.php') ?: [] as $file) {
                $className = $namespace . '\\' . basename($dir) . '\\' . basename($file, '.php');
                if (class_exists($className)) {
                    $models[] = new $className();
                }
            }
        }

        return $models;
    }

    /**
     * Purpose: handles an HTTP request in FakeField middleware.
     *
     * Action: performs request checks or transformations before passing the request to the next handler.
     *
     * @param $obj
     * @param $prop
     * @return array|mixed
     * @throws \ReflectionException
     */
    public function accessProtected(object $obj, string $prop): array
    {
        $reflection = new ReflectionClass($obj);
        if ($reflection->hasProperty($prop)) {
            $property = $reflection->getProperty($prop);
            $property->setAccessible(true);
            return $property->getValue($obj);
        } else {
            return [];
        }

    }

    /**
     * Purpose: handles an HTTP request in FakeField middleware.
     *
     * Action: performs request checks or transformations before passing the request to the next handler.
     *
     * @param $request
     * @param $allFields
     */
    private function setOriginalFieldInRequest(Request $request, array $allFields): void
    {
        $fakeFields = array_flip($allFields['table_keys']);
        $inputs = $request->all();
        $newRequestArray = [];
        $inputKeys = [];
        $isFileExists = false;
        foreach ($inputs as $key => $value) {
            if (array_key_exists($key, $fakeFields)) {

                $inputKeys[$fakeFields[$key]] = $key;

                if($request->hasFile($key)){
                    $newRequestFileArray[$fakeFields[$key]] = $value;
                    $request->files->remove($key);
                    $isFileExists = true;
                }else{
                    $newRequestArray[$fakeFields[$key]] = $value;
                    $request->request->remove($key);
                }

            }
        }
        $request->merge($newRequestArray);

        if($isFileExists){
            $request->files->replace($newRequestFileArray);
        }

        config()->set('fakefields.input_keys', $inputKeys);
    }
}

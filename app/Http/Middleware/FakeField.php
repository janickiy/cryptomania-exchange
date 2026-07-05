<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use ReflectionClass;

class FakeField
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(mixed $request, Closure $next): mixed
    {
        $allFields = $this->setFakeFields($request);
        $this->setOriginalFieldInRequest($request, $allFields);
        return $next($request);
    }

    /**
     * @param $request
     * @return array
     */
    public function setFakeFields(mixed $request): mixed
    {
        $models = $this->getModels();
        $base_request_key = $request->get('base_key');
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

    public function getModels(): mixed
    {
        $models = [];
        $modelPath = config('fakefields.model_path');
        $path = base_path($modelPath);
        foreach (glob($path.'/*') as $file) {
            if(is_dir($file)){
                $dir = $file;
                foreach (glob($dir.'/*.php') as $file){
                    $className = str_replace('/', '\\', Str::studly($modelPath)) . '\\' .basename($dir).'\\'. basename($file, '.php');
                    array_push($models, new $className());
                }
            }else{
                $className = str_replace('/', '\\', Str::studly($modelPath)) . '\\'. basename($file, '.php');
                array_push($models, new $className());
            }
        }
        return $models;
    }

    /**
     * @param $obj
     * @param $prop
     * @return array|mixed
     * @throws \ReflectionException
     */
    public function accessProtected(mixed $obj, mixed $prop): mixed
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
     * @param $request
     * @param $allFields
     */
    private function setOriginalFieldInRequest(mixed $request, mixed $allFields): void
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

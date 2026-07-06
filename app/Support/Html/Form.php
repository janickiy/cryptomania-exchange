<?php

namespace App\Support\Html;

use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class Form
{
    protected static mixed $model = null;

    /**
     * Purpose: builds an HTML element or helper value through open.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function open(array $options = []): HtmlString
    {
        $method = strtoupper((string) ($options['method'] ?? $options['medthod'] ?? 'POST'));
        $formMethod = in_array($method, ['GET', 'POST'], true) ? $method : 'POST';
        $action = self::resolveAction($options);
        $attributes = self::attributes(array_merge(
            Arr::except($options, ['route', 'url', 'action', 'method', 'medthod', 'model']),
            ['method' => $formMethod, 'action' => $action]
        ));

        $html = '<form' . $attributes . '>';

        if ($formMethod !== 'GET') {
            $html .= csrf_field();
        }

        if ($formMethod !== $method) {
            $html .= method_field($method);
        }

        return new HtmlString($html);
    }

    /**
     * Purpose: builds an HTML element or helper value through model.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function model(mixed $model, array $options = []): HtmlString
    {
        static::$model = $model;

        return static::open($options);
    }

    /**
     * Purpose: builds an HTML element or helper value through close.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function close(): HtmlString
    {
        static::$model = null;

        return new HtmlString('</form>');
    }

    /**
     * Purpose: builds an HTML element or helper value through text.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function text(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return static::input('text', $name, $value, $options);
    }

    /**
     * Purpose: builds an HTML element or helper value through email.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function email(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return static::input('email', $name, $value, $options);
    }

    /**
     * Purpose: builds an HTML element or helper value through password.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function password(string $name, array $options = []): HtmlString
    {
        return static::input('password', $name, null, $options);
    }

    /**
     * Purpose: builds an HTML element or helper value through hidden.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function hidden(string $name, mixed $value = null, array $options = []): HtmlString
    {
        return static::input('hidden', $name, $value, $options);
    }

    /**
     * Purpose: builds an HTML element or helper value through file.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function file(string $name, array $options = []): HtmlString
    {
        return static::input('file', $name, null, $options);
    }

    /**
     * Purpose: builds an HTML element or helper value through input.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function input(string $type, string $name, mixed $value = null, array $options = []): HtmlString
    {
        $value = $value ?? static::modelValue($name);
        $attributes = array_merge($options, [
            'type' => $type,
            'name' => $name,
        ]);

        if ($type !== 'file') {
            $attributes['value'] = $value;
        }

        return new HtmlString('<input' . self::attributes($attributes) . '>');
    }

    /**
     * Purpose: builds an HTML element or helper value through textarea.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function textarea(string $name, mixed $value = null, array $options = []): HtmlString
    {
        $value = $value ?? static::modelValue($name);
        $attributes = self::attributes(array_merge($options, ['name' => $name]));

        return new HtmlString('<textarea' . $attributes . '>' . e((string) $value) . '</textarea>');
    }

    /**
     * Purpose: builds an HTML element or helper value through select.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function select(string $name, iterable $list = [], mixed $selected = null, array $options = []): HtmlString
    {
        $selected = $selected ?? static::modelValue($name);
        $placeholder = $options['placeholder'] ?? null;
        unset($options['placeholder']);

        $html = '<select' . self::attributes(array_merge($options, ['name' => $name])) . '>';

        if ($placeholder !== null) {
            $html .= '<option value="">' . e((string) $placeholder) . '</option>';
        }

        foreach ($list as $value => $label) {
            $html .= '<option' . self::attributes([
                'value' => $value,
                'selected' => (string) $value === (string) $selected,
            ]) . '>' . e((string) $label) . '</option>';
        }

        return new HtmlString($html . '</select>');
    }

    /**
     * Purpose: builds an HTML element or helper value through radio.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function radio(string $name, mixed $value = null, bool $checked = false, array $options = []): HtmlString
    {
        return static::checkable('radio', $name, $value, $checked, $options);
    }

    /**
     * Purpose: builds an HTML element or helper value through checkbox.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function checkbox(string $name, mixed $value = 1, bool $checked = false, array $options = []): HtmlString
    {
        return static::checkable('checkbox', $name, $value, $checked, $options);
    }

    /**
     * Purpose: builds an HTML element or helper value through submit.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function submit(string $value = 'Submit', array $options = []): HtmlString
    {
        return static::input('submit', 'submit', $value, $options);
    }

    /**
     * Purpose: builds an HTML element or helper value through reset.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    public static function reset(string $value = 'Reset', array $options = []): HtmlString
    {
        return static::input('reset', 'reset', $value, $options);
    }

    /**
     * Purpose: builds an HTML element or helper value through checkable.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    protected static function checkable(string $type, string $name, mixed $value, bool $checked, array $options): HtmlString
    {
        return new HtmlString('<input' . self::attributes(array_merge($options, [
            'type' => $type,
            'name' => $name,
            'value' => $value,
            'checked' => $checked,
        ])) . '>');
    }

    /**
     * Purpose: builds an HTML element or helper value through resolve action.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    protected static function resolveAction(array $options): string
    {
        if (isset($options['route'])) {
            $route = $options['route'];

            return is_array($route)
                ? route((string) array_shift($route), $route)
                : route((string) $route);
        }

        return (string) ($options['url'] ?? $options['action'] ?? url()->current());
    }

    /**
     * Purpose: builds an HTML element or helper value through model value.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    protected static function modelValue(string $name): string|int|float|bool|array|object|null
    {
        if (static::$model === null) {
            return null;
        }

        $key = preg_replace('/\[[^\]]*]/', '', $name);

        return data_get(static::$model, $key);
    }

    /**
     * Purpose: builds an HTML element or helper value through attributes.
     *
     * Action: is used by Blade templates to generate forms and attributes consistently.
     *
     */
    protected static function attributes(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $key => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            if ($value === true) {
                $html .= ' ' . e((string) $key);
                continue;
            }

            $html .= ' ' . e((string) $key) . '="' . e((string) $value) . '"';
        }

        return $html;
    }
}

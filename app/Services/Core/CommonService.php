<?php

namespace App\Services\Core;

class CommonService
{
    public function customEncode(): void
    {
        $code = $this->_code();
    }

    public function customDecode(): void
    {
        $code = $this->_code(true);
    }

    private function _code(mixed $decode = false): mixed
    {
        return ['o', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    }
}
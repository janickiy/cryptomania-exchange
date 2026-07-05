<?php

namespace App\Services\Core;

class CommonService
{
    public function customEncode()
    {
        $code = $this->_code();
    }

    public function customDecode()
    {
        $code = $this->_code(true);
    }

    private function _code($decode = false)
    {
        return ['o', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    }
}
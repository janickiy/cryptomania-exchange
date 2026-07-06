<?php

namespace App\Services\Core;

class CommonService
{
    /**
     * Purpose: executes the custom encode service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function customEncode(): void
    {
        $code = $this->_code();
    }

    /**
     * Purpose: executes the custom decode service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function customDecode(): void
    {
        $code = $this->_code(true);
    }

    /**
     * Purpose: executes the code service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    private function _code(mixed $decode = false): array
    {
        return ['o', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    }
}

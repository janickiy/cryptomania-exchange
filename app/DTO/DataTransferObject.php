<?php

namespace App\DTO;

interface DataTransferObject
{
    /**
     * Purpose: describes the to array contract for DataTransferObject.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}

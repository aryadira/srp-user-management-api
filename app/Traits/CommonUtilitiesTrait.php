<?php

namespace App\Traits;

trait CommonUtilitiesTrait
{
    protected function generateRandomNumberId()
    {
        return rand(1000000000, 9999999999); // Menghasilkan angka acak 10 digit
    }
}

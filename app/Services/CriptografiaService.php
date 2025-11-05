<?php

namespace App\Services;

class CriptografiaService
{
    public static function decrypt($data)
    {
        try {
            return decrypt($data);
        } catch (\Exception $e) {
            return null;
        }
    }
}
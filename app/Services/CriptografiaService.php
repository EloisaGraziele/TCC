<?php

namespace App\Services;

class CriptografiaService
{
    public static function encrypt($data)
    {
        try {
            return encrypt($data);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public static function decrypt($data)
    {
        try {
            return decrypt($data);
        } catch (\Exception $e) {
            return null;
        }
    }
}
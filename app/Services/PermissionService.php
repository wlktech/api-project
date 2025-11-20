<?php
namespace App\Services;

use App\Helpers\ResponseHelper;
use Closure;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class PermissionService
{
    /**
     * Check if user has permission
     * @param string $permission
    */

    public function checkPermission(string $permission, callable $callback)
    {
        if(JWTAuth::user()->hasPermission($permission)){
            return $callback();
        }
        return ResponseHelper::forbidden('You have no permission for this.');
    }
}
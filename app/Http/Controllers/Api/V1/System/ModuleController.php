<?php

namespace App\Http\Controllers\Api\V1\System;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ModuleController extends Controller
{
    protected $permissionService;
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->permissionService->checkPermission('module_list', function() use ($request) {
            $size = $request->get('size', 15);
            $modules = Module::latest()->paginate($size);
            return ResponseHelper::paginated($modules,"Modules retrieved successfully.");
        });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

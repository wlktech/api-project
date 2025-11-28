<?php

namespace App\Http\Controllers\Api\V1\System;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Api\System\ModuleRequest;
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
            $size = (int) $request->get('size', 15);
            $modules = Module::paginate($size);
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
    public function store(ModuleRequest $request)
    {
        return $this->permissionService->checkPermission('module_create', function() use ($request) {
            $request->validated();
            $module = Module::create([
                'name' => $request->name,
                'slug' => implode('-', explode(' ', strtolower($request->name))),
            ]);
            return ResponseHelper::success($module,"Module created successfully.");
        });
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
    public function update(ModuleRequest $request, string $id)
    {
        return $this->permissionService->checkPermission('module_update', function() use ($request, $id) {
            $request->validated();
            $module = Module::find($id);
            if(!$module){
                return ResponseHelper::notFound("Module not found.");
            }
            $module->update([
                'name' => $request->name,
                'slug' => implode('-', explode(' ', strtolower($request->name))),
            ]);
            return ResponseHelper::success($module,"Module updated successfully.");
        });
    }

    public function statusChange($id)
    {
        return $this->permissionService->checkPermission('module_status_change', function() use ($id) {
            $module = Module::find($id);
            if(!$module){
                return ResponseHelper::notFound("Module not found.");
            }
            $module->update([
                'is_active' => !$module->is_active,
            ]);
            return ResponseHelper::success($module,"Module status updated successfully.");
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->permissionService->checkPermission('module_delete', function() use ($id){
            $module = Module::find($id);
            if(!$module){
                return ResponseHelper::notFound("Module not found.");
            }
            $module->delete();
            return ResponseHelper::success($module,"Module deleted successfully.");
        });
    }
}

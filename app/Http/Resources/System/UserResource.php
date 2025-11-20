<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,  
            'department_id' => $this->department_id,         
            'created_at' => $this->created_at->format('Y-m-d H:iA'),               
            'updated_at' => $this->updated_at->format('Y-m-d H:iA'),
            'roles' => $this->roleResource($this->roles)               
        ];
    }

    protected function roleResource($roles)
    {
        return $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'department_id' => $role->department_id,
                'department' => $role->department->name,
            ];
        });
    }
}

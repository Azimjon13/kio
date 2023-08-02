<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'job' => $this->job,
            'phone' => $this->phone,
            'username' => $this->username,
            'roles' => array_map(
                function ($role) {
                    return $role['name'];
                },
                $this->roles->toArray()
            ),
            'places' => array_map(
                function ($place) {
                    return $place['id_place'];
                },
                $this->user_places->toArray()
            ),
            'permissions' => array_map(
                function ($permission) {
                    return $permission['name'];
                },
                $this->getAllPermissions()->toArray()
            ),
            'avatar' => env('APP_URL').'/storage/user.png',
        ];
    }
}

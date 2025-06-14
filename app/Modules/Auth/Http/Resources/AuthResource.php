<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Resources;

use App\Modules\Core\Http\Resources\CoreResource;

class AuthResource extends CoreResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // Add other fields as necessary
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

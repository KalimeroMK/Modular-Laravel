<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'created_at' => $this->created_at instanceof \Carbon\Carbon ? $this->created_at->toISOString() : ($this->created_at ? (string) $this->created_at : null),
            'updated_at' => $this->updated_at instanceof \Carbon\Carbon ? $this->updated_at->toISOString() : ($this->updated_at ? (string) $this->updated_at : null),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Http\Resources;

use App\Modules\Permission\Infrastructure\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin Permission
 */
class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    #[Override]
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

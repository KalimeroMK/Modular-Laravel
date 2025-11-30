<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Http\Resources;

use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at instanceof \Carbon\Carbon ? $this->email_verified_at->toISOString() : ($this->email_verified_at ? (string) $this->email_verified_at : null),
            'created_at' => $this->created_at instanceof \Carbon\Carbon ? $this->created_at->toISOString() : ($this->created_at ? (string) $this->created_at : null),
            'updated_at' => $this->updated_at instanceof \Carbon\Carbon ? $this->updated_at->toISOString() : ($this->updated_at ? (string) $this->updated_at : null),
        ];
    }
}

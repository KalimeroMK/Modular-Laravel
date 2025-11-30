<?php

declare(strict_types=1);

namespace App\Modules\NonExistentStubModule\Infrastructure\Http\Resources;

use App\Modules\NonExistentStubModule\Infrastructure\Models\NonExistentStubModule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin NonExistentStubModule
 */
class NonExistentStubModuleResource extends JsonResource
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
            //             'name' => $this->name,
            'created_at' => $this->created_at instanceof \Carbon\Carbon ? $this->created_at->toISOString() : ($this->created_at ? (string) $this->created_at : null),
            'updated_at' => $this->updated_at instanceof \Carbon\Carbon ? $this->updated_at->toISOString() : ($this->updated_at ? (string) $this->updated_at : null),
        ];
    }
}

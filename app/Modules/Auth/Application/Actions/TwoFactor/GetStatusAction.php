<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions\TwoFactor;

use App\Modules\Auth\Application\Services\TwoFactor\ServiceInterface;
use App\Modules\User\Infrastructure\Models\User;

class GetStatusAction
{
    public function __construct(
        protected ServiceInterface $twoFactorService,
    ) {}

    /**
     * @return array{enabled: bool}
     */
    public function execute(User $user): array
    {
        return [
            'enabled' => $this->twoFactorService->isTwoFactorEnabled($user),
        ];
    }
}

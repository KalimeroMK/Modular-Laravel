<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions\TwoFactor;

use App\Modules\Auth\Application\Services\TwoFactor\ServiceInterface;
use App\Modules\Auth\Infrastructure\Exceptions\TwoFactorNotEnabledException;
use App\Modules\User\Infrastructure\Models\User;

class DisableAction
{
    public function __construct(
        protected ServiceInterface $twoFactorService,
    ) {}

    public function execute(User $user): bool
    {
        if (! $this->twoFactorService->isTwoFactorEnabled($user)) {
            throw new TwoFactorNotEnabledException();
        }

        return $this->twoFactorService->disableTwoFactor($user);
    }
}

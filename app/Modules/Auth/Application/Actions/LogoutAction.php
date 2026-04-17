<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions;

use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Http\Request;

class LogoutAction
{
    public function __construct(
        protected IssueTokenServiceInterface $tokenService,
    ) {}

    public function execute(Request $request): void
    {
        $user = $request->user();

        if ($user instanceof User) {
            $this->tokenService->revokeToken($user);
        }
    }
}

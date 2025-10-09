<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions;

use App\Modules\Auth\Application\DTO\UserResponseDTO;
use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use Exception;
use Illuminate\Http\Request;

class GetCurrentUserAction
{
    public function __construct(
        protected IssueTokenServiceInterface $tokenService,
    ) {}

    public function execute(Request $request): UserResponseDTO
    {
        $user = $request->user();

        if ($user === null) {
            throw new Exception('User not authenticated');
        }

        return UserResponseDTO::fromUser($user);
    }
}

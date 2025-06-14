<?php

declare(strict_types=1);

namespace App\Modules\Auth\Interfaces;

interface AuthInterface
{
    public function login(array $credentials): array;

    public function logout(): void;

    public function register(array $data): mixed;
}

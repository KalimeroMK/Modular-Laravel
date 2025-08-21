<?php

declare(strict_types=1);

namespace App\Modules\Auth\Interfaces;

interface AuthInterface
{
    /**
     * @param  array<string, mixed>  $credentials
     * @return array<string, mixed>
     */
    public function login(array $credentials): array;

    public function logout(): void;

    /**
     * @param  array<string, mixed>  $data
     */
    public function register(array $data): mixed;
}

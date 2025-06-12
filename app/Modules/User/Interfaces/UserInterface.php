<?php

namespace App\Modules\User\Interfaces;

interface UserInterface
{
    public function getAll(): mixed;
    public function findById(int $id): mixed;
}

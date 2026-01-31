<?php

declare(strict_types=1);

namespace App\Modules\Core\ValueObjects;

use InvalidArgumentException;
use Stringable;

readonly class Email implements Stringable
{
    public function __construct(
        public string $value
    ) {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: {$value}");
        }
    }

    


    public function __toString(): string
    {
        return $this->value;
    }

    


    public static function fromString(string $email): self
    {
        return new self($email);
    }

    


    public function localPart(): string
    {
        return explode('@', $this->value)[0];
    }

    


    public function domain(): string
    {
        return explode('@', $this->value)[1];
    }

    


    public function isFromDomain(string $domain): bool
    {
        return $this->domain() === $domain;
    }

    


    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}

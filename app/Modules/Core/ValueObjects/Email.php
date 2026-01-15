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

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create Email from string.
     */
    public static function fromString(string $email): self
    {
        return new self($email);
    }

    /**
     * Get the local part of the email (before @).
     */
    public function localPart(): string
    {
        return explode('@', $this->value)[0];
    }

    /**
     * Get the domain part of the email (after @).
     */
    public function domain(): string
    {
        return explode('@', $this->value)[1];
    }

    /**
     * Check if email is from a specific domain.
     */
    public function isFromDomain(string $domain): bool
    {
        return $this->domain() === $domain;
    }

    /**
     * Check equality with another Email.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}

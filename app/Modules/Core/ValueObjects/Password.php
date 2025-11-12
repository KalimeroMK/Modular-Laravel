<?php

declare(strict_types=1);

namespace App\Modules\Core\ValueObjects;

use InvalidArgumentException;

readonly class Password
{
    private const int MIN_LENGTH = 8;

    public function __construct(
        public string $value
    ) {
        $this->validate();
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create Password from string.
     */
    public static function fromString(string $password): self
    {
        return new self($password);
    }

    /**
     * Check if password meets strength requirements.
     */
    public function isStrong(): bool
    {
        return mb_strlen($this->value) >= self::MIN_LENGTH
            && preg_match('/[A-Z]/', $this->value) // Has uppercase
            && preg_match('/[a-z]/', $this->value) // Has lowercase
            && preg_match('/[0-9]/', $this->value) // Has number
            && preg_match('/[^A-Za-z0-9]/', $this->value); // Has special char
    }

    /**
     * Get password strength score (0-4).
     */
    public function strengthScore(): int
    {
        $score = 0;

        if (mb_strlen($this->value) >= self::MIN_LENGTH) {
            $score++;
        }
        if (preg_match('/[A-Z]/', $this->value)) {
            $score++;
        }
        if (preg_match('/[a-z]/', $this->value)) {
            $score++;
        }
        if (preg_match('/[0-9]/', $this->value)) {
            $score++;
        }
        if (preg_match('/[^A-Za-z0-9]/', $this->value)) {
            $score++;
        }

        return min($score, 4);
    }

    /**
     * Validate password strength.
     */
    private function validate(): void
    {
        if (mb_strlen($this->value) < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                'Password must be at least '.self::MIN_LENGTH.' characters long'
            );
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Core\ValueObjects;

use InvalidArgumentException;
use Stringable;

readonly class Password implements Stringable
{
    private const int MIN_LENGTH = 8;

    public function __construct(
        public string $value
    ) {
        $this->validate();
    }

    


    public function __toString(): string
    {
        return $this->value;
    }

    


    public static function fromString(string $password): self
    {
        return new self($password);
    }

    


    public function isStrong(): bool
    {
        return mb_strlen($this->value) >= self::MIN_LENGTH
            && preg_match('/[A-Z]/', $this->value) 
            && preg_match('/[a-z]/', $this->value) 
            && preg_match('/\d/', $this->value) 
            && preg_match('/[^A-Za-z0-9]/', $this->value); 
    }

    


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
        if (preg_match('/\d/', $this->value)) {
            $score++;
        }
        if (preg_match('/[^A-Za-z0-9]/', $this->value)) {
            $score++;
        }

        return min($score, 4);
    }

    


    private function validate(): void
    {
        if (mb_strlen($this->value) < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                'Password must be at least '.self::MIN_LENGTH.' characters long'
            );
        }
    }
}

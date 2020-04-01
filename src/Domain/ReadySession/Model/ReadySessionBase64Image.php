<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Model;

final class ReadySessionBase64Image
{
    /** @var string  */
    private $value;

    public function __construct(string $base64Image)
    {
        $this->value = $base64Image;
    }

    public static function fromString(string $base64Image): ReadySessionBase64Image
    {
        return new self($base64Image);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ReadySessionBase64Image $other): bool
    {
        return $this->value === $other->value;
    }
}

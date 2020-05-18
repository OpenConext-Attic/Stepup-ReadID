<?php

declare(strict_types=1);

namespace StepupReadId\Domain\Session\Model;

use StepupReadId\Domain\Session\Exception\InvalidSessionDocumentAttributeException;

final class SessionDocumentAttribute
{
    /** @var string */
    private $name;
    /** @var string */
    private $value;

    private function __construct(string $name, string $value)
    {
        $this->checkNotEmptyName($name);

        $this->name  = $name;
        $this->value = $value;
    }

    public static function with(string $name, string $value): SessionDocumentAttribute
    {
        return new self($name, $value);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }

    private function checkNotEmptyName(string $name): void
    {
        if (empty($name)) {
            throw InvalidSessionDocumentAttributeException::becauseEmptyName();
        }
    }
}

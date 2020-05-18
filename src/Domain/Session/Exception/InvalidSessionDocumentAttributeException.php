<?php

declare(strict_types=1);

namespace StepupReadId\Domain\Session\Exception;

use InvalidArgumentException;

final class InvalidSessionDocumentAttributeException extends InvalidArgumentException
{
    public static function becauseEmptyName(): self
    {
        return new self('Attribute name cannot be empty');
    }
}

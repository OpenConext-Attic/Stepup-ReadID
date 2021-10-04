<?php

declare(strict_types=1);

namespace StepupReadId\Domain\Session\Exception;

use RuntimeException;
use function sprintf;

/**
 * @SuppressWarnings(PHPMD.LongClassName)
 */
final class SessionDocumentAttributeNotFoundException extends RuntimeException
{
    public static function withName(string $name): self
    {
        return new self(sprintf('Not found document attribute name \'%s\'', $name));
    }
}

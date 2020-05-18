<?php

declare(strict_types=1);

namespace StepupReadId\Domain\Session\Model;

use StepupReadId\Domain\Session\Exception\SessionDocumentAttributeNotFoundException;
use function array_key_exists;

final class SessionDocument
{
    /** @var array<string, mixed> */
    private $values;

    private function __construct()
    {
        $this->values = [];
    }

    public static function empty(): SessionDocument
    {
        return new self();
    }

    public function add(SessionDocumentAttribute $attribute): void
    {
        $this->values[$attribute->name()] = $attribute;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->values);
    }

    public function get(string $name): SessionDocumentAttribute
    {
        if (!array_key_exists($name, $this->values)) {
            throw SessionDocumentAttributeNotFoundException::withName($name);
        }

        return $this->values[$name];
    }
}

<?php


namespace Apie\ValueObjects;

use Apie\OpenapiSchema\Contract\SchemaContract;
use Apie\ValueObjects\Exceptions\InvalidValueForValueObjectException;
use LogicException;
use ReflectionClass;

trait StringTrait
{
    private $value;

    final public static function fromNative($value)
    {
        return new self((string) $value);
    }

    final public function __construct(string $value)
    {
        if (!$this->validValue($value)) {
            throw new InvalidValueForValueObjectException($value, __CLASS__);
        }
        $this->value = $this->sanitizeValue($value);
    }

    abstract protected function validValue(string $value): bool;

    abstract protected function sanitizeValue(string $value): string;

    final public function toNative()
    {
        return $this->value;
    }

    final public function __toString(): string
    {
        return $this->toNative();
    }

    final static public function toSchema(): SchemaContract
    {
        if (!class_exists(SchemaContract::class)) {
            throw new LogicException('To use toSchema(), you require to include apie/openapi-schema');
        }
        $refl = new ReflectionClass(__CLASS__);
        return SchemaFactory::createStringSchema(
            strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $refl->getShortName()))
        );
    }
}
<?php

declare(strict_types=1);

namespace Courage;

use ArrayAccess;
use \ArrayIterator;
use Courage\Exceptions\InvalidArgumentException;
use IteratorAggregate;
use ReturnTypeWillChange;
use RuntimeException;

/**
 * @template T
 * @implements IteratorAggregate<int|string, T>
 * @implements ArrayAccess<int|string, T>
 * @phpstan-consistent-constructor
 */
class CoList implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var T[] $value
     */
    protected array $value;

    /**
     * @param T[] $value
     */
    public function __construct(array $value)
    {
        $this->value = $value;
    }

    /**
     * @return T[]
     */
    public function toArray(): array
    {
        return $this->value;
    }

    /**
     * Applies the callback to the elements.
     *
     * @template X
     * @param callable(T): X $callable
     * @return self<X>
     */
    public function map(callable $callable): self
    {
        return new self(
            array_values(
                array_map($callable, $this->value)
            )
        );
    }

    /**
     * @param callable(T): bool $closure
     * @return static
     */
    public function filter(callable $closure): static
    {
        return new static(
            array_values(
                array_filter(
                    $this->value, $closure
                )
            )
        );
    }

    /**
     * @param callable(T): bool $closure
     * @return bool
     */
    public function some(callable $closure): bool
    {
        return array_reduce($this->value, function ($callback, $item) use ($closure) {
            return $callback || $closure($item);
        }, false);
    }

    /**
     * @param T[] $add
     */
    public function merge(array $add): void
    {
        $this->value = array_merge($this->value, $add);
    }

    /**
     * @return void
     */
    public function unique(): void
    {
        $this->value = array_values(array_unique($this->value, SORT_REGULAR));
    }

    /**
     * Returns length of list.
     * @return int<0, max>
     */
    public function length(): int
    {
        return count($this->value);
    }

    /**
     * Returns length of list.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->length();
    }

    /**
     * Whether a offset exists
     *
     * @param int|string $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->value[$offset]);
    }

    /**
     * Retrieve for value of offset
     *
     * @param mixed $offset
     * @return T
     */
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $offset)
    {
        if (!isset($this->value[$offset])) {
            throw new InvalidArgumentException('Does not exist offset was specified.');
        }

        return $this->value[$offset];
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (
            is_null($offset) ||
            is_array($offset) ||
            $offset === ''
        ) {
            throw new RuntimeException('Invalid offset.');
        }

        $this->value[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param int|string $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->value[$offset]);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->value);
    }

    public function isEmpty(): bool
    {
        return count($this->value) === 0;
    }
}

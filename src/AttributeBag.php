<?php

namespace Magic;

use Closure;
use function ucfirst;

class AttributeBag
{
    private static array $registeredFunctions = [];

    private array $recordings = [];

    public function __construct(private array $attributes)
    {
    }

    public static function registerFunction(string $name, callable $function)
    {
        static::$registeredFunctions[$name] = $function;
    }

    public function __get(string $name)
    {
        $mutator = $this->getMutator('get'.ucfirst($name).'Attribute');

        return $mutator($this->attributes[$name] ?? null);
    }

    private function getMutator($mutator)
    {
        if (!$this->hasMutator($mutator)) {
            return fn ($attribute) => $attribute;
        }

        return fn($attribute) => $this->{$mutator}($attribute);
    }

    public function __set(string $name, $value): void
    {
        if (! array_key_exists($name, $this->attributes)) {
            return;
        }

        $mutator = $this->getMutator('set' . ucfirst($name) . 'Attribute');

        $this->attributes[$name] = $mutator($value);
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->attributes)
            || $this->hasMutator('get'.ucfirst($name).'Attribute');
    }

    private function hasMutator($mutator): bool
    {
        return method_exists($this, $mutator);
    }

    public function __unset(string $name): void
    {
        unset($this->attributes[$name]);
    }

    public function record(string $name, callable $callback)
    {
        $this->recordings[$name] = Closure::fromCallable($callback)->bindTo($this);
    }

    public function __call(string $name, array $arguments)
    {
        if (! array_key_exists($name, $this->recordings)) {
            return;
        }

        return $this->recordings[$name](...$arguments);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        if (! array_key_exists($name, static::$registeredFunctions)) {
            return;
        }

        return static::$registeredFunctions[$name](...$arguments);
    }

    public function __serialize(): array
    {
        return $this->attributes;
    }

    public function __unserialize(array $data): void
    {
        $this->attributes = $data;
    }

    public function __debugInfo(): ?array
    {
        $mutators = $this->getAvailableMutators();

        $mutated = [];
        foreach($mutators as $attribute => $mutator) {
            $mutated[$attribute] = $this->$mutator($this->attributes[$attribute] ?? null);
        }

        return array_merge($this->attributes, $mutated);
    }

    private function getAvailableMutators()
    {
        $reflectedSelf = new \ReflectionClass($this);
        $methods = array_map(fn(\ReflectionMethod $method) => $method->getName(), $reflectedSelf->getMethods());

        $mutators = array_filter($methods, fn ($method) => !! preg_match('/get.*Attribute/', $method));

        return array_reduce($mutators, function ($allMutators, $method) {
            preg_match('/get(.*)Attribute/', $method, $matches);
            $allMutators[strtolower($matches[1])] = $method;

            return $allMutators;
        }, []);
    }
}

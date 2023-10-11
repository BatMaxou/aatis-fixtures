<?php

namespace Aatis\FixturesBundle\Service;

class ConstructorWarner
{
    private array $parameters = [];

    public function init(\ReflectionMethod $construct): void
    {
        foreach ($construct->getParameters() as $parameter) {
            $this->parameters[$parameter->getName()] = null;
        }
    }

    public function addParameter(string $key, mixed $parameter): void
    {
        $this->parameters[$key] = $parameter;
    }

    public function isComplete(): bool
    {
        foreach ($this->parameters as $parameter) {
            if (null === $parameter) {
                return false;
            }
        }

        return true;
    }

    public function getParameters(): array
    {
        return array_values($this->parameters);
    }

    public function refresh(): void
    {
        $this->parameters = [];
    }
}

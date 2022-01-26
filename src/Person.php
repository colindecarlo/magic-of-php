<?php

namespace Magic;

use Ramsey\Uuid\Uuid;

class Person
{
    private string $id;

    public function __construct(private string $name, private string $dateOfBirth)
    {
        $this->setId();
    }

    public function __get(string $name)
    {
        return $this->{$name};
    }

    public function __clone(): void
    {
        $this->setId();
    }

    private function setId(): void
    {
        $this->id = Uuid::uuid4()->toString();
    }
}

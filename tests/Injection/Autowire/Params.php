<?php
declare(strict_types=1);

namespace Tests\Injection\Autowire;

class Params
{
    private string $name = '';
    private string $surname = '';

    public function __construct(?string $name = null, ?string $surname = null)
    {
        if ($name !== null) {
            $this->name = $name;
        }
        if ($surname !== null) {
            $this->surname = $surname;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $surname
     * @return Params
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }
}
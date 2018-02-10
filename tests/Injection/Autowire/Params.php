<?php

namespace Injection\Autowire;


class Params
{
    public $name;
    public $surname;

    public function __construct($name = null, $surname = null)
    {
        $this->name = $name;
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Params
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
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
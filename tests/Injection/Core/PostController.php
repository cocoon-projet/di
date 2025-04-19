<?php
namespace Tests\Injection\Core;

class PostController implements ControllerInterface
{
    protected $params;
    protected $class;

    public function __construct ($params = '', $class = '')
    {
        $this->params = $params;
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getParamUn()
    {
        return $this->params;
    }

    /**
     * @return mixed
     */
    public function getParamDeux()
    {
        return $this->class;
    }

    public function render ()
    {
    
    }
}
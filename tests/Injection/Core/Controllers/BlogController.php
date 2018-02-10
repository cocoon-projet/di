<?php

namespace Injection\Core\Controllers;

use Injection\Core\itemController;

class BlogController
{
    /**
     * @var BlogModel
     */
    private $blog;
    /**
     * @var null
     */
    public $param;

    public function __construct(BlogModel $blog, $param = null)
    {
        $this->blog = $blog;
        $this->param = $param;
    }
    public function index()
    {
        $posts = $this->blog->All();
        return $posts;
    }

    public function getId($id)
    {
        return $this->blog->all()[$id];
    }

    public function item(ItemController $item, $append )
    {
        return $item->render() . $append;
    }
}
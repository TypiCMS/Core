<?php
namespace TypiCMS\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

abstract class BasePublicController extends BaseController
{

    protected $repository;

    public function __construct($repository = null)
    {
        $this->repository = $repository;
    }
}

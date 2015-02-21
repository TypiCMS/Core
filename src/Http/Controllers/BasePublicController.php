<?php
namespace TypiCMS\Http\Controllers;

use Illuminate\Routing\Controller;

abstract class BasePublicController extends Controller
{

    protected $repository;

    public function __construct($repository = null)
    {
        $this->repository = $repository;
    }
}

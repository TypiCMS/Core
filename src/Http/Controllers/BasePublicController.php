<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;

abstract class BasePublicController extends Controller
{
    protected $repository;

    public function __construct($repository = null)
    {
        $this->middleware('web');
        $this->middleware('public');
        $this->repository = $repository;
    }
}

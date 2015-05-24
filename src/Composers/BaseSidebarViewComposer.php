<?php namespace TypiCMS\Modules\Core\Composers;

// use TypiCMS\Modules\Users\Repositories\UserInterface as Authentication;
use Illuminate\Contracts\Auth\Guard as Auth;

abstract class BaseSidebarViewComposer
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param Authentication $auth
     */
    public function __construct(Auth $auth)
    {
        $this->user = $auth->user();
    }
}

<?php namespace TypiCMS\Modules\Core\Composers;

use TypiCMS\Modules\Users\Repositories\UserInterface as Auth;

abstract class BaseSidebarViewComposer
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
}

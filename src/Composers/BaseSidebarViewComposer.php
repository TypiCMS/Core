<?php namespace TypiCMS\Modules\Core\Composers;

use TypiCMS\Modules\Users\Repositories\UserInterface as Authentication;

abstract class BaseSidebarViewComposer
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param Authentication $auth
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }
}

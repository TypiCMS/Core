<?php namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Contracts\Auth\Guard as Auth;

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
        $this->user = $auth->user();
    }
}

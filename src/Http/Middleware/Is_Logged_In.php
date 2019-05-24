<?php

namespace Snap\Http\Middleware;

use Snap\Http\Request;
use Snap\Hookables\Middleware;
use Snap\Services\Response;

/**
 * Some basic middleware.
 */
class Is_Logged_In extends Middleware
{
    /**
     * Check if the current user is logged in, and perform the redirect if not.
     *
     * Example: is_logged_in|404
     *
     * @param  \Snap\Http\Request $request  The current request.
     * @param  string|null        $redirect The middleware argument. How to redirect this request.
     * @return boolean
     */
    public function handle(Request $request, $redirect = null)
    {
        if (\is_user_logged_in() === true) {
            return true;
        }

        if ($redirect === 'login') {
            Response::redirectToLogin();
        }

        if ($redirect === 'admin') {
            Response::redirectToAdmin();
        }

        if ($redirect !== null) {
            Response::redirect($redirect);
        }

        Response::set404();

        return false;
    }
}

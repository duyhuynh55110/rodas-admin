<?php

namespace App\Modules\Admin\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Modules\Admin\Http\Controllers\BaseController;
use App\Modules\Admin\Services\UserService;

/**
 * Login controller
 */
class LoginController extends BaseController
{
    /**
     * @var User Service
     */
    private $userService;

    /**
     * __construct
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display login form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return View
     */
    public function login(Request $request)
    {
        // user is logging then redirect
        if (authAdmin()->check()) {
            $request->session()->regenerate();

            return redirect()->intended(routeAdmin(config('admin.auth.redirect_to')));
        }

        // user is not logging then display login form
        return view('Admin::login.form');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // authentication attempt
        if ($this->userService->authenticate($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(routeAdmin(config('admin.auth.redirect_to')));
        }

        // back with error
        return back()->withErrors(
            [
                'error' => __('Invalid username or password.'),
            ]
        )->withInput(
            $request->except('password')
        );
    }
}
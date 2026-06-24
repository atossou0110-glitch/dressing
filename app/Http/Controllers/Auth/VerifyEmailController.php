<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($this->redirectPath($request->user()->is_admin, true));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended($this->redirectPath($request->user()->is_admin, true));
    }

    /**
     * Resolve the post-verification redirect path.
     */
    private function redirectPath(bool $isAdmin, bool $verified = false): string
    {
        $route = $isAdmin ? 'dashboard' : 'catalog.index';

        return route($route, absolute: false).($verified ? '?verified=1' : '');
    }
}

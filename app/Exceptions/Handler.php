<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        TokenMismatchException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // When a CSRF token is expired / missing, redirect back with a friendly
        // message instead of showing the raw 419 "Page Expired" error.
        $this->renderable(function (TokenMismatchException $e, $request) {
            if ($request->is('logout')) {
                // Session already expired — just redirect to login.
                return redirect()->route('login')
                    ->withErrors(['session' => 'Your session has expired. Please log in again.']);
            }

            return back()
                ->withInput($request->except($this->dontFlash))
                ->withErrors(['session' => 'Your session expired. Please try again.']);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

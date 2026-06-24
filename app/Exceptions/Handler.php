<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontReport = [
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Database\QueryException::class,
        \Illuminate\Auth\AuthenticationException::class,
    ];

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (\Exception $e) {
            Log::error('Application Error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user' => Auth::id() ?? 'guest',
                'ip' => request()->ip(),
            ]);
        });

        $this->renderable(function (\Exception $e, Request $request) {
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return redirect()->guest(route('login'));
            }

            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return redirect()
                    ->back()
                    ->withInput($request->except($this->dontFlash))
                    ->withErrors($e->errors());
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Internal Server Error',
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ], 500);
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();

                return response()->view('errors.minimal', [
                    'message' => $e->getMessage() ?: 'The requested action is not available.',
                    'code' => $statusCode,
                    'title' => $statusCode === 403 ? 'Forbidden' : 'Error',
                ], $statusCode);
            }

            // Share $errors variable with the view
            if (config('app.debug')) {
                $message = $e->getMessage();
            } else {
                $message = 'An unexpected error occurred. Please try again later.';
            }

            // Return a simple error response without extending the app layout
            return response()->view('errors.minimal', [
                'message' => $message,
                'code' => 500,
                'title' => 'Server Error',
            ], 500);
        });
    }

    /**
     * Redirect unauthenticated users to login page instead of showing error.
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        return redirect()->guest(route('login'));
    }
}

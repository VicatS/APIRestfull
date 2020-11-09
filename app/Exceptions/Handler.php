<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Dotenv\Exception\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Para mostrar en formato json errores de validacion
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        // para mostrar la instancia no encontrada
        if ($exception instanceof ModelNotFoundException) {
            $modelo = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse(
                "No existe niguna instancia de {$modelo} con el id especificado",
                404);
        }

        // Para mostrar erroes de autenticacion
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        // Para mostrar error de autorizacion de permisos
        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse(
                'No posee permisos para ejecutar esta accion',
                403
            );
        }

        // Para manejar paginas o rutas no encontradas
        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse(
                'No se encontro la URL especificada',
                404
            );
        }

        // Para controllar el metodo de peticion correcto(GET, POST)
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse(
                'El metodo especificado en la peticion no es valido',
                405
            );
        }

        // Para manejar distintos errores Http de manera general
        if ($exception instanceof HttpException) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        }

        // Para manejar distintos errores de eliminar datos que tengas relaciones en otras tablas
        if ($exception instanceof QueryException) {
            $codigo = $exception->errorInfo[1];

            if ($codigo == 1451) {
                return $this->errorResponse(
                    'No se puede eliminar de forma permanente el recurso porque esta relacionado con algun otro',
                    409
                );
            }
        }

        // falta de token crsf()
        if ($exception instanceof TokenMismatchException) {
            return redirect()->back()->withInput($request->input());
        }

        // Fallas inesperadas
        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        return $this->errorResponse(
            'Falla inesperada. Intente luego',
            500
        );
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->isFronted($request)) {
            return redirect()->guest('login');
        }

        return $this->errorResponse(
            'No autenticado.',
            401);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param \Throwable $exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    protected function convertValidationExceptionToResponse(Throwable $exception, $request)
    {
        $errors = $exception->validator->errors()->getMessages();

        if ($this->isFronted($request)) {
            return $request->ajax() ? response()->json($errors, 422) : redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($errors);
        }

        return $this->errorResponse($errors, 422);
    }

    private function isFronted($request) {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //validar la excepcion del tken 
        'http://apirest-laravel.com/registroCliente',
        'http://apirest-laravel.com/cursos',
        'http://apirest-laravel.com/cursos/*'
    ];
}

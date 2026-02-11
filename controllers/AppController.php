<?php

namespace Controllers;

use MVC\Router;

class AppController {
    public static function index(Router $router)
    {
        // Redirigir al dashboard
        header('Location: /comodin_motors/dashboard');
        exit;
    }

}
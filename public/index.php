<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DeveloperAnnur\Belajar\PHP\MVC\App\Router;
use DeveloperAnnur\Belajar\PHP\MVC\Config\Database;
use DeveloperAnnur\Belajar\PHP\MVC\Controller\HomeController;
use DeveloperAnnur\Belajar\PHP\MVC\Controller\UserController;
use DeveloperAnnur\Belajar\PHP\MVC\Middleware\MustLoginMiddleware;
use DeveloperAnnur\Belajar\PHP\MVC\Middleware\MustNotLoginMiddleware;

Database::getConnection('prod');

// Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);

Router::run();
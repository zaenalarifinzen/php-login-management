<?php

namespace DeveloperAnnur\Belajar\PHP\MVC\Controller;

use DeveloperAnnur\Belajar\PHP\MVC\App\View;
use DeveloperAnnur\Belajar\PHP\MVC\Config\Database;
use DeveloperAnnur\Belajar\PHP\MVC\Exception\ValidationException;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserLoginRequest;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserRegisterRequest;
use DeveloperAnnur\Belajar\PHP\MVC\Repository\SessionRepository;
use DeveloperAnnur\Belajar\PHP\MVC\Repository\UserRepository;
use DeveloperAnnur\Belajar\PHP\MVC\Service\SessionService;
use DeveloperAnnur\Belajar\PHP\MVC\Service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct() {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    // untuk menampilkan form registrasi
    public function register()
    {
        View::render('User/register',[
            "title" => "Register New User",
        ]);
    }

    // aksi dari register
    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect('/users/login'); // redirect to /users/login
        } catch (ValidationException $exception) {
            View::render('User/register', [
                "title" => "Register New User",
                "error" => $exception->getMessage()
            ]); // kembalikan ke form register & tampilkan error
        }
    }

    public function login()
    {
        View::render('User/login', [
            "title" => "Login user"
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->id);

            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/login', [
                "title" => "Login user",
                "error" => $exception->getMessage()
            ]); // kembalikan ke form login & tampilkan error
        }
    }

    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect("/");
    }
}
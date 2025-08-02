<?php

namespace DeveloperAnnur\Belajar\PHP\MVC\Controller;

use DeveloperAnnur\Belajar\PHP\MVC\App\View;

class HomeController
{

    function index(): void
    {
        $model = [
            "title" => "PHP MVC Course",
            "content" => "Selamat Belajar PHP MVC dari PZN Course"
        ];
        
        View::render('Home/index', $model);
    }

    function hello(): void
    {
        echo "HomeController.hello()";
    }

    function world(): void
    {
        echo "HomeController.world()";
    }

    function about(): void
    {
        echo "Author : Zaenal Arifin";
    }

    function login() : void
    {
        $request = [
            "username" => $_POST['username'],
            "password" => $_POST['password'],
        ];

        $user = [

        ];

        $response = [
            "message" => "Login sukses!"
        ];
    }
}

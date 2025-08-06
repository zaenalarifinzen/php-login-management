<?php

namespace DeveloperAnnur\Belajar\PHP\MVC\Controller;

use DeveloperAnnur\Belajar\PHP\MVC\Config\Database;
use DeveloperAnnur\Belajar\PHP\MVC\Domain\Session;
use DeveloperAnnur\Belajar\PHP\MVC\Domain\User;
use DeveloperAnnur\Belajar\PHP\MVC\Repository\SessionRepository;
use DeveloperAnnur\Belajar\PHP\MVC\Repository\UserRepository;
use DeveloperAnnur\Belajar\PHP\MVC\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    function setUp() : void
    {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    function testGuest()
    {
        $this->homeController->index();

        $this->expectOutputRegex("[Login Management]");      
    }

    function testUserLogin()
    {
        $user = new User();
        $user->id = "zaenal";
        $user->name = "Zaenal";
        $user->password = "rahasia";
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();

        $this->expectOutputRegex("[Hello Zaenal]");
    }
}
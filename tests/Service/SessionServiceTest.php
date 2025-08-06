<?php

namespace DeveloperAnnur\Belajar\PHP\MVC\Service;

use DeveloperAnnur\Belajar\PHP\MVC\Config\Database;
use DeveloperAnnur\Belajar\PHP\MVC\Domain\Session;
use DeveloperAnnur\Belajar\PHP\MVC\Repository\SessionRepository;
use DeveloperAnnur\Belajar\PHP\MVC\Repository\UserRepository;
use DeveloperAnnur\Belajar\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

function setcookie(string $name, string $value){
    echo "$name: $value";
};

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function setUp() : void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "zaenal";
        $user->name = "Zaenal";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $user = 

        $session = $this->sessionService->create("zaenal");

        $this->expectOutputRegex("[X-NF-SESSION: $session->id]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals('zaenal', $result->userId);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "zaenal";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-NF-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "zaenal";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }
}
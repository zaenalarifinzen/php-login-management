<?php

namespace DeveloperAnnur\Belajar\PHP\MVC\Middleware {

    require_once __DIR__ . "/../Helper/helper.php";

    use DeveloperAnnur\Belajar\PHP\MVC\Config\Database;
    use DeveloperAnnur\Belajar\PHP\MVC\Domain\Session;
    use DeveloperAnnur\Belajar\PHP\MVC\Domain\User;
    use DeveloperAnnur\Belajar\PHP\MVC\Repository\SessionRepository;
    use DeveloperAnnur\Belajar\PHP\MVC\Repository\UserRepository;
    use DeveloperAnnur\Belajar\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustNotLoginMiddlewareTest extends TestCase
    {
        private MustNotLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp() : void
        {
            $this->middleware = new MustNotLoginMiddleware();
            putenv("mode=test");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->middleware->before();

            $this->expectOutputString("");
        }

        public function testBeforeLoginUser()
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

            $this->middleware->before();

            $this->expectOutputRegex("[Location: /]");
        }
    }
}

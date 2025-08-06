<?php

namespace DeveloperAnnur\Belajar\PHP\MVC\Controller {

    require_once __DIR__ . "/../Helper/helper.php";

    use DeveloperAnnur\Belajar\PHP\MVC\Config\Database;
    use DeveloperAnnur\Belajar\PHP\MVC\Domain\Session;
    use DeveloperAnnur\Belajar\PHP\MVC\Repository\UserRepository;
    use DeveloperAnnur\Belajar\PHP\MVC\Domain\User;
    use DeveloperAnnur\Belajar\PHP\MVC\Repository\SessionRepository;
    use DeveloperAnnur\Belajar\PHP\MVC\Service\SessionService;
    use DeveloperAnnur\Belajar\PHP\MVC\Service\UserService;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;


        protected function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testRegister()
        {
            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Register New User]");
        }

        public function testPostRegisterSucces()
        {
            $_POST['id'] = 'zaenal';
            $_POST['name'] = 'Zaenal';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();
            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testPostRegisterValidationError()
        {
            $_POST['id'] = ''; // id kosong
            $_POST['name'] = 'Zaenal';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Register New User]");
            $this->expectOutputRegex("[Id, Name, Password cannot blank]");
        }

        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = 'rahasia';

            $this->userRepository->save($user);

            $_POST['id'] = 'zaenal';
            $_POST['name'] = 'Zaenal';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Register New User]");
            $this->expectOutputRegex("[User already exist]");
        }

        public function testLogin()
        {
            $this->userController->login();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Password]");
        }

        public function testLoginSucces()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'zaenal';
            $_POST['password'] = 'rahasia';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Location: /]");           
            $this->expectOutputRegex("[X-NF-SESSION: ]");           
        }

        public function testLoginValidationError()
        {
            $_POST['id'] = '';
            $_POST['password'] = '';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or Password cannot blank]");
        }

        public function testLoginUserNotFound()
        {
            $_POST['id'] = 'notfound';
            $_POST['password'] = 'notfound';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'zaenal';
            $_POST['password'] = 'salah';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLogout()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-NF-SESSION: ]");
        }

        public function testUpdateProfile()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[zaenal]");
            $this->expectOutputRegex("[name]");
            $this->expectOutputRegex("[Zaenal]");
        }

        public function testPostUpdateProfileSucces()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = 'John';
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById('zaenal');
            self::assertEquals("John", $result->name);
        }

        public function testPostUpdateProfileValidationError()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = '';
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[zaenal]");
            $this->expectOutputRegex("[name]");
            $this->expectOutputRegex("[Name can not blank]");
        }

        public function testUpdatePassword()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[zaenal]");
        }

        public function testPostpdatePasswordSucces()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = 'rahasia';
            $_POST['newPassword'] = 'admin123';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);
            self::assertTrue(password_verify("admin123", $result->password));
        }

        public function testPostpdatePasswordValidationError()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = '';
            $_POST['newPassword'] = '';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[zaenal]");
            $this->expectOutputRegex("[Password can not blank]");
        }

        public function testPostpdatePasswordWrongOldPassword()
        {
            $user = new User();
            $user->id = 'zaenal';
            $user->name = 'Zaenal';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = 'salah';
            $_POST['newPassword'] = 'baru';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[zaenal]");
            $this->expectOutputRegex("[Old password is wrong]");
        }
    }
}

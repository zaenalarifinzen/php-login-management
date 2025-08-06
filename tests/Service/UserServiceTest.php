<?php

namespace DeveloperAnnur\Belajar\PHP\MVC\Service;

use DeveloperAnnur\Belajar\PHP\MVC\Config\Database;
use DeveloperAnnur\Belajar\PHP\MVC\Domain\User;
use DeveloperAnnur\Belajar\PHP\MVC\Exception\ValidationException;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserLoginRequest;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserRegisterRequest;
use DeveloperAnnur\Belajar\PHP\MVC\Repository\SessionRepository;
use DeveloperAnnur\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertSame;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository($connection);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testRegisterSucces()
    {
        $request = new UserRegisterRequest();
        $request->id = "zaenal";
        $request->name = "Zaenal";
        $request->password = "rahasia";

        $response = $this->userService->register($request);

        assertEquals($request->id, $response->user->id);
        assertEquals($request->name, $response->user->name);
        assertNotEquals($request->password, $response->user->password);

        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        // daftar user baru
        $user = new User();
        $user->id = "zaenal";
        $user->name = "Zaenal";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        // daftar user baru menggunakan data yang sudah terdaftar
        $request = new UserRegisterRequest();
        $request->id = "zaenal";
        $request->name = "Zaenal";
        $request->password = "rahasia";

        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = 'test';
        $request->password = "rahasia";

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "zaenal";
        $user->name = "Zaenal";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = 'test';
        $request->password = "salah";

        $this->userService->login($request);
    }

    public function testLoginSucces()
    {
        $user = new User();
        $user->id = "zaenal";
        $user->name = "Zaenal";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = 'test';
        $request->password = "rahasia";

        $response = $this->userService->login($request);
        self::assertEquals($request->id, $response->user->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testUpdateSucces()
    {
        $user = new User();
        $user->id = "zaenal";
        $user->name = "Zaenal";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->id = "zaenal";
        $request->name = "Zaenal Arifin";

        $this->userService->updateProfile($request);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($request->name, $result->name);
    }

    public function testUpdateValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";

        $this->userService->updateProfile($request);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);
        
        $request = new UserProfileUpdateRequest();
        $request->id = "zaenal";
        $request->name = "Zaenal Arifin";

        $this->userService->updateProfile($request);
    }

    public function testUpdatePasswordSucces()
    {
        $user = new User();
        $user->id = "zaenal";
        $user->name = "Zaenal";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = "zaenal";
        $request->oldPassword = "rahasia";
        $request->newPassword = "baru";

        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);
        self::assertTrue(password_verify($request->newPassword, $result->password));
    }

    public function testUpdatePasswordValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "zaenal";
        $request->oldPassword = "";
        $request->newPassword = "";

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordWrongOldPassword()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "zaenal";
        $request->oldPassword = "salah";
        $request->newPassword = "baru";

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordNotFound()
    {
        $this->expectException(ValidationException::class);
        
        $request = new UserPasswordUpdateRequest();
        $request->id = "zaenal";
        $request->oldPassword = "rahasia";
        $request->newPassword = "baru";

        $this->userService->updatePassword($request);
    }
}
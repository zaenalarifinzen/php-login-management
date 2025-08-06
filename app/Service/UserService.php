<?php

namespace DeveloperAnnur\Belajar\PHP\MVC\Service;

use DeveloperAnnur\Belajar\PHP\MVC\Config\Database;
use DeveloperAnnur\Belajar\PHP\MVC\Domain\User;
use DeveloperAnnur\Belajar\PHP\MVC\Exception\ValidationException;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserLoginRequest;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserLoginResponse;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserPasswordUpdateResponse;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserProfileUpdateResponse;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserRegisterRequest;
use DeveloperAnnur\Belajar\PHP\MVC\Model\UserRegisterResponse;
use DeveloperAnnur\Belajar\PHP\MVC\Repository\UserRepository;

use function PHPUnit\Framework\throwException;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    // REGISTER NEW USER
    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        
        // cek data di parameter request apakah ada yang kosong atau tidak
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();

            // cek apakah user sudah teregistrasi sebelumnya atau belum
            $user = $this->userRepository->findById($request->id);
            if ($user != null) {
                throw new ValidationException('User already exist');
            }

            // simpan user baru
            $user = new User;
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT); // simpan password dengan hashing

            $this->userRepository->save($user);

            // respon
            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;

        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    // validasi parameter request
    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null ||
        trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, Name, Password cannot blank");
        }
    }

    // USER LOGIN
    public function login(UserLoginRequest $request) : UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException('Id or password is wrong');
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException('Id or password is wrong');
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null ||
        trim($request->id) == "" || trim($request->password) == "") {
            throw new ValidationException("Id or Password cannot blank");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request) : UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User is not found");
            }

            $user->name = $request->name;
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }

    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if ($request->id == null || $request->name == null ||
        trim($request->id) == "" || trim($request->name) == "") {
            throw new ValidationException("Name can not blank");
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request) : UserPasswordUpdateResponse
    {
        $this->validateUserPasswordUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException('User is not found');
            }

            if (!password_verify($request->oldPassword, $user->password)) {
                throw new ValidationException('Old password is wrong');
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserPasswordUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if ($request->id == null || $request->oldPassword == null || $request->newPassword == null ||
        trim($request->id) == "" || trim($request->oldPassword) == "" || trim($request->newPassword) == "") {
            throw new ValidationException("Password can not blank");
        }
    }
}
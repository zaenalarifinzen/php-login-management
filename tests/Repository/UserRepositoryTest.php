<?php

namespace DeveloperAnnur\Belajar\PHP\MVC\Repository;

use PHPUnit\Framework\TestCase;
use DeveloperAnnur\Belajar\PHP\MVC\Config\Database;
use DeveloperAnnur\Belajar\PHP\MVC\Domain\User;
use DeveloperAnnur\Belajar\PHP\MVC\Service\SessionService;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp() : void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testSaveSucces()
    {
        $user = new User();
        $user->id = 'zaenal';
        $user->name = 'Zaenal';
        $user->password = 'rahasia';

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        assertEquals($user->id, $result->id);
        assertEquals($user->name, $result->name);
        assertEquals($user->password, $result->password);
    }

    public function testFindByIdNotFound()
    {
        $user = $this->userRepository->findById('notfound');
        assertNull($user);
    }

    public function testUpdate()
    {
        $user = new User();
        $user->id = 'zaenal';
        $user->name = 'Zaenal';
        $user->password = 'rahasia';

        $this->userRepository->save($user);

        $user->name = 'Arifin';
        $this->userRepository->update($user);

        $result = $this->userRepository->findById($user->id);

        assertEquals($user->id, $result->id);
        assertEquals($user->name, $result->name);
        assertEquals($user->password, $result->password);
    }
}
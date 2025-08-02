<?php

namespace DeveloperAnnur\Belajar\PHP\MVC\s\Config;

use DeveloperAnnur\Belajar\PHP\MVC\Config\Database;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertNotNull;

class DatabaseTest extends TestCase
{
    public function testGetConnection()
    {
        $connection = Database::getConnection();
        assertNotNull($connection);
    }

    public function testGetConnectionSingleton()
    {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        self::assertSame($connection1, $connection2);
    }
}
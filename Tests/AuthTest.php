<?php

require "bootstrap.php";

use App\Auth\Auth;
use App\DB\DatabaseConnector;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertSame;

class AuthTest extends TestCase {

    private $db;

    public function setUp(): void {
        $this->db = new DatabaseConnector();
    }

    /**
     * @test
     * @covers App\Auth\Auth
     * @covers App\DB\DatabaseConnector
     * @covers App\Controllers\UsersController
     * @dataProvider provideLoginData
     */
    public function UserLogin(array $credentials, $expected) {
        $user = Auth::AuthenticateUser($this->db, $credentials);
        assertSame($expected, $user);
    }

    public function provideLoginData() {
        return [
            'correctLogin' => [
                'credentials' => [
                    'login' => 'eduardo',
                    'token' => 't0k3n',
                    'host' => 'dev.localhost'
                ],
                'expected' => array("id" => 1, "role" => 1),
            ],
            'incorrectLogin' => [
                'credentials' => [
                    'login' => 'eduardo',
                    'token' => 't0k3',
                    'host' => 'dev.localhost'
                ],
                'expected' => null,
            ]
        ];
    }
}
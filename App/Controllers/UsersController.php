<?php

namespace App\Controllers;

use App\DB\DatabaseConnector;

class UsersController {

    private $db;
    private $login;
    private $token;
    private $host;

    public function __construct(DatabaseConnector $db, array $credentials) {
        $this->db = $db;
        $this->login = $credentials['login'];
        $this->token = $credentials['token'];
        $this->host = $credentials['host'];
    }

    public function logUser() {
        $sql = "SELECT id, role "
            . "FROM users "
            . "WHERE login = :login "
            . "AND token = :token "
            . "AND origin = :origin "
            . "AND active = 1 "
            . "LIMIT 1";

        $command = $this->db->getConnection()->prepare($sql);
        $command->bindValue(":login", $this->login);
        $command->bindValue(":token", $this->token);
        $command->bindValue(":origin", $this->host);
        $command->execute();
        $result = $command->fetch();

        return empty($result) ? null : ["id" => $result['id'], "role" => $result['role']];
    }
}
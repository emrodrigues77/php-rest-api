<?php

namespace App\Models;

use App\DB\DatabaseConnector;
use App\Traits\EntitiesTrait;

class Author {

    use EntitiesTrait;

    private $db;
    private $table = "authors";

    public function __construct(DatabaseConnector $db) {
        $this->db = $db;
    }

    public function store($data) {

        if (!$this->validate($data)) {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = "Invalid data sent";
            return $response;
        }

        $sql = "INSERT INTO $this->table (name) VALUES (:name)";
        $command = $this->db->getConnection()->prepare($sql);
        $command->bindValue(":name", $data['name']);

        return $this->storeEntity($command);
    }

    public function update($id, $data) {

        if (!$this->entityExists($id, $this->table)) {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = "Author not found";
            return $response;
        }

        if (!$this->validate($data) || !is_int((int) $id)) {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = "Invalid data sent";
            return $response;
        }

        $sql = "UPDATE $this->table SET name = :name WHERE id = :id";
        $command = $this->db->getConnection()->prepare($sql);
        $command->bindValue(":name", $data['name']);
        $command->bindValue(":id", $id);

        return $this->updateEntity($command);
    }

    public function delete($id) {

        if (!is_int((int) $id)) {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = "Invalid data sent";
            return $response;
        }

        if (!$this->entityExists($id, $this->table)) {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = "Author not found";
            return $response;
        }

        if (!$this->canEntityBeDeleted($id, "books", "author")) {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = "Author cannot be deleted";
            return $response;
        }

        if ($this->destroy($id, $this->table)) {
            $response['status_code_header'] = 'HTTP/1.1 200 Ok';
            $response['body'] = "Author successfully deleted";
            return $response;
        } else {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = "Author cannot be deleted";
            return $response;
        }
    }

    private function validate($data) {

        if (!isset($data['name']) || empty($data['name'])) {
            return false;
        }

        return true;
    }
}
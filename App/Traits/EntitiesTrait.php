<?php

namespace App\Traits;

use App\DB\DatabaseConnector;

trait EntitiesTrait {

    private $db;

    public function canEntityBeDeleted($id, string $table, string $field) {
        $this->db = new DatabaseConnector();
        $sql = "SELECT COUNT(id) AS value FROM $table WHERE $field = :id";
        $command = $this->db->getConnection()->prepare($sql);
        $command->bindValue(":id", $id);
        $command->execute();
        $result = $command->fetch();
        return empty($result) ? true : (int) $result['value'] === 0;
    }

    public function findAll(array $fields, string $table, string $order) {
        $this->db = new DatabaseConnector();
        $sql = "SELECT " . implode(",", $fields)
            . " FROM $table "
            . "ORDER BY $order";
        $command = $this->db->getConnection()->prepare($sql);
        $command->execute();
        $result = $command->fetchAll();
        return $result;
    }

    public function find($id, array $fields, $table) {
        $this->db = new DatabaseConnector();
        $sql = "SELECT " . implode(",", $fields)
            . " FROM $table "
            . "WHERE id = :id";
        $command = $this->db->getConnection()->prepare($sql);
        $command->bindValue(":id", $id);
        $command->execute();
        $result = $command->fetch();
        return $result;
    }

    private function entityExists($id, $table) {
        $sql = "SELECT id FROM $table WHERE id = :id";
        $command = $this->db->getConnection()->prepare($sql);
        $command->bindValue(":id", $id);
        $command->execute();
        $result = $command->fetch();
        return empty($result) ? false : (int) $result['id'] === (int) $id;
    }

    public function destroy($id, $table) {
        $this->db = new DatabaseConnector();
        $sql = "DELETE "
            . "FROM $table "
            . "WHERE id = :id";
        $command = $this->db->getConnection()->prepare($sql);
        $command->bindValue(":id", $id);
        return $command->execute();
    }

    public function storeEntity($command) {
        try {
            $command->execute();
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = "Author added successfully";
        } catch (\Error | \Exception $ex) {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = "Error code #" . $ex->getCode();
        } finally {
            return $response;
        }
    }

    public function updateEntity($command) {
        try {
            $command->execute();
            $response['status_code_header'] = 'HTTP/1.1 200 Ok';
            $response['body'] = "Author updated successfully";
        } catch (\Error | \Exception $ex) {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = "Error code #" . $ex->getCode();
        } finally {
            return $response;
        }
    }
}
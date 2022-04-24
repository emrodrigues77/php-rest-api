<?php

namespace App\Core;

use App\Auth\Auth;
use App\DB\DatabaseConnector;

class Gateway {

    private $db;
    private $connection;
    private $uri;
    private $response;
    private $method;
    private $entity;
    private $user;
    private $data;
    public static $URI_ENTITIES_INDEX = 4;

    public function __construct(DatabaseConnector $db, $uri) {
        $this->db = $db;
        $this->connection = $this->db->getConnection();
        $this->uri = $uri;
        $this->entity = $this->uri[Gateway::$URI_ENTITIES_INDEX];
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->data = $this->prepareData();
        echo $this->entity . " " . $this->method;
    }

    public function processRequest() {

        try {
            // retrieving input data        
            $login = isset($_REQUEST['login']) ? filter_var($_REQUEST["login"], FILTER_DEFAULT) : null;
            $token = isset($_REQUEST['token']) ? filter_var($_REQUEST["token"], FILTER_DEFAULT) : null;

            // if one of the credentials isn't defined, set http code and body
            if ($login === null || $token === null) {
                $this->response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
                $this->response['body'] = "User's credentials not properly informed";
                $this->returnResponse();
                return;
            }

            // trying to authenticate user
            $credentials = ["login" => $login, "token" => $token, "host" => $_SERVER['HTTP_HOST']];
            $this->user = Auth::AuthenticateUser($this->db, $credentials);

            // if user's credentials are invalid, set http code and body
            if ($this->user === null) {
                $this->response['status_code_header'] = 'HTTP/1.1 403 Forbidden';
                $this->response['body'] = "User not allowed";
                $this->returnResponse();
                return;
            }

            // checking whether is a valid request method
            if (!$this->isValidEntityMethodRequest()) {
                $this->response['status_code_header'] = 'HTTP/1.1 403 Forbidden';
                $this->response['body'] = "User not allowed to perform this action";
                $this->returnResponse();
                return;
            }

            // user's credential are valid, call entity controller
            $this->response = $this->callEntityController($this->entity, [$this->db, $this->method, $this->uri, $this->data]);
            $this->returnResponse();
        } catch (\Error | \Exception $e) {
            echo var_dump($e);
        }
    }

    private function prepareData() {
        if ($this->method === "PUT") {
            parse_str(file_get_contents('php://input', 'r'), $data);
        } else {
            $data = $_REQUEST;
        }

        return $data;
    }

    private function returnResponse() {
        header($this->response['status_code_header']);

        if (isset($this->response['body'])) {
            echo $this->response['body'];
        }
    }

    private function isValidEntityMethodRequest() {
        $sql = "SELECT COUNT(em.id) AS value "
            . "FROM entities_methods em "
            . "INNER JOIN entities e ON em.entity = e.id "
            . "WHERE em.allowed = 1 "
            . "AND e.name = :entity "
            . "AND em.method = :method "
            . "AND em.role = :role";
        $command = $this->connection->prepare($sql);
        $command->bindValue(":entity", $this->entity);
        $command->bindValue(":method", $this->method);
        $command->bindValue(":role", $this->user['role']);
        $command->execute();
        $result = $command->fetch();
        return empty($result) ? false : $result['value'] > 0;
    }

    private function callEntityController(string $entity, $parameters) {
        $controllerClass = "App\Controllers\\" . ucfirst($entity) . "Controller";
        $reflector = new \ReflectionClass($controllerClass);
        $instance = $reflector->newInstanceArgs($parameters);
        return $instance->processRequest();
    }
}
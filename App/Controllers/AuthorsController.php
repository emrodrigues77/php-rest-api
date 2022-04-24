<?php

namespace App\Controllers;

use App\Auth\Auth;
use App\DB\DatabaseConnector;
use App\Controllers\ControllersInterface;
use App\Core\Gateway;
use App\Models\Author;

use function PHPUnit\Framework\isNan;

class AuthorsController implements ControllersInterface {

    private $db;
    private $method;
    private $uri;
    private $data;

    public function __construct(DatabaseConnector $db, string $method, $uri, array $data) {
        $this->db = $db;
        $this->method = $method;
        $this->uri = $uri;
        $this->data = $data;
    }

    public function processRequest() {
        if ($this->method === "GET" && !isset($this->uri[Gateway::$URI_ENTITIES_INDEX + 1])) {
            $response = $this->processAllAuthorsRequest();
        } elseif ($this->method === "GET" && isset($this->uri[Gateway::$URI_ENTITIES_INDEX + 1])) {
            $response = $this->processOneAuthorRequest();
        } elseif ($this->method === "POST") {
            $response = $this->processNewAuthorRequest();
        } elseif ($this->method === "PUT" && !empty($this->uri[Gateway::$URI_ENTITIES_INDEX + 1])) {
            $response = $this->processUpdateAuthorRequest($this->uri[Gateway::$URI_ENTITIES_INDEX + 1]);
        } elseif ($this->method === "DELETE" && !empty($this->uri[Gateway::$URI_ENTITIES_INDEX + 1])) {
            $response = $this->processDeleteAuthorRequest($this->uri[Gateway::$URI_ENTITIES_INDEX + 1]);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = "";
        }

        return $response;
    }

    private function processAllAuthorsRequest() {
        $authors = new Author($this->db);
        $body = $authors->findAll(["id", "name"], "authors", "name");
        $response['status_code_header'] = 'HTTP/1.1 200 Ok';
        $response['body'] = json_encode($body);
        return $response;
    }

    private function processOneAuthorRequest() {
        $id = $this->uri[Gateway::$URI_ENTITIES_INDEX + 1];

        if (!is_numeric($id)) {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = "Author code most be an integer";
            return $response;
        }

        $controller = new Author($this->db);
        $author = $controller->find($id, ['id', 'name'], 'authors');

        if (empty($author)) {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = "Author not found";
        } else {
            $response['status_code_header'] = 'HTTP/1.1 200 Ok';
            $response['body'] = json_encode($author);
        }

        return $response;
    }

    private function processNewAuthorRequest() {
        $author = new Author($this->db);
        $response = $author->store($this->data);
        return $response;
    }

    private function processUpdateAuthorRequest($id) {
        $author = new Author($this->db);
        $response = $author->update($id, $this->data);
        return $response;
    }

    private function processDeleteAuthorRequest($id) {
        $author = new Author($this->db);
        $response = $author->delete($id);
        return $response;
    }
}
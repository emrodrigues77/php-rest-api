<?php

use App\Controllers\AuthorsController;
use App\DB\DatabaseConnector;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertSame;

class AuthorsControllerTest extends TestCase {

    private $db;

    public function setUp(): void {
        $this->db = new DatabaseConnector();
    }

    /**
     * Undocumented function
     * @test
     * @covers use App\Controllers\AuthorsController;
     * @dataProvider provideRequestData  
     */
    public function processRequest($method, $uri, $data, $expectedHttpCode) {
        $uri = explode('/', $uri);
        $controller = new AuthorsController($this->db, $method, $uri, $data);
        $response = $controller->processRequest();
        assertSame($expectedHttpCode, $response['status_code_header'], "Not expected HTTP code");
    }

    public function provideRequestData() {
        return [
            'correctAllAuthorsRequest' => [
                "method" => "GET",
                "uri" => "dev.localhost/php-rest-api/api/v1/authors",
                "data" => [],
                "expectedHttpCode" => "HTTP/1.1 200 Ok",
            ],
            'correctOneAuthorRequest' => [
                "method" => "GET",
                "uri" => "dev.localhost/php-rest-api/api/v1/authors/1",
                "data" => [],
                "expectedHttpCode" => "HTTP/1.1 200 Ok",
            ],
            'nonExistentAuthorRequest' => [
                "method" => "GET",
                "uri" => "dev.localhost/php-rest-api/api/v1/authors/100",
                "data" => [],
                "expectedHttpCode" => "HTTP/1.1 404 Not Found",
            ],
            'malformedAuthorRequestId' => [
                "method" => "GET",
                "uri" => "dev.localhost/php-rest-api/api/v1/authors/ss",
                "data" => [],
                "expectedHttpCode" => "HTTP/1.1 400 Bad Request",
            ]
        ];
    }
}
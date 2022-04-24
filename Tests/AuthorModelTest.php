<?php

use App\DB\DatabaseConnector;
use App\Models\Author;
use PHPUnit\Framework\TestCase;

class AuthorModelTest extends TestCase {

    /**
     * @test
     * @covers App\Models\Author
     */
    function ModelCreationTest() {
        $db = $this->createMock(DatabaseConnector::class);
        $author = new Author($db);
        $this->assertInstanceOf(App\Models\Author::class, $author, 'Object is not an Author Object');
    }
}
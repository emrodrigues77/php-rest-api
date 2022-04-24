<?php

require "../bootstrap.php";

use App\DB\DatabaseConnector;
use App\Core\Gateway;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$db = new DatabaseConnector();
$uri = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
$uri = explode('/', $uri);
$gateway = new Gateway($db, $uri);
$gateway->processRequest();

// all of our endpoints start with /person
// everything else results in a 404 Not Found
/*if ($uri[1] !== 'person') {
    header("HTTP/1.1 404 Not Found");
    exit();
}*/

// the user id is, of course, optional and must be a number:
/*$userId = null;
if (isset($uri[2])) {
    $userId = (int) $uri[2];
}*/

// pass the request method and user ID to the PersonController and process the HTTP request:
//$controller = new PersonController($dbConnection, $requestMethod, $userId);
//$controller->processRequest();
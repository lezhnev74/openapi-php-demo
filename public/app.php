<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App;
$app->get('/generate', function (Request $request, Response $response, array $args) {

    $length   = $request->getQueryParams()['length'] ?? 20;
    $password = substr(bin2hex(random_bytes($length)), $length);

    $accept = count($request->getHeader('Accept')) ? $request->getHeader('Accept')[0] : null;

    switch ($accept) {
        case "text/plain":
            $response = $response->withHeader('Content-Type', 'text/plain');
            $response->getBody()->write($password);
            break;
        case "application/json":
        default:
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['password' => $password]));

    }


    return $response;
});

return $app;
<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
    return $this->renderer->render($response, 'index.phtml', $args);
});

// URL routes - to display pages
$app->get('/admin', 'adminControllerFactory');

// API routes
$app->post('/api/login', 'LoginController');
$app->post('/api/registerUser', 'RegisterUserControllerFactory');

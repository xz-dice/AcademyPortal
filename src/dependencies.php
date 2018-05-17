<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// db connection
$container['dbConnection'] = function () {
    $db = new PDO('mysql:host=192.168.20.20;dbname=academyPortal', 'root');
    return $db;
};

// factory to create register controller
$container['RegisterController'] = new \Portal\Factory\RegisterControllerFactory();

// factory to create user model
$container['UserModel'] = new \Portal\Factories\UserModelFactory();

//factory to create login controller
$container['LoginController'] = new \Portal\Factories\LoginControllerFactory();

//factory to create admin controller
$container['AdminController'] = new \Portal\Factories\AdminControllerFactory();

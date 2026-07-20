<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ClientsController::index');
$routes->post('/login', 'ClientsController::login');
$routes->get('/logout', 'ClientsController::logout');
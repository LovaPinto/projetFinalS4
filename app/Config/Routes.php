<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/', 'ClientAuthController::index');
$routes->post('/login', 'ClientAuthController::login');
$routes->get('/logout', 'ClientAuthController::logout');
<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ClientsController::index');
$routes->post('/login', 'ClientsController::login');
$routes->get('/logout', 'ClientsController::logout');

$routes->get('/dashboard', 'DashbordController::index');

$routes->get('/depot', 'DepotController::index');
$routes->post('/depot/executer', 'DepotController::executer');

$routes->get('/retrait', 'RetraitController::index');
$routes->post('/retrait/executer', 'RetraitController::executer');

$routes->get('/transfert', 'TransfertController::index');
$routes->post('/transfert/executer', 'TransfertController::executer');

$routes->get('/historique', 'HistoriqueController::index');

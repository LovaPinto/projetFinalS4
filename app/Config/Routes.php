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

$routes->get('/operator/login', 'OperatorController::login');
$routes->post('/operator/login', 'OperatorController::doLogin');
$routes->get('/operator/logout', 'OperatorController::doLogout');
$routes->get('/operator/dashboard', 'OperatorController::dashboard');
$routes->get('/operator/clients', 'OperatorController::clients');
$routes->get('/operator/prefixes', 'OperatorController::prefixes');
$routes->post('/operator/prefixes/add', 'OperatorController::prefixAdd');
$routes->get('/operator/prefixes/delete/(:num)', 'OperatorController::prefixDelete/$1');
$routes->get('/operator/operations', 'OperatorController::operations');
$routes->post('/operator/operations/add', 'OperatorController::operationAdd');
$routes->get('/operator/operations/toggle/(:num)', 'OperatorController::operationToggle/$1');
$routes->get('/operator/fees', 'OperatorController::fees');
$routes->post('/operator/fees/add', 'OperatorController::feeAdd');
$routes->get('/operator/fees/delete/(:num)', 'OperatorController::feeDelete/$1');
$routes->get('/operator/transactions', 'OperatorController::transactions');
$routes->get('/operator/gains', 'OperatorController::gains');

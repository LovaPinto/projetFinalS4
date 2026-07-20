<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// ── Auth opérateur ──
$routes->get('operator/login', 'OperatorController::loginForm');
$routes->post('operator/login', 'OperatorController::login');
$routes->get('operator/logout', 'OperatorController::logout');

// ── Espace opérateur (protégé) ──
$routes->group('operator', ['filter' => 'operatorAuth'], function ($routes) {
    $routes->get('dashboard', 'OperatorController::dashboard');

    $routes->get('prefixes', 'OperatorController::prefixes');
    $routes->post('prefixes', 'OperatorController::prefixesStore');
    $routes->post('prefixes/(:num)/update', 'OperatorController::prefixesUpdate/$1');
    $routes->post('prefixes/(:num)/toggle', 'OperatorController::prefixesToggle/$1');
    $routes->post('prefixes/(:num)/delete', 'OperatorController::prefixesDelete/$1');

    $routes->get('operations', 'OperatorController::operations');
    $routes->post('operations', 'OperatorController::operationsStore');
    $routes->post('operations/(:num)/update', 'OperatorController::operationsUpdate/$1');
    $routes->post('operations/(:num)/toggle', 'OperatorController::operationsToggle/$1');
    $routes->post('operations/(:num)/delete', 'OperatorController::operationsDelete/$1');

    $routes->get('fees', 'OperatorController::fees');
    $routes->post('fees', 'OperatorController::feesStore');
    $routes->post('fees/(:num)/update', 'OperatorController::feesUpdate/$1');
    $routes->post('fees/(:num)/toggle', 'OperatorController::feesToggle/$1');
    $routes->post('fees/(:num)/delete', 'OperatorController::feesDelete/$1');

    $routes->get('clients', 'OperatorController::clients');
    $routes->get('clients/(:num)', 'OperatorController::clientDetail/$1');
    $routes->post('clients/(:num)/status', 'OperatorController::clientStatus/$1');

    $routes->get('transactions', 'OperatorController::transactions');
    $routes->get('gains', 'OperatorController::gains');
});

// ── Auth client ──
$routes->get('client/login', 'ClientController::loginForm');
$routes->post('client/login', 'ClientController::login');
$routes->get('client/logout', 'ClientController::logout');

// ── Espace client (protégé) ──
$routes->group('client', ['filter' => 'clientAuth'], function ($routes) {
    $routes->get('dashboard', 'ClientController::dashboard');
    $routes->get('deposit', 'ClientController::depositForm');
    $routes->post('deposit', 'ClientController::deposit');
    $routes->get('withdraw', 'ClientController::withdrawForm');
    $routes->post('withdraw', 'ClientController::withdraw');
    $routes->get('transfer', 'ClientController::transferForm');
    $routes->post('transfer', 'ClientController::transfer');
    $routes->get('history', 'ClientController::history');
});

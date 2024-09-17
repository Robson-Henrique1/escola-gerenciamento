<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);  // Desativa o auto-roteamento

// Rotas de Login e Autenticação
$routes->post('api/logar', 'UsuarioController::logar');
$routes->post('api/registrar', 'UsuarioController::registrar');

// Rotas de Escolas
$routes->group('api/escola', ['filter' => 'cors', 'filter' => 'jwt-auth'], function (RouteCollection $routes) {
    $routes->get('/', 'EscolaController::index');
    $routes->get('(:num)', 'EscolaController::show/$1');
    $routes->post('/', 'EscolaController::create');
    $routes->put('(:num)', 'EscolaController::update/$1');
    $routes->delete('(:num)', 'EscolaController::delete/$1');
});

// Rotas de Professores
$routes->group('api/professores', ['filter' => 'cors', 'filter' => 'jwt-auth'], function (RouteCollection $routes) {
    $routes->get('/', 'ProfessorController::index');
    $routes->get('(:num)', 'ProfessorController::show/$1');
    $routes->post('/', 'ProfessorController::create');
    $routes->put('(:num)', 'ProfessorController::update/$1');
    $routes->delete('(:num)', 'ProfessorController::delete/$1');
});

// Rotas de Alunos
$routes->group('api/alunos', ['filter' => 'cors', 'filter' => 'jwt-auth'], function (RouteCollection $routes) {
    $routes->get('/', 'AlunoController::index');
    $routes->get('(:num)', 'AlunoController::show/$1');
    $routes->post('/', 'AlunoController::create');
    $routes->put('(:num)', 'AlunoController::update/$1');
    $routes->delete('(:num)', 'AlunoController::delete/$1');
});
// Rotas OPTIONS para lidar com CORS preflight requests
$routes->options('api/(:any)', static function () {
    $response = service('response');
    $response->setStatusCode(204);
    $response->setHeader('Allow', 'OPTIONS, GET, POST, PUT, DELETE');
    return $response;
});

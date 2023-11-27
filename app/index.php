<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Slim\Cookie\Cookie;

require __DIR__ . '/../vendor/autoload.php';
 
require_once "./middlewares/MWCliente.php";
require_once "./middlewares/MWToken.php"; 
require_once "./middlewares/MWAdministrador.php";
require_once "./middlewares/Logger.php";

require_once './db/accesoDB.php'; 

require_once './controllers/CuentaController.php';
require_once './controllers/AjusteController.php';
require_once './controllers/DepositoController.php';
require_once './controllers/RetiroController.php';
require_once './controllers/UsuarioController.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();


// Instantiate App
$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// -->Cuentas
$app->group('/cuenta',function (RouteCollectorProxy $group){
    $group->get('[/]',\CuentaController::class . '::TraerTodos');
    $group->get('/{id}',\CuentaController::class . '::TraerUno');
    $group->post('[/]', \CuentaController::class . '::CargarUno');
    $group->put('/{id}', \CuentaController::class . '::ModificarUno');
    $group->delete('/{id}/{tipoCuenta}/{moneda}', \CuentaController::class . '::BorrarUno');
    $group->post('/consultarCuenta/{numeroCuenta}/{tipoCuenta}', \CuentaController::class . '::ConsultarCuenta');
});//->add(new MWToken())->add(new MWSocios())

//-->Movimientos
$app->group('/movimientos', function (RouteCollectorProxy $group) {
    //-->Depositos
    $group->get('/totalDepositadoFechaParticular', \DepositoController::class . '::TotalDepositadoTipoCuentaFecha');
    $group->get('/depositosEntreFechasOrdenNombre', \DepositoController::class . '::DepositosEntreFechasSortNombre');
    $group->get('/depositosPorUsuario', \DepositoController::class . '::DepositosUsuario');
    $group->get('/depositosPorTipoCuenta', \DepositoController::class . '::DepositosTipoCuenta');
    $group->get('/depositosPorMoneda', \DepositoController::class . '::DepositosPorMoneda');
    //-->Retiros
    $group->get('/totalRetiradoFechaParticular', \RetiroController::class . '::TotalRetiradoTipoCuentaFecha');
    $group->get('/retirosPorUsuario', \RetiroController::class . '::RetirosUsuario');
    $group->get('/retirosEntreFechasOrdenNombre', \RetiroController::class . '::RetirosEntreFechasSortNombre');
    $group->get('/retirosPorTipoCuenta', \RetiroController::class . '::RetirosTipoCuenta');
    $group->get('/retirosPorMoneda', \RetiroController::class . '::RetirosPorMoneda');
    //-->Retiros y Depositos
    $group->get('/retirosYDepositosPorUsuario', \RetiroController::class . '::RetirosYDepositosPorUsuario');
});//->add(new MWToken());


// -->Ajustes
$app->group('/ajuste',function (RouteCollectorProxy $group){
    $group->get('[/]',\AjusteController::class . '::TraerTodos');
    $group->get('/{id}',\AjusteController::class . '::TraerUno');
    $group->post('[/]', \AjusteController::class . '::CargarUno');
    $group->put('/{id}', \AjusteController::class . '::ModificarUno');
    $group->delete('/{id}', \AjusteController::class . '::BorrarUno');
});//->add(new MWToken());

// -->Depositos
$app->group('/deposito',function (RouteCollectorProxy $group){
    $group->get('[/]',\DepositoController::class . '::TraerTodos');
    $group->get('/{id}',\DepositoController::class . '::TraerUno');
    $group->post('[/]', \DepositoController::class . '::CargarUno');
    $group->put('/{id}', \DepositoController::class . '::ModificarUno');
    $group->delete('/{id}', \DepositoController::class . '::BorrarUno');
});

// -->Retiros
$app->group('/retiro',function (RouteCollectorProxy $group){
    $group->get('[/]',\RetiroController::class . '::TraerTodos');
    $group->get('/{id}',\RetiroController::class . '::TraerUno');
    $group->post('[/]', \RetiroController::class . '::CargarUno');
    $group->put('/{id}', \RetiroController::class . '::ModificarUno');
    $group->delete('/{id}', \RetiroController::class . '::BorrarUno');
});//->add(new MWToken())->add(new MWSocios())

//-->Usuarios, lo maneja el administrador
$app->group('/usuario',function (RouteCollectorProxy $group){
    $group->get('[/]',\UsuarioController::class . '::TraerTodos');
    $group->get('/{id}',\UsuarioController::class . '::TraerUno');
    $group->post('[/]', \UsuarioController::class . '::CargarUno'); 
})->add(new MWToken())->add(new MWAdmin());

// //-->Login para conseguir token de ingreso
$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', \UsuarioController::class . '::LoguearCuenta')->add(\Logger::class . '::ValidarUsuario');
});
  
$app->get('[/]', function (Request $request, Response $response) {
    $payload = json_encode(array("SP" => "Banco"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});
  
$app->run();
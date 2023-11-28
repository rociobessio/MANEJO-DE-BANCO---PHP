<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

    class MWLog{
        public function __invoke(Request $request,RequestHandler $handler) : Response
        {
            $header = $request->getHeaderLine(("Authorization"));
            $token = trim(explode("Bearer", $header)[1]);
            $data = AutentificadorJWT::ObtenerData($token);
    
            $idUsuario = $data->id;//-->Obtengo el id del usuario
            $metodo = $request->getMethod();//-->Se obtiene el metodo/accion
            $response = new Response();
    
            try
            {
                $response= $handler->handle($request);
            }
            catch(Exception $excepcion)
            {
                $response->getBody()->write(json_encode(array("Error" => $excepcion->getMessage())));
            }
            return $response->withHeader('Content-Type', 'application/json');
        }
    
    }
<?php
    require_once "../app/models/Usuario.php";
    
    class Logger{
        /**
         * Esta funcion me permitira validar un usuario
         * para su ingreso a la aplicacion.
         * 
         */
        public static function ValidarUsuario($request,$handler){
            $parametros = $request->getParsedBody();
            $email = $parametros['emailUsuario'];
            $clave = $parametros['clave'];
            $usuario = Usuario::obtenerUsuarioMailClave($email,$clave);

            if($usuario != false){
                return $handler->handle($request);
            }

            throw new Exception("Mail y/o nro documento erroneos");
        }
    }
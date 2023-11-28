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

        /**
         * Se deberá generar un log de todos los accesos (no sólo login, sino cada vez que
         * se intente consumir un recurso, independientemente de su resultado)
         */
        public static function CargarLog($idUsuario,$accion){
            $objAccesoDatos = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDatos->retornarConsulta("INSERT INTO logsacceso (idUsuario,accion,fechaAccion) VALUES
            (:idUsuario,:accion,:fechaAccion)");
            $consulta->bindValue(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $consulta->bindValue(":accion", $accion, PDO::PARAM_STR);
            $fechaAccion = new DateTime(date("d-m-Y"));
            $consulta->bindValue(":fechaAccion",date_format($fechaAccion, 'Y-m-d'));
            $consulta->execute();
        }

        /**
         * Para desloguear al usuario.
         */
        public static function Desloguear($request,$response){
            $response->getBody()->write("Usuario deslogueado!");
            return $response->withHeader('Content-Type', 'application/json');
        }

        /**
         * Me va a permitir encapsular el obtener
         * la info del token para no repetir codigo
         */
        public static function ObtenerInfoLog($request){
            $header = $request->getHeaderLine(("Authorization"));
            $token = trim(explode("Bearer", $header)[1]);
            $data = AutentificadorJWT::ObtenerData($token);
            return $data;
        }
    }
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
         * Se deberá generar un log de transacciones en el que se registrarán los datos de
         * fecha y hora, usuario y número de operación, luego de que la operación sea
         * registrada.
         * 
         * @param int $idUsuario asumo que idUsuario es el usuario a cargo de realizar
         * la transaccion y no el propietario de la cuenta.
         * @param int $nroOperacion el nro de operacion del deposito/retiro generado.
         */
        public static function CargarLogTransaccion($idUsuario,$nroOperacion,$sobre,$idCuenta){
            $objAccesoDatos = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDatos->retornarConsulta("INSERT INTO logstransacciones (idUsuario,nroOperacion,idCuenta,fechaTransaccion,sobre) VALUES
            (:idUsuario,:nroOperacion,:idCuenta,:fechaTransaccion,:sobre)");
            $consulta->bindValue(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $consulta->bindValue(":nroOperacion", $nroOperacion, PDO::PARAM_INT);
            $consulta->bindValue(":idCuenta", $idCuenta, PDO::PARAM_INT);
            $consulta->bindValue(":sobre", $sobre, PDO::PARAM_STR);
            $fechaTransaccion = new DateTime();
            $consulta->bindValue(":fechaTransaccion", $fechaTransaccion->format('Y-m-d H:i:s'));
            $consulta->execute();
        }

        /**
         * Para desloguear al usuario.
         */
        public static function Desloguear($request,$response){
            //-->Se limpia la cookie
            $response = $response->withHeader('Set-Cookie', 'token=; Max-Age=0; HttpOnly; Secure');
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

        public static function ObtenerLogsAccesos(){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT * FROM logsacceso");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
        }

        public static function ObtenerLogsTransacciones(){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT * FROM logstransacciones");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
        }
    }
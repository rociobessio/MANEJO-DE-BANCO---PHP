<?php

    include_once "./models/Usuario.php";

    class UsuarioController extends Usuario{

        /**
         * Me permtira cargar un nuevo usuario en la tabla
         * usuarios.
         */
        public static function CargarUno($request, $response, $args){
            $parametros = $request->getParsedBody();

            if(isset($parametros['nombre']) && isset($parametros['apellido']) && isset($parametros['tipoDocumento']) &&
            isset($parametros['numeroDocumento']) && isset($parametros['email']) && isset($parametros['rol']) && isset($parametros['clave'])){

                $usuario = Usuario::obtenerUsuarioMailClave($parametros['email'],$parametros['clave']);
                if($usuario){
                    $payload = json_encode(array("mensaje" => "El usuario ya existe en el sistema!"));
                }
                else{
                    $nuevoUsuario = new Usuario();
                    $nuevoUsuario->setNombre($parametros['nombre']);
                    $nuevoUsuario->setApellido($parametros['apellido']);
                    $nuevoUsuario->setTipoDocumento($parametros['tipoDocumento']);
                    $nuevoUsuario->setNumeroDocumento($parametros['numeroDocumento']);
                    $nuevoUsuario->setEmail($parametros['email']);
                    $nuevoUsuario->setClave($parametros['clave']);
                    $nuevoUsuario->setRol($parametros['rol']);

                    Usuario::crear($nuevoUsuario);
                    $payload = json_encode(array("mensaje" => "El usuario se ha creado correctamente!"));
                }
            }
            else {
                $payload = json_encode(array("mensaje" => "Se necesitan todos los datos para generar el usuario!"));
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        /**
         * Me permtiria obtener un solo usuario mediante
         * su id.
         */
        public static function TraerUno($request, $response, $args){
            $val = $args['id'];
            $usuario = Usuario::obtenerUno(intval($val));//-->Me traigo uno.

            if($usuario !== false){$payload = json_encode($usuario);}
            else{ $payload = json_encode(array("mensaje" => "No hay coincidencia de usuario con ID:" . $val ." !"));}
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        /**
         * Me voy a poder traer todos los usuarios.
         */
        public static function TraerTodos($request, $response, $args){
            $listado = Usuario::obtenerTodos();
            $payload = json_encode(array("Usuarios" => $listado));
            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type','application/json');
        }

        /**
         * Me permitira loguear un usuario en la aplicacion
         * podra ser Cliente o Administrador.
         */
        public static function LoguearCuenta($request,$response,$args){
            $parametros = $request->getParsedBody();
            $email = $parametros['emailUsuario'];
            $clave = $parametros['clave'];

            $usuario = Usuario::obtenerUsuarioMailClave($email,$clave);
            if($usuario){
                $data = array('usuario' => $usuario->getNombre(), 'nroDocumento' => $usuario->getNumeroDocumento(), 'id' =>$usuario->getIdUsuario(),
                'rol' => $usuario->getRol());
                $creacionToken = AutentificadorJWT::CrearToken($data);
                
                $response = $response->withHeader('Set-Cookie', 'token=' . $creacionToken['jwt']);
                
                $payload = json_encode(array("mensaje" => "Usuario logueado correctamente", "token" => $creacionToken['jwt']));
            }
            else{$payload = json_encode(array("mensaje" => "Error al loguear el usuario"));}

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }
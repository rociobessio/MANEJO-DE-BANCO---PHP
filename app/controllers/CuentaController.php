<?php

    require_once "./interfaces/IApiUsable.php";
    include_once "./models/Cuenta.php";
    include_once "./models/AccionesLogs.php";
    include_once "./models/Uploader.php";
    require_once "./middlewares/AutentificadorJWT.php";

    class CuentaController extends Cuenta implements IApiUsable{

        /**
         * Me permtira dar de alta una cuenta. 
         * Si no se pasa el nro de cuenta puede ser un 
         * alta, si este se pasa y coincide tipo y moneda
         * entonces se modifica el saldo.
         * Si se da de alta una cuenta se asigna el id del usuario
         * al cual le pertenece y una url de la imagen guardada. 
        */
        public static function CargarUno($request, $response, $args){
            //-->1: Obtengo los datos.
            $parametros = $request->getParsedBody();
            $nroCuenta = isset($parametros['nroCuenta']) ? intval($parametros['nroCuenta']) : null;
            $nroDocumento = isset($parametros['nroDocumento']) ? $parametros['nroDocumento'] : null;
            $files = $request->getUploadedFiles();
            // var_dump($parametros);
            
            //-->Todos los parametros necesarios para la cuenta
            if(isset($parametros['tipoCuenta']) && isset($parametros['saldo']) && isset($parametros['tipoCuenta']) &&
            isset($parametros['moneda'])){
                //-->Busco la cuenta
                $cuenta = Cuenta::ObtenerCuentaPorNroYTipo(intval($nroCuenta),$parametros['tipoCuenta'],$parametros['moneda']);
                // var_dump($cuenta);

                if($cuenta && $cuenta->getEstado()){//-->Existe y esta activa
                    $cuenta->setSaldo($cuenta->getSaldo() + floatval($parametros['saldo']) );
                    Cuenta::modificar($cuenta);
                    $payload = json_encode(array("mensaje" => "La cuenta se ha modificado correctamente!"));     
                }else{//-->Cuenta nueva 
                    $cuenta = new Cuenta();
                    $cuenta->setEmail($parametros['email']);
                    $cuenta->setTipoDocumento($parametros['tipoDocumento']);
                    $cuenta->setNombre($parametros['nombre']);
                    $cuenta->setApellido($parametros['apellido']);
                    $cuenta->setSaldo(floatval($parametros['saldo']));//-->Inicial deberia ser 0?
                    $cuenta->setTipoCuenta($parametros['tipoCuenta']);
                    $cuenta->setMoneda($parametros['moneda']);
                    $cuenta->setNroDocumento($nroDocumento);
        
                    //-->Guardo la imagen de la cuenta
                    if (isset($files['fotoCuenta'])) {
                        $nroImagen = rand(1, 9999);//-->Genero un rand
                        $ruta = './ImagenesDeCuentas/2023/' . $cuenta->getTipoCuenta() . "_" .  $nroImagen .'.jpg';
                        $files['fotoCuenta']->moveTo($ruta); 
                        $cuenta->setUrlImagen($ruta);
                    }
                    
                    Cuenta::crear($cuenta);
                    $payload = json_encode(array("mensaje" => "La cuenta se ha creado correctamente!")); 
                }
            }
            else{
                $payload = json_encode(array("mensaje" => "Quedan parametros por ingresar!"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
	    
        public static function TraerUno($request, $response, $args){
            $val = $args['id'];
            $cuenta = Cuenta::obtenerUno(intval($val));//-->Me traigo uno.

            if($cuenta !== false){$payload = json_encode(array("Cuenta Buscada:" =>$cuenta));}
            else{ $payload = json_encode(array("mensaje" => "No hay coincidencia de cuenta con ID:" . $val ." !"));}
            
            //-->Cargo el log:
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id,AccionesLogs::TRAER_Cuenta);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
	    
        public static function TraerTodos($request, $response, $args){
            $listado = Cuenta::obtenerTodos();
            $payload = json_encode(array("Cuentas" => $listado));

            //-->Info log accion y carga:
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id,AccionesLogs::TODOS_Cuentas);


            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type','application/json');
        }
        
        /**
         * BorrarCuenta.php (por DELETE), debe recibir un número el tipo y número de cuenta
         * y debe realizar la baja de la cuenta (soft-delete, no físicamente) y la foto relacionada a
         * esa venta debe moverse a la carpeta /ImagenesBackupCuentas/2023.    
         */
        public static function BorrarUno($request, $response, $args){
            $id = $args['id'];
            $tipoCuenta = $args['tipoCuenta'];
            $moneda = $args['moneda'];

            if(!empty($tipoCuenta) && !empty($moneda)){
                $cuenta = Cuenta::ObtenerCuentaPorNroYTipo(intval($id),$tipoCuenta,$moneda);
                if($cuenta && $cuenta->getEstado()){
                    // var_dump($cuenta);
                    //-->Queda mover la img a backups:
                    $nroImagen = rand(1, 9999);//-->Genero un rand
                    $nombreImagen = Uploader::crearPathImagenCuenta($cuenta->getTipoCuenta(), $nroImagen);
                    $directorioBackup = './ImagenesBackUpCuentas/2023/'; 
                    $imagenActual = $cuenta->getUrlImagen();//-->Path actual de la imagen 
                    $uploader = new Uploader($directorioBackup);
                    
                    if ($uploader->moverImagenABackUp($imagenActual, $directorioBackup, $nombreImagen)) {
                        Cuenta::borrar(intval($id));
                        $payload = json_encode(array("mensaje" => "Se ha dado de baja la cuenta."));
                    }
                    else{$payload = json_encode(array("mensaje" => "No se pudo dar de baja."));}
                }
                else
                    $payload = json_encode(array("mensaje" => "No hay coincidencia de datos."));
            }
            else
                $payload = json_encode(array("mensaje" => "Debe de ingresar tambien el tipo de cuenta y la moneda!"));

            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::BAJA_Cuenta);


            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
	    
        /**
         * Debe recibir todos los datos propios de una cuenta (a excepción del saldo); si dicha
         * cuenta existe (comparar por Tipo y Nro. de Cuenta) se modifica, de lo contrario
         * informar que no existe esa cuenta.
         */
        public static function ModificarUno($request, $response, $args){
            $id = $args['id'];
            $params = $request->getParsedBody();

            if(isset($params['tipoCuenta'])){
                $cuenta = Cuenta::ObtenerCuentaPorNroYTipo(intval($id),$params['tipoCuenta'],$params['moneda']);
                // var_dump($cuenta);
                if($cuenta){
    
                    //-->Me fijo que parametros estan setteados para modificar
                    if(isset($params['estado'])){$cuenta->setEstado($params['estado']);}
                    if(isset($params['monedaMod'])){$cuenta->setMoneda($params['monedaMod']);} 
                    if(isset($params['urlImagen'])){$cuenta->setUrlImagen($params['urlImagen']);}
                    if(isset($params['estado'])){$cuenta->setEstado($params['estado']);}
                    if(isset($params['nroDocumento'])){
                        $usuario = Usuario::obtenerUnoPorNroDocumento(intval($params['nroDocumento']));

                        //-->Se podria tambien modificar la informacion dentro de usuarios
                        if($usuario){$cuenta->setNroDocumento($params['nroDocumento']);}
                        else{$payload = json_encode(array("mensaje" => "No existe ese usuario!"));}
                    }
                    if(isset($params['tipoCuentaMod'])){$cuenta->setTipoCuenta($params['tipoCuentaMod']);}
    
                    Cuenta::modificar($cuenta);
                    $payload = json_encode(array("mensaje" => "La cuenta se modifico correctamente!"));
                }
                else{
                    $payload = json_encode(array("mensaje" => "No hay coincidencia de cuenta con ID:" . $id ." !"));
                }
            }
            else{$payload = json_encode(array("mensaje" => "Se debe de ingresar el tipo y numero de cuenta para seguir!"));}

            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::MODIFICAR_Cuenta);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public static function ConsultarCuenta($request, $response, $args){
            $id = $args['numeroCuenta'];
            $tipoCuenta = $args['tipoCuenta'];
            if(!empty($id) && !empty($tipoCuenta)){
                $mensaje = Cuenta::ConsultarCuentaPorTipo(intval($id),$tipoCuenta);
                $payload =  json_encode(['resultado' => $mensaje]);
            }
            else{$payload = json_encode(array("mensaje" => "Se debe de ingresar el tipo y numero de cuenta para seguir!"));}

            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::CONSULTAR_Cuenta);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
<?php

    require_once "./interfaces/IApiUsable.php";
    include_once "./models/Deposito.php";
    include_once "./models/Cuenta.php";

    class DepositoController extends Deposito implements IApiUsable{

        public static function CargarUno($request, $response, $args){
            $parametros = $request->getParsedBody();
            $files = $request->getUploadedFiles();
            // var_dump($parametros);
        
            if(isset($parametros['nroCuenta']) && isset($parametros['tipoCuenta']) && isset($parametros['importeDeposito']) &&
            isset($parametros['moneda'])){
                //-->Valido que exista la cuenta
                $cuenta = Cuenta::ObtenerCuentaPorNroYTipo(intval($parametros['nroCuenta']), $parametros['tipoCuenta'],$parametros['moneda']);
                
                if($cuenta && $cuenta->getEstado()){//-->Existe y esta ACTIVA
                    $importeTotal = $cuenta->getSaldo() + floatval($parametros['importeDeposito']);
                    // var_dump($importeTotal);
                    $cuenta->setSaldo($importeTotal);
                    Cuenta::modificar($cuenta);//-->Modifico la cuenta
        
                    //-->Genero el deposito
                    $deposito = new Deposito();
                    $deposito->setMoneda($cuenta->getMoneda());
                    $deposito->setImporte(floatval($parametros['importeDeposito']));
                    $deposito->setNumeroCuenta(intval($parametros['nroCuenta']));
                    $deposito->setTipoCuenta($cuenta->getTipoCuenta());
                    
                    //-->Guardo la imagen del talon
                    if (isset($files['fotoTalonDeposito'])) {
                        $ruta = './ImagenesDeDepositos2023/' . $cuenta->getTipoCuenta() . "_" . $cuenta->getIdCuenta() . "_" . date_format(new DateTime(), 'Y-m-d_H-i-s') .'.jpg';
                        $files['fotoTalonDeposito']->moveTo($ruta); 
                    }
                    // var_dump($deposito);
                    Deposito::crear($deposito);
        
                    $payload = json_encode(array("mensaje" => "Depósito realizado correctamente."));
                }
                else{
                    $payload = json_encode(array("mensaje" => "No hay coincidencia de cuenta ACTIVA con ID:" . $parametros['nroCuenta'] 
                    .", tipo: " . $parametros['tipoCuenta'] . " y moneda: " . $parametros['moneda'] ."!"));
                }
            }
            else {
                $payload = json_encode(array("mensaje" => "Faltan parámetros en la solicitud."));
            }
        
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public static function ModificarUno($request, $response, $args){
            $id = $args['id'];
            $params = $request->getParsedBody();
            $deposito = Deposito::obtenerUno(intval($id));
            if($deposito){
                if(isset($params['tipoCuenta'])){$deposito->setTipoCuenta($params['tipoCuenta']);}
                if(isset($params['numeroCuenta'])){$deposito->setNumeroCuenta(intval($params['numeroCuenta']));}
                if(isset($params['importeDeposito'])){$deposito->setImporte(floatval($params['importeDeposito']));}
                if(isset($params['fechaDeposito'])){$deposito->setFechaDeposito($params['fechaDeposito']);}

                Deposito::modificar($deposito);
                $payload = json_encode(array("mensaje" => "El deposito se modifico correctamente!"));
            }
            else{
                $payload = json_encode(array("mensaje" => "No hay coincidencia de deposito con ID:" . $id ." !"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public static function TraerUno($request, $response, $args){
            $val = $args['id'];
            $deposito = Deposito::obtenerUno(intval($val));//-->Me traigo uno.

            if($deposito !== false){$payload = json_encode($deposito);}
            else{ $payload = json_encode(array("mensaje" => "No hay coincidencia de deposito con ID:" . $val ." !"));}
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
	    
        public static function TraerTodos($request, $response, $args){
            $listado = Deposito::obtenerTodos();
            $payload = json_encode(array("Depositos" => $listado));
            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type','application/json');
        }
	    
        public static function BorrarUno($request, $response, $args){
            $id = $args['id'];

            if(Deposito::obtenerUno(intval($id))){
                Deposito::borrar(intval($id));
                $payload = json_encode(array("mensaje" => "Se ha dado de baja el deposito."));
            }
            else
                $payload = json_encode(array("mensaje" => "El ID:" . $id . " no esta asignado a ningun deposito."));
                
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

//********************************************* MOVIMIENTOS *********************************************
        /**
         * a- El total depositado (monto) por tipo de cuenta y moneda en un día en
         * particular (se envía por parámetro), si no se pasa fecha, se muestran las del día
         * anterior.
         */
        public static function TotalDepositadoTipoCuentaFecha($request, $response, $args){
            $params = $request->getQueryParams();
            $fecha = $params['fechaParticular'] ?? date("Y-m-d");
            $payload = "";
        
            if(isset($params['tipoCuenta'])){
                $resultado = Deposito::calcularTotalDepositos($params['tipoCuenta'], $fecha);
                if (!$resultado) {
                    $payload = json_encode(array("mensaje" => "No se pudo calcular el total de depositos."));
                }
            }
            else{
                $payload = json_encode(array("mensaje" => "Se debe ingresar el tipo de cuenta."));
            }
        
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        /**
         * b- El listado de depósitos para un usuario en particular.
         */
        public static function DepositosUsuario($request, $response, $args) {
            $params = $request->getQueryParams(); 
        
            if (isset($params['emailUsuario'])) {
                $listado = Deposito::DepositosUsuarioParticular($params['emailUsuario']);
                $payload = json_encode(array("Depositos Del Usuario:" => $listado));
            } else {
                $payload = json_encode(array("mensaje" => "Se debe ingresar el mail del usuario."));
            }
        
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        /**
         * c- El listado de depósitos entre dos fechas ordenado por nombre.
         */
        public static function DepositosEntreFechasSortNombre($request, $response, $args){
            $params = $request->getQueryParams(); 
        
            if(isset($params['fechaInicio']) && isset($params['fechaFin'])){   
        
                $listado = Deposito::DepositosEntreFechasOrdenadosPorNombre($params['fechaInicio'],$params['fechaFin']);
                $payload = json_encode(array("Depositos entre fechas ordenados por Nombre:" => $listado));
            }
            else{
                $payload = json_encode(array("mensaje" => "Se deben ingresar las fechas."));
            }
        
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        /**
         * El listado de depósitos por tipo de cuenta.
         */
        public static function DepositosTipoCuenta($request, $response, $args){
            $params = $request->getQueryParams(); 

            if(isset($params['tipoCuenta'])){
                $listado = Deposito::DepositosPorTipoCuenta($params['tipoCuenta']);
                $payload = json_encode(array("Depositos por Tipo de Cuenta " . $params['tipoCuenta'] .":" => $listado));
            }
            else{
                $payload = json_encode(array("mensaje" => "Se deben ingresar el tipo de cuenta."));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        public static function DepositosPorMoneda($request, $response, $args){
            $params = $request->getQueryParams();
            if(isset($params['moneda'])){
                $listado = Deposito::DepositosPorTipoMoneda($params['moneda']);
                $payload = json_encode(array("Depositos con moneda " . $params['moneda'] .":" => $listado));
            }
            else{
                $payload = json_encode(array("mensaje" => "Se deben ingresar la moneda."));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

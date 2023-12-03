<?php
    require_once "./interfaces/IApiUsable.php";
    include_once "./models/Retiro.php";
    include_once "./models/Cuenta.php";

    class RetiroController extends Retiro implements IApiUsable{

        /**
         * (por POST) se recibe el Tipo de Cuenta, Nro de Cuenta y Moneda
         * y el importe a depositar, si la cuenta existe en banco.json, se decrementa el saldo
         * existente según el importe extraído y se registra en el archivo retiro.json la operación
         * con los datos de la cuenta y el depósito (fecha, monto) e id autoincremental.
         * Si la cuenta no existe o el saldo es inferior al monto a retirar, informar el tipo de error.
         */
        public static function CargarUno($request, $response, $args){
            $parametros = $request->getParsedBody();
            // var_dump($parametros);
        
            $payload = "";  
        
            if(isset($parametros['nroCuenta']) && isset($parametros['tipoCuenta']) && isset($parametros['importeRetiro']) && 
            isset($parametros['moneda'])){
                $cuenta = Cuenta::ObtenerCuentaPorNroYTipo(intval($parametros['nroCuenta']), $parametros['tipoCuenta'],$parametros['moneda']);
        
                if($cuenta && $cuenta->getEstado()){//-->Que ademas la cuenta este activa.
                    // var_dump($cuenta);
                    if($cuenta->verificarSaldo(floatval($parametros['importeRetiro']))){
                        $importeTotal = $cuenta->getSaldo() - floatval($parametros['importeRetiro']);
                        $cuenta->setSaldo($importeTotal);
                        Cuenta::modificar($cuenta);//-->Modifico la cuenta
        
                        //-->Genero el retiro
                        $nroOperacion = rand(1,999999);
                        $retiro = new Retiro();
                        $retiro->setNroOperacion($nroOperacion);
                        $retiro->setMoneda($cuenta->getMoneda());
                        $retiro->setImporteRetiro(floatval($parametros['importeRetiro'])); 
                        $retiro->setNumeroCuenta(intval($parametros['nroCuenta']));
                        $retiro->setTipoCuenta($cuenta->getTipoCuenta());
        
                        Retiro::crear($retiro);

                        //-->Si pude hacer la transaccion, guardo el log.
                        $data = Logger::ObtenerInfoLog($request);
                        Logger::CargarLogTransaccion($data->id,$nroOperacion,AccionesLogs::RETIRO);
                        $payload = json_encode(array("mensaje" => "Retiro generado correctamente!"));
                    }
                    else {
                        $payload = json_encode(array("mensaje" => "El saldo a retirar es mayor al saldo disponible!"));
                    }
                }
                else {
                    $payload = json_encode(array("mensaje" => "No hay coincidencia de cuenta activa con ID:" . $parametros['nroCuenta'] 
                        .", tipo: " . $parametros['tipoCuenta'] ." y moneda: " . $parametros['moneda'] . "!"));
                }
            }
            else {
                $payload = json_encode(array("mensaje" => "Se necesitan todos los datos para generar el retiro!"));
            }

            //-->Si pude hacer la transaccion, guardo el log.
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLogTransaccion($data->id,$nroOperacion,AccionesLogs::CARGAR_Retiro);
        
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        public static function ModificarUno($request, $response, $args){
            $id = $args['id'];
            $params = $request->getParsedBody();
            $retiro = Retiro::obtenerUno(intval($id));
            if($retiro){
                if(isset($params['tipoCuenta'])){$retiro->setTipoCuenta($params['tipoCuenta']);}
                if(isset($params['moneda'])){$retiro->setMoneda($params['moneda']);}
                if(isset($params['numeroCuenta'])){$retiro->setNumeroCuenta(intval($params['numeroCuenta']));}
                if(isset($params['importeRetiro'])){$retiro->setImporteRetiro(floatval($params['importeRetiro']));}
                if(isset($params['fechaExtraccion'])){$retiro->setFechaExtraccion($params['fechaExtraccion']);} 

                Retiro::modificar($retiro);
                $payload = json_encode(array("mensaje" => "El retiro se modifico correctamente!"));
            }
            else{
                $payload = json_encode(array("mensaje" => "No hay coincidencia de retiro con ID:" . $id ." !"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public static function TraerUno($request, $response, $args){
            $val = $args['id'];
            $retiro = Retiro::obtenerUno(intval($val));//-->Me traigo uno.

            if($retiro !== false){$payload = json_encode($retiro);}
            else{ $payload = json_encode(array("mensaje" => "No hay coincidencia de retiro con ID:" . $val ." !"));}
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        public static function TraerTodos($request, $response, $args){
            $listado = Retiro::obtenerTodos();
            $payload = json_encode(array("Retiros" => $listado));
            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type','application/json');
        }
        
        public static function BorrarUno($request, $response, $args){
            $id = $args['id'];

            if(Retiro::obtenerUno(intval($id))){
                Retiro::borrar(intval($id));
                $payload = json_encode(array("mensaje" => "Se ha dado de baja el retiro."));
            }
            else
                $payload = json_encode(array("mensaje" => "El ID:" . $id . " no esta asignado a ningun retiro."));
                
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

//********************************************* MOVIMIENTOS *********************************************
        public static function TotalRetiradoTipoCuentaFecha($request, $response, $args){
            $params = $request->getQueryParams();
            $fecha = $params['fechaParticular'] ?? date("Y-m-d");
            $payload = "";
        
            if(isset($params['tipoCuenta'])){
                $resultado = Retiro::calcularTotalRetiros($params['tipoCuenta'], $fecha);
                if (!$resultado) {
                    $payload = json_encode(array("mensaje" => "No se pudo calcular el total de retiros."));
                }
            }
            else{
                $payload = json_encode(array("mensaje" => "Se debe ingresar el tipo de cuenta."));
            }

            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::MOVIMIENTO_TOTAL_RETIRO_FECHA);
        
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public static function RetirosUsuario($request, $response, $args) {
            $params = $request->getQueryParams(); 
        
            if (isset($params['emailUsuario'])) {
                $listado = Retiro::RetirosUsuarioParticular($params['emailUsuario']);
                $payload = json_encode(array("Retiros Del Usuario:" => $listado));
            } else {
                $payload = json_encode(array("mensaje" => "Se debe ingresar el mail del usuario."));
            }
            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::MOVIMIENTO_RETIROS_USUARIO);
        
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        /**
         * c- El listado de retiros entre dos fechas ordenado por nombre.
         */
        public static function RetirosEntreFechasSortNombre($request, $response, $args){
            $params = $request->getQueryParams(); 
        
            if(isset($params['fechaInicio']) && isset($params['fechaFin'])){   
        
                $listado = Retiro::RetirosEntreFechasOrdenadosPorNombre($params['fechaInicio'],$params['fechaFin']);
                $payload = json_encode(array("Retiros entre fechas ordenados por Nombre:" => $listado));
            }
            else{
                $payload = json_encode(array("mensaje" => "Se deben ingresar las fechas."));
            }

            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::MOVIMIENTO_RETIROS_ENTRE_FECHAS_SORT_NOMBRE);
        
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        /**
         * d- El listado de retiros por tipo de cuenta.
         */
        public static function RetirosTipoCuenta($request, $response, $args){
            $params = $request->getQueryParams(); 

            if(isset($params['tipoCuenta'])){
                $listado = Retiro::RetirosPorTipoCuenta($params['tipoCuenta']);
                $payload = json_encode(array("Retiros por Tipo de Cuenta " . $params['tipoCuenta'] .":" => $listado));
            }
            else{
                $payload = json_encode(array("mensaje" => "Se deben ingresar el tipo de cuenta."));
            }
            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::MOVIMIENTO_RETIROS_TIPO_CUENTA);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        /**
         * f- El listado de todas las operaciones (depósitos y retiros) por usuario
         */
        public static function RetirosYDepositosPorUsuario($request, $response, $args){
            $params = $request->getQueryParams(); 

            if (isset($params['emailUsuario'])) {
                $listadoRetiros = Retiro::RetirosUsuarioParticular($params['emailUsuario']);
                $payload = json_encode(array("Retiros Del Usuario " . $params['emailUsuario'] . ": " => $listadoRetiros));
                $listaDepositos = Deposito::DepositosUsuarioParticular($params['emailUsuario']);
                $payload .= json_encode(array("Depositos Del Usuario " . $params['emailUsuario'] . ": " => $listaDepositos));
            
            } else {
                $payload = json_encode(array("mensaje" => "Se debe ingresar el mail del usuario."));
            }
            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::MOVIMIENTO_RETIROS_DEPOSITOS_USUARIO);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        /**
         * El listado de retiros por moneda.
         */
        public static function RetirosPorMoneda($request, $response, $args){
            $params = $request->getQueryParams();
            if(isset($params['moneda'])){
                $listado = Retiro::RetirosPorTipoMoneda($params['moneda']);
                $payload = json_encode(array("Retiros con moneda " . $params['moneda'] .":" => $listado));
            }
            else{
                $payload = json_encode(array("mensaje" => "Se deben ingresar la moneda."));
            }

            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::MOVIMIENTO_RETIROS_POR_MONEDA);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
	    
    }
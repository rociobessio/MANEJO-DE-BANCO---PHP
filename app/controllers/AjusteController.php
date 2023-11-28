<?php

    require_once "./interfaces/IApiUsable.php";
    include_once "./models/Ajuste.php";
    include_once "./models/Cuenta.php";

    class AjusteController extends Ajuste implements IApiUsable{

        /**
         * Se ingresa el número de extracción o depósito afectado al ajuste y el motivo del
         * mismo. El número de extracción o depósito debe existir.
         * 
         * Actualiza en el saldo en el archivo banco.json
         */
        public static function CargarUno($request, $response, $args){
            $parametros = $request->getParsedBody();

            if(isset($parametros['ajusteMonto']) && isset($parametros['motivoAjuste'])){

                //-->Me fijo que parametro esta setteado
                if(isset($parametros['nroExtraccion'])){
                    if(Ajuste::generarAjuste($parametros['motivoAjuste'],floatval($parametros['ajusteMonto']),
                    "extracciones",intval($parametros['nroExtraccion']))){
                        $payload = json_encode(array("mensaje" => "Ajuste generado correctamente sobre la extraccion!"));
                    }
                    else{
                        $payload = json_encode(array("mensaje" => "Ocurrio un error al querer realizar el ajuste sobre la extraccion!"));
                    }
                }
                elseif(isset($parametros['nroDeposito'])){
                    if(Ajuste::generarAjuste($parametros['motivoAjuste'],floatval($parametros['ajusteMonto']),
                    "depositos",intval($parametros['nroDeposito']))){
                        $payload = json_encode(array("mensaje" => "Ajuste generado correctamente sobre el deposito!"));
                    }
                    else{$payload = json_encode(array("mensaje" => "Ocurrio un error al querer realizar el ajuste sobre el deposito!"));}
                }
                else{$payload = json_encode(array("mensaje" => "Para completar la accion se debe de ingresar el nro de deposito o de extraccion!"));}
            }
            else{$payload = json_encode(array("mensaje" => "Se debe de ingresar el monto del ajuste y motivo!"));}
        
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        public static function TraerUno($request, $response, $args){
            $val = $args['id'];
            $ajuste = Ajuste::obtenerUno(intval($val));//-->Me traigo uno.

            if($ajuste !== false){$payload = json_encode($ajuste);}
            else{ $payload = json_encode(array("mensaje" => "No hay coincidencia de ajuste con ID:" . $val ." !"));}
            
            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::TRAER_Ajuste);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
	    
        public static function TraerTodos($request, $response, $args){
            $listado = Ajuste::obtenerTodos();
            $payload = json_encode(array("Ajustes" => $listado));
            $response->getBody()->write($payload);

            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::TODOS_Ajustes);

            return $response
            ->withHeader('Content-Type','application/json');
        }
	    	    
        public static function BorrarUno($request, $response, $args){
            $id = $args['id'];

            if(Ajuste::obtenerUno(intval($id))){
                Ajuste::borrar(intval($id));
                $payload = json_encode(array("mensaje" => "Se ha dado de baja el ajuste."));
            }
            else
                $payload = json_encode(array("mensaje" => "El ID:" . $id . " no esta asignado a ningun ajuste."));
            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::ELIMINAR_Ajuste);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
	    
        public static function ModificarUno($request, $response, $args){
            $id = $args['id'];

            $ajuste = Ajuste::obtenerUno(intval($id));
            if($ajuste !== false){
                $params = $request->getParsedBody();

                //-->Me fijo que parametros estan setteados para modificar
                if(isset($params['motivoAjuste'])){$ajuste->setMotivoAjuste($params['motivoAjuste']);}
                if(isset($params['ajusteMonto'])){$ajuste->setAjusteMonto(floatval($params['ajusteMonto']));}
                if(isset($params['tipoDocumento'])){$ajuste->setTipoDocumento($params['tipoDocumento']);}
                if(isset($params['numeroBuscado'])){$ajuste->setNumeroBuscado(intval($params['numeroBuscado']));}
                if(isset($params['numeroCuenta'])){$ajuste->setNumeroCuenta(intval($params['numeroCuenta']));}
                if(isset($params['ajusteSobre'])){$ajuste->setAjusteSobre($params['ajusteSobre']);}

                Ajuste::modificar($ajuste);
                $payload = json_encode(array("mensaje" => "El ajuste se modifico correctamente!"));
            }
            else{
                $payload = json_encode(array("mensaje" => "No hay coincidencia de ajuste con ID:" . $id ." !"));
            }

            //-->Guardo el log
            $data = Logger::ObtenerInfoLog($request);
            Logger::CargarLog($data->id, AccionesLogs::MODIFICAR_Ajuste);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
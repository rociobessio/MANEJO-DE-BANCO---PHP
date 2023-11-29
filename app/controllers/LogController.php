<?php
    include_once "./middlewares/Logger.php";
    include_once "./models/CSV.php";

    class LogController extends Logger{

        /**
         * Me permitira obtener todos los logs
         * de las acciones realizadas.
         */
        public static function TraerTodosLogsAcciones($request, $response, $args){
            $listado = Logger::ObtenerLogsAccesos();
            
            $payload = json_encode(array("Logs Acciones" => $listado)); 
            
            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type','application/json');
        }

        /**
         * Me permitira obtener todos los logs
         * realizados sobre las transacciones.
         */
        public static function TraerTodosLogsTransacciones($request, $response, $args){
            $listado = Logger::ObtenerLogsTransacciones();
            
            $payload = json_encode(array("Logs Transacciones" => $listado));  
            
            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type','application/json');
        }


        public static function ExportarLogsAcciones($request,$response,$args){
            try{  
                
                $archivo = CSV::ExportarCSV("logsAcciones.csv");
                if(file_exists($archivo) && filesize($archivo) > 0){
                    $payload = json_encode(array("Archivo creado" => $archivo));
                }
                else{
                    $payload = json_encode(array("Error" => "Datos ingresados invalidos."));
                }
                $response->getBody()->write($payload);
            }
            catch (Exception $e){
                echo $e;
            }
            finally{
                return $response->withHeader('Content-Type', 'text/csv');
            }
        }
    }
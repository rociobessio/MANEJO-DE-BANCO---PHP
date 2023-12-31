<?php

    class CSV{
        /**
         * Me permitira exportar un archivo CSV.
         * 
         * @param string $path la ruta del archivo.
         * @return string $path el path del archivo.
         */
        public static function ExportarCSV($path){
            // echo  'EN EXPORTAR: ';
            $lista = Logger::ObtenerLogsAccesos();
            // var_dump($lista);
            $archivo = fopen($path,"w");

            foreach($lista as $item){
                $separar = implode(",", (array)$item);
                if($archivo){
                    fwrite($archivo, $separar.",\r\n");
                }
            }
            fclose($archivo);
            return $path;
        }

        /**
         * Me va a permitirme importarme un 
         * archivo csv
         * @param string $path el path del archivo.
         * @param array retorna el array.
         */
        public static function ImportarCSV($path){
            $aux = fopen($path,"r");
            $array = [];

            if(isset($aux)){
                try
                {
                    while(!feof($aux))
                    {
                        $datos = fgets($aux);                        
                        if(!empty($datos))
                        {          
                            array_push($array, $datos);                                                
                        }
                    }
                }
                catch(Exception $e)
                {
                    echo "Error:";
                    echo $e;
                }
                finally
                {
                    fclose($aux);
                    return $array;
                }
            }
        }

        /**
         * Me permtira leer un CSV.
         */
        public static function LeerCSV($path){
            $file = fopen($path, 'r');
            $data = array();
            while (($line = fgetcsv($file)) !== FALSE) {
                $data[] = $line;
            }
            fclose($file);
            return $data;
        }
        

        /**
         * Me va a permitir validar la extension del 
         * archivo.
         */
        public static function ValidarArchivo($request,$handler){
            $uploadedFiles = $request->getUploadedFiles();
            // var_dump($uploadedFiles);
            if (isset($uploadedFiles['file'])) {
            
                if (preg_match('/\.csv$/i', $uploadedFiles['file']->getClientFilename()) == 0){
                    throw new Exception("Debe ser un archivo CSV");
                }
                
                return $handler->handle($request);
            }

            throw new Exception("Error no se recibio el archivo");
        }
        
    }
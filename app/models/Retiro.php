<?php
    require_once "./interfaces/ICrud.php";  
    
    class Retiro implements ICrud{
//********************************************* ATRIBUTOS *********************************************
        public $idRetiro;
        public $numeroCuenta;
        public $tipoCuenta;
        public $importeRetiro;
        public $fechaExtraccion;
        public $moneda;
//********************************************* GETTERS *********************************************
        public function getIdRetiro(){
            return $this->idRetiro;
        }
        public function getNumeroCuenta(){
            return $this->numeroCuenta;
        }
        public function getTipoCuenta(){
            return $this->tipoCuenta;
        }
        public function getImporteRetiro(){
            return $this->importeRetiro;
        } 
        public function getFechaExtraccion(){
            return $this->fechaExtraccion;
        } 
        public function getMoneda(){
            return $this->moneda;
        }
//********************************************* SETTERS *********************************************
        public function setIdRetiro($id){
            if (isset($id) && is_numeric($id)){
                $this->idRetiro = $id;
            }
        }
        public function setNumeroCuenta($nroCuenta){
            if (isset($nroCuenta) && is_numeric($nroCuenta)){
                $this->numeroCuenta = $nroCuenta;
            }
        }
        public function setTipoCuenta($tipoCuenta){
            if(isset($tipoCuenta) && !empty($tipoCuenta)) {
                $this->tipoCuenta = $tipoCuenta;
            }
        } 
        public function setImporteRetiro($importe){
            if(isset($importe) && is_float($importe)) {
                $this->importeRetiro = $importe;
            }
        }
        public function setFechaExtraccion($fechaRetiro){
            if(isset($fechaRetiro) && !empty($fechaRetiro)) {
                $this->fechaExtraccion = $fechaRetiro;
            }
        } 
        public function setMoneda($moneda){
            if(isset($moneda) && !empty($moneda)) {
                $this->moneda = $moneda;
            }
        }
//********************************************* FUNCIONES *********************************************
        /**
         * Me va a permitir guardar una instancia
         * de Retiro en la tabla retiros.
         */
        public static function crear($retiro){
            $fechaExtraccion = new DateTime(date("d-m-Y"));//-->Le asigno la fecha de extraccion
            $accesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $accesoDB->retornarConsulta("INSERT INTO retiros (numeroCuenta,tipoCuenta,importeRetiro,fechaExtraccion,moneda)
            VALUES (:numeroCuenta,:tipoCuenta,:importeRetiro,:fechaExtraccion,:moneda)");
            $consulta->bindValue(':numeroCuenta',$retiro->getNumeroCuenta(),PDO::PARAM_INT);
            $consulta->bindValue(':tipoCuenta',$retiro->getTipoCuenta(),PDO::PARAM_STR);
            $consulta->bindValue(':moneda',$retiro->getMoneda(),PDO::PARAM_STR);
            $consulta->bindValue(':importeRetiro',$retiro->getImporteRetiro(),PDO::PARAM_INT); 
            $consulta->bindValue(':fechaExtraccion',date_format($fechaExtraccion, "Y-m-d"),PDO::PARAM_STR);
            $consulta->execute();
            return $accesoDB->retornarUltimoInsertado();
        }

        public static function obtenerTodos(){
            $accesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $accesoDB->retornarConsulta("SELECT * FROM retiros");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Retiro');
        }
        
        /**
         * Me permtira obtener una instancia de 
         * Retiro de la tabla retiros mediante
         * la coincidencia de su id.
         */
        public static function obtenerUno($idRetiro){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT idRetiro,numeroCuenta,tipoCuenta,importeRetiro,
            fechaExtraccion,moneda FROM retiros WHERE idRetiro = :idRetiro");
            $consulta->bindValue(':idRetiro', $idRetiro, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchObject('Retiro');
        }
        
        /**
         * Me permitira modificar
         * un retiro
         */
        public static function modificar($retiro){
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("UPDATE retiros SET importeRetiro = :importeRetiro,
            tipoCuenta= :tipoCuenta, numeroCuenta = :numeroCuenta,moneda = :moneda,
            fechaExtraccion = :fechaExtraccion WHERE idRetiro = :idRetiro");
            $consulta->bindValue(':idRetiro',$retiro->getIdRetiro(),PDO::PARAM_INT);
            $consulta->bindValue(':numeroCuenta',$retiro->getNumeroCuenta(),PDO::PARAM_INT);
            $consulta->bindValue(':tipoCuenta',$retiro->getTipoCuenta(),PDO::PARAM_STR);
            $consulta->bindValue(':moneda',$retiro->getMoneda(),PDO::PARAM_STR);
            $consulta->bindValue(':importeRetiro',$retiro->getImporteRetiro(),PDO::PARAM_INT); 
            $consulta->bindValue(':fechaExtraccion',$retiro->getFechaExtraccion(),PDO::PARAM_STR);
            $consulta->execute();
        }
        
        /**
         * Me permitira generar una baja fisica de un retiro
         */
        public static function borrar($idRetiro){
            $objAccesoDato = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDato->retornarConsulta("DELETE FROM retiros WHERE idRetiro = :idRetiro"); 
            $consulta->bindValue(':idRetiro', $idRetiro, PDO::PARAM_INT);
        
            return $consulta->execute();
        }

        public function verificarImporte($valor){
            return $this->getImporteRetiro() >= $valor;
        }
//********************************************* MOVIMIENTOS *********************************************
        /**
         * a- El total retirado (monto) por tipo de cuenta y moneda en un día en particular
         * (se envía por parámetro), si no se pasa fecha, se muestran las del día anterior.
         */
        public static function calcularTotalRetiros($tipoCuenta, $fecha = null){
            $retorno = false;
            $objAccesoDato = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDato->retornarConsulta("
                SELECT
                    tipoCuenta,
                    SUM(importeRetiro) as totalRetiro
                FROM
                    retiros
                WHERE
                    fechaExtraccion = COALESCE(:fecha, DATE_SUB(CURDATE(), INTERVAL 1 DAY)) 
                    AND tipoCuenta = :tipoCuenta
                GROUP BY
                    tipoCuenta;
            "); 
            $consulta->bindParam(':fecha', $fecha, PDO::PARAM_STR); 
            $consulta->bindParam(':tipoCuenta', $tipoCuenta, PDO::PARAM_STR);
            $consulta->execute();
            
            while ($resultado = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $retorno = true;
                echo 'El monto total retirado con tipo de cuenta: ' . $resultado['tipoCuenta'] . ' en la fecha: ' . $fecha . '<br>' . 
                    'es: ' .  $resultado['totalRetiro'] . '<br>';
            } 
            return $retorno;
        }

        /**
         * b- El listado de retiros para un usuario en particular.
         */
        public static function RetirosUsuarioParticular($emailUsuario) {
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("
                SELECT retiros.*
                FROM retiros
                INNER JOIN cuentas ON retiros.numeroCuenta = cuentas.idCuenta
                WHERE cuentas.email = :emailUsuario
            ");
        
            $valorEmailUsuario = $emailUsuario ?? '';
        
            $consulta->bindParam(':emailUsuario', $valorEmailUsuario);
            $consulta->execute();
        
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }
        

        /**
         * c- El listado de retiros entre dos fechas ordenado por nombre.
         */
        public static function RetirosEntreFechasOrdenadosPorNombre($fechaInicio, $fechaFin) {
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("
                    SELECT
                    cuentas.nombre AS nombreUsuario,
                    cuentas.apellido AS apellidoUsuario,
                    retiros.idRetiro,
                    retiros.numeroCuenta,
                    retiros.tipoCuenta,
                    retiros.importeRetiro,
                    retiros.fechaExtraccion
                FROM
                    retiros
                INNER JOIN
                    cuentas ON retiros.numeroCuenta = cuentas.idCuenta 
                WHERE
                retiros.fechaExtraccion BETWEEN :fechaInicio AND :fechaFin
                ORDER BY
                    cuentas.nombre, cuentas.apellido, retiros.numeroCuenta, retiros.fechaExtraccion;
            ");
        
            $consulta->bindParam(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
            $consulta->bindParam(':fechaFin', $fechaFin, PDO::PARAM_STR);
            $consulta->execute();
        
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * d- El listado de retiros por tipo de cuenta.
         */
        public static function RetirosPorTipoCuenta($tipoCuenta){
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("SELECT * FROM retiros
            WHERE tipoCuenta = :tipoCuenta");

            $consulta->bindParam(':tipoCuenta', $tipoCuenta, PDO::PARAM_STR);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * El listado de retiros por moneda.
         */
        public static function RetirosPorTipoMoneda($moneda){
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("
            SELECT retiros.*
            FROM retiros
            INNER JOIN cuentas ON retiros.numeroCuenta = cuentas.idCuenta
            WHERE cuentas.moneda = :moneda
            "); 

            $consulta->bindParam(':moneda', $moneda);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }
}
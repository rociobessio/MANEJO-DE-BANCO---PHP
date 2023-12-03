<?php
    require_once "./interfaces/ICrud.php";  

    class Ajuste implements ICrud{
//********************************************* ATRIBUTOS *********************************************
        public $idAjuste;
        public $motivoAjuste;
        public $ajusteMonto;
        public $numeroBuscado;//-->La id de retiro o deposito.
        public $numeroCuenta;
        public $ajusteSobre;//-->Extraccion o deposito
        public $nroOperacion;
//********************************************* GETTERS *********************************************
        public function getIdAjuste(){
            return $this->idAjuste;
        }
        public function getMotivoAjuste(){
            return $this->motivoAjuste;
        }
        public function getAjusteMonto(){
            return $this->ajusteMonto;
        }
        public function getNumeroBuscado(){
            return $this->numeroBuscado;
        }
        public function getNumeroCuenta(){
            return $this->numeroCuenta;
        }
        public function getAjusteSobre(){
            return $this->ajusteSobre;
        }
        public function getNroOperacion(){
            return $this->nroOperacion;
        }
//********************************************* SETTERS *********************************************
        public function setIdAjuste($id){
            if (isset($id) && is_numeric($id)){
                $this->idAjuste = $id;
            }
        }
        public function setMotivoAjuste($motivo){
            if(isset($motivo) && !empty($motivo)) {
                $this->motivoAjuste = $motivo;
            }
        }
        public function setAjusteMonto($ajuste){
            if (isset($ajuste) && is_float($ajuste)){
                $this->ajusteMonto = $ajuste;
            }
        }
        public function setNumeroBuscado($nroBuscado){
            if (isset($nroBuscado) && is_numeric($nroBuscado)){
                $this->numeroBuscado = $nroBuscado;
            }
        }
        public function setNumeroCuenta($nroCuenta){
            if (isset($nroCuenta) && is_numeric($nroCuenta)){
                $this->numeroCuenta = $nroCuenta;
            }
        }
        public function setAjusteSobre($sobre){
            if(isset($sobre) && !empty($sobre)) {
                $this->ajusteSobre = $sobre;
            }
        }
        public function setNroOperacion($nro){
            if(isset($nro) && is_numeric($nro)) {
                $this->nroOperacion = $nro;
            }
        }
//********************************************* CONSTRUCTOR *********************************************
        public static function constructor($monto,$sobre,$motivo,$nroBuscado,$nroCuenta,$nroOperacion){
            $ajuste = new Ajuste();
            $ajuste->setAjusteMonto($monto);
            $ajuste->setAjusteSobre($sobre);
            $ajuste->setMotivoAjuste($motivo);
            $ajuste->setNumeroBuscado($nroBuscado);
            $ajuste->setNumeroCuenta($nroCuenta);
            $ajuste->setNroOperacion($nroOperacion);
            return $ajuste;
        }
//********************************************* FUNCIONES *********************************************

        public static function crear($ajuste){
            $accesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $accesoDB->retornarConsulta("INSERT INTO ajustes (numeroCuenta,motivoAjuste,ajusteMonto,
            numeroBuscado,ajusteSobre,nroOperacion) VALUES (:numeroCuenta,:motivoAjuste,:ajusteMonto,:numeroBuscado,:ajusteSobre,:nroOperacion)");
            $consulta->bindValue(':numeroCuenta',$ajuste->getNumeroCuenta(),PDO::PARAM_INT);
            $consulta->bindValue(':motivoAjuste',$ajuste->getMotivoAjuste(),PDO::PARAM_STR);
            $consulta->bindValue(':ajusteMonto',$ajuste->getAjusteMonto(),PDO::PARAM_INT);
            $consulta->bindValue(':numeroBuscado', $ajuste->getNumeroBuscado(),PDO::PARAM_INT);
            $consulta->bindValue(':ajusteSobre', $ajuste->getAjusteSobre(),PDO::PARAM_STR);
            $consulta->bindValue(':nroOperacion', $ajuste->getNroOperacion(),PDO::PARAM_INT);
            $consulta->execute();
            return $accesoDB->retornarUltimoInsertado();
        }
        
        public static function obtenerTodos(){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT * FROM ajustes");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Ajuste');
        }
        
        public static function obtenerUno($idAjuste){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT idAjuste,numeroCuenta,motivoAjuste,ajusteMonto,ajusteSobre,nroOperacion
            FROM ajustes WHERE idAjuste = :idAjuste");
            $consulta->bindValue(':idAjuste', $idAjuste, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchObject('Ajuste');
        }
        
        public static function modificar($ajuste){
            $accesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $accesoDB->retornarConsulta("UPDATE ajustes SET
            numeroCuenta = :numeroCuenta, motivoAjuste = :motivoAjuste,ajusteMonto = :ajusteMonto,
            numeroBuscado = :numeroBuscado,ajusteSobre =:ajusteSobre
            WHERE idAjuste = :idAjuste");
            $consulta->bindValue(':idAjuste',$ajuste->getIdAjuste(),PDO::PARAM_INT);
            $consulta->bindValue(':numeroCuenta',$ajuste->getNumeroCuenta(),PDO::PARAM_INT);
            $consulta->bindValue(':motivoAjuste',$ajuste->getMotivoAjuste(),PDO::PARAM_STR);
            $consulta->bindValue(':ajusteMonto',$ajuste->getAjusteMonto(),PDO::PARAM_INT);
            $consulta->bindValue(':numeroBuscado', $ajuste->getNumeroBuscado(),PDO::PARAM_INT);
            $consulta->bindValue(':ajusteSobre', $ajuste->getAjusteSobre(),PDO::PARAM_STR);
            $consulta->execute();
        }
        
        public static function borrar($idAjuste){
            $objAccesoDato = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDato->retornarConsulta("DELETE FROM ajustes WHERE idAjuste = :idAjuste"); 
            $consulta->bindValue(':idAjuste', $idAjuste, PDO::PARAM_INT);
        
            return $consulta->execute();
        }

        /**¨
         * Me permitira discriminar sobre donde se realiza el ajuste, sobre
         * extracciones o depositos.
         */
        public static function generarAjuste($motivo,$montoAjuste,$sobre,$nroBuscado,$nroOperacion,$request){ 
            if($sobre === "extracciones"){
                $retiro = Retiro::obtenerUno(intval($nroBuscado));
                if($retiro){
                    if(Ajuste::aplicarAjusteSobreExtraccion($motivo,$montoAjuste,$retiro,$nroOperacion,$request)){
                        return true;
                    }
                }
                else{
                    echo 'No existe un numero de extraccion/retiro bajo el numero: ' . $nroBuscado . '<br>';
                }
            }
            elseif($sobre === "depositos"){
                $deposito = Deposito::obtenerUno(intval($nroBuscado));
                if($deposito){
                    if(Ajuste::aplicarAjusteSobreDeposito($motivo,$montoAjuste,$deposito,$nroOperacion,$request)){
                        return true;
                    }
                }
                else{
                    echo 'No existe un numero de deposito bajo el numero: ' . $nroBuscado . '<br>';
                }
            }
            return false;
        }

        /**
         * Se podra aplicar un ajuste sobre un retiro existente.
         * verifica que el importe del retiro sea mayor al ajuste
         * ingresado, que exista la cuenta, y que se pueda actualizar
         * tanto el retiro como la cuenta existentes.
         * 
         * @param string $motivo
         * @param float $montoAjuste
         * @param Retiro $retiro
         * 
         * @return bool true si pudo aplicar el ajuste
         * correctamente, false sino.
         */
        private static function aplicarAjusteSobreExtraccion($motivo,$montoAjuste,$retiro,$nroOperacion,$request){

            if($retiro->verificarImporte(floatval($montoAjuste))){
                $cuenta = Cuenta::ObtenerCuentaPorNroYTipo($retiro->getNumeroCuenta(),$retiro->getTipoCuenta(),$retiro->getMoneda());
                if($cuenta && $cuenta->getEstado()){
                    
                    $cuenta->setSaldo($cuenta->getSaldo() + $montoAjuste);
                    Cuenta::modificar($cuenta); 

                    //-->Genero el ajuste:
                    $ajuste = Ajuste::constructor($montoAjuste,"Retiro",$motivo,$retiro->getIdRetiro(),$cuenta->getIdCuenta(),$nroOperacion);
                    Ajuste::crear($ajuste);
                    
                    //-->Si pude hacer la transaccion, guardo el log.
                    $data = Logger::ObtenerInfoLog($request);
                    Logger::CargarLogTransaccion($data->id,$nroOperacion,AccionesLogs::AJUSTE,$cuenta->getIdCuenta());

                    return true;
                }
            }
            else{
                echo 'El monto del ajuste es mayor al valor de ese retiro.<br>'; 
            }
            return false;
        }

        /**
         * Se podra aplicar un ajuste sobre un deposito existente.
         * verifica que el importe del deposito sea mayor al ajuste
         * ingresado, que exista la cuenta, y que se pueda actualizar
         * tanto el deposito como la cuenta existentes.
         * 
         * 
         * @return bool true si pudo aplicar el ajuste
         * correctamente, false sino.
         */
        private static function aplicarAjusteSobreDeposito($motivo,$montoAjuste,$deposito,$nroOperacion,$request){
            if($deposito->verificarImporte($montoAjuste)){
                $cuenta = Cuenta::ObtenerCuentaPorNroYTipo($deposito->getNumeroCuenta(),$deposito->getTipoCuenta(),$deposito->getMoneda());
                if($cuenta && $cuenta->getEstado()){

                    $cuenta->setSaldo($cuenta->getSaldo() - $montoAjuste);
                    // var_dump($cuenta->getSaldo());
                    Cuenta::modificar($cuenta); 

                    //-->Genero el ajuste:
                    $ajuste = Ajuste::constructor($montoAjuste,"Deposito",$motivo,$deposito->getIdDeposito(),$cuenta->getIdCuenta(),$nroOperacion);
                    Ajuste::crear($ajuste);

                    //-->Si pude hacer la transaccion, guardo el log.
                    $data = Logger::ObtenerInfoLog($request);
                    Logger::CargarLogTransaccion($data->id,$nroOperacion,AccionesLogs::AJUSTE,$cuenta->getIdCuenta());

                    return true;
                }
            }
            else{ 
                echo 'El monto del ajuste es mayor al valor de ese deposito.<br>';
            }
            return false;
        }
}
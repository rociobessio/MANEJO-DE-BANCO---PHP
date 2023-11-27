<?php
    require_once "./interfaces/ICrud.php";  
    
    class Cuenta implements ICrud{
//********************************************* ATRIBUTOS *********************************************
        public $idCuenta;
        public $tipoCuenta;//-->Se unifica tipo cuenta & moneda [CA$,CAU$S,CC$,CCU$S]
        public $saldo;
        public $estado;//-->Si esta activa (true), o no (false).
        public $moneda;
        public $idUsuario;
        public $urlImagen;
//********************************************* GETTERS *********************************************
        public function getIdCuenta(){
            return $this->idCuenta;
        }
        public function getTipoCuenta(){
            return $this->tipoCuenta;
        }
        public function getSaldo(){
            return $this->saldo;
        }
        public function getEstado(){
            return $this->estado;
        }  
        public function getMoneda(){
            return $this->moneda;
        }
        public function getIdUsuario(){
            return $this->idUsuario;
        }
        public function getUrlImagen(){
            return $this->urlImagen;
        }
//********************************************* SETTERS *********************************************
        public function setID($id){
            if (isset($id) && is_numeric($id)){
                $this->idCuenta = $id;
            }
        }
        public function setIdUsuario($idUsuario){
            if (isset($idUsuario) && is_numeric($idUsuario)){
                $this->idUsuario = $idUsuario;
            }
        }
        public function setTipoCuenta($tipoCuenta){
            if(isset($tipoCuenta) && !empty($tipoCuenta)) {
                $this->tipoCuenta = $tipoCuenta;
            }
        }
        public function setSaldo($saldo){
            if (is_float($saldo)){
                $this->saldo = $saldo;
            }
        }
        public function setEstado($estado){
            if(isset($estado) && is_bool($estado)) {
                $this->estado = $estado;
            }
        }
        public function setMoneda($moneda){
            if(isset($moneda) && !empty($moneda)) {
                $this->moneda = $moneda;
            }
        }
        public function setUrlImagen($urlImagen){
            if(isset($urlImagen) && !empty($urlImagen)) {
                $this->urlImagen = $urlImagen;
            }
        }
//********************************************* FUNCIONES *********************************************
        public function verificarSaldo($saldo){
            return $this->getSaldo() > $saldo;
        }

        /**
         * Me permitira crear una cuenta nueva
         * y alojarla en la tabla 'cuentas'.
         * @param Cuenta $cuenta la cuenta 
         * a crear.
         */
        public static function crear($cuenta) {
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("INSERT INTO cuentas (saldo,estado,tipoCuenta,moneda,idUsuario,urlImagen)
            VALUES (:saldo,:estado,:tipoCuenta,:moneda,:idUsuario,:urlImagen)");
            $consulta->bindValue(':saldo', $cuenta->getSaldo(), PDO::PARAM_INT);
            $consulta->bindValue(':estado', true, PDO::PARAM_BOOL);//-->Comienza activa
            $consulta->bindValue(':idUsuario', $cuenta->getIdUsuario(), PDO::PARAM_INT);
            $consulta->bindValue(':tipoCuenta', $cuenta->getTipoCuenta(), PDO::PARAM_STR);
            $consulta->bindValue(':moneda', $cuenta->getMoneda(), PDO::PARAM_STR);
            $consulta->bindValue(':urlImagen', $cuenta->getUrlImagen(), PDO::PARAM_STR);

            $consulta->execute();
            return $objAccesoDB->retornarUltimoInsertado();
        }

        /**
         * Me permitira obtener todos los 
         * datos de la tabla 'cuentas'
         */
        public static function obtenerTodos() {
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT * FROM cuentas");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cuenta');
        }

        /**
         * Me permitira traerme un unico objeto
         * cuenta de la tabla cuentas mediante
         * la coincidencia del id/numeroCuenta.
         * 
         * @param int $idCuenta el id de la cuenta
         * a buscar.
         */
        public static function obtenerUno($idCuenta){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT idCuenta,apellido,nombre,tipoDocumento,numeroDocumento,saldo,
            estado, email,tipoCuenta,moneda,idUsuario,urlImagen FROM cuentas WHERE idCuenta = :idCuenta");
            $consulta->bindValue(':idCuenta', $idCuenta, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchObject('Cuenta');
        }

        /**
         * Me permitira obtener un usuario
         * por mail y nro de documento
         */
        public static function obtenerCuentaPorUsuario($email, $numeroDocumento) {
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("
                SELECT c.idCuenta, u.apellido, u.nombre, u.tipoDocumento, u.numeroDocumento, c.saldo,
                       c.estado, u.email, c.tipoCuenta, c.moneda, c.idUsuario
                FROM cuentas c
                JOIN usuarios u ON c.idUsuario = u.idUsuario
                WHERE u.email = :email AND u.numeroDocumento = :numeroDocumento
            ");
            $consulta->bindValue(':email', $email, PDO::PARAM_STR);
            $consulta->bindValue(':numeroDocumento', $numeroDocumento, PDO::PARAM_STR);
            $consulta->execute();
        
            return $consulta->fetchObject('Cuenta');
        }
        

        /**
         * Me permtira obtener una cuenta mediante
         * la coincidencia de su id y tipo de cuenta.
         */
        public static function ObtenerCuentaPorNroYTipo($nro,$tipo,$moneda){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT idCuenta,saldo,estado,tipoCuenta,moneda,idUsuario,urlImagen
            FROM cuentas WHERE idCuenta = :idCuenta AND tipoCuenta = :tipoCuenta AND moneda = :moneda");

            $consulta->bindValue(':idCuenta', $nro, PDO::PARAM_INT);
            $consulta->bindValue(':tipoCuenta', $tipo, PDO::PARAM_STR);
            $consulta->bindValue(':moneda', $moneda, PDO::PARAM_STR);
            $consulta->execute();

            return $consulta->fetchObject('Cuenta');
        }

        /**
         * Me permitira modificar los valores
         * de una cuenta.
         */
        public static function modificar($cuenta){
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("UPDATE cuentas SET saldo = :saldo, estado = :estado,
            tipoCuenta = :tipoCuenta,moneda = :moneda, idUsuario = :idUsuario,urlImagen = :urlImagen WHERE idCuenta = :idCuenta");
            $consulta->bindValue(':idCuenta', $cuenta->getIdCuenta(), PDO::PARAM_INT);
            $consulta->bindValue(':saldo', $cuenta->getSaldo(), PDO::PARAM_INT);
            $consulta->bindValue(':estado', $cuenta->getEstado(), PDO::PARAM_BOOL); 
            $consulta->bindValue(':tipoCuenta', $cuenta->getTipoCuenta(), PDO::PARAM_STR);
            $consulta->bindValue(':urlImagen', $cuenta->getUrlImagen(), PDO::PARAM_STR);
            $consulta->bindValue(':moneda', $cuenta->getMoneda(), PDO::PARAM_STR);
            $consulta->bindValue(':idUsuario', $cuenta->getIdUsuario(), PDO::PARAM_INT);
            return $consulta->execute();
        }

        /**
         * Me permitira hacer una baja logica de una
         * cuenta, se le cambia su estado a false.
         * 
         * @param int $idCuenta el id de la cuenta 
         * a dar de baja.
         */
	    public static function borrar($idCuenta){
            $objAccesoDato = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDato->retornarConsulta("UPDATE cuentas SET estado = :estado WHERE idCuenta = :idCuenta"); 
            $consulta->bindValue(':idCuenta', $idCuenta, PDO::PARAM_INT);
            $consulta->bindValue(':estado', false, PDO::PARAM_BOOL);
            return $consulta->execute();
        }

        /**
         * Me permitira verificar si existe una cuenta
         * mediante el numero de cuenta y el tipo
         * buscado.
         * 
         * #1: primero verifico si coincide le tipo y la cuenta
         * con alguna ya registrada, si sucede directamente
         * se retorna el mensaje.
         * 
         * #2: Caso dos busco que coincida el tipo
         * de cuenta, lo alojo en un array.
         * 
         * #3: Veo si existe el id y lo guardo en
         * otro array de cuentas.
         * 
         * #4: Verifico el resultado, si el array de
         * cuentas con tipo NO esta vacio pero si el
         * array de nro cuenta, quiere decir que hay coincidencia
         * del tipo pero no del nro de cuenta.
         * 
         * #5: Si existe el numero pero no hay de tipo.
         * 
         * #6: No hay cuentas con X tipo directamente.
         * 
         * #7: No hay coincidencia ni de tipo ni de 
         * nro de cuenta.
         */
        public static function ConsultarCuentaPorTipo($numeroCuenta,$tipo){
            $cuentas = Cuenta::obtenerTodos();
            $cuentasConTipo = [];
            $cuentasConNro = [];
            foreach($cuentas as $cuenta){
                //#1
                if($cuenta->getTipoCuenta() === $tipo && $cuenta->getIdCuenta() === $numeroCuenta){
                    return 'Si hay cuentas con tipo de cuenta: ' . $tipo . ' y numero: ' . $numeroCuenta . 
                    '<br> Su saldo es: $' . $cuenta->getSaldo() . ' y la moneda de ella es: ' . $cuenta->getMoneda();
                }
                if($cuenta->getTipoCuenta() === $tipo){//#2
                    $cuentasConTipo[] = $cuenta;
                }
                if($cuenta->getIdCuenta() === $numeroCuenta)//#3
                { $cuentasConNro[] = $cuenta;}
            }
            //#4
            if (!empty($cuentasConTipo) && empty($cuentasConNro)) {
                $msj = 'Si hay cuentas con tipo: ' . $tipo . ' pero el numero: ' . $numeroCuenta . ' no le pertenece';
            } elseif (!empty($cuentasConNro) && empty($cuentasConTipo)) {//#5
                $msj = 'Solo hay cuentas con el numero: ' . $numeroCuenta . ' pero no con el tipo: ' . $tipo;
            } elseif (!empty($cuentasConTipo)) {//#6
                $msj = 'No hay coincidencia de tipo de cuenta: ' . $tipo . ' y numero de cuenta: ' . $numeroCuenta;
            } else {//#7
                $msj = 'No existe la combinacion de numero y tipo de cuenta.';
            }
            
            return $msj;
        }
}
<?php
    require_once "./interfaces/ICrud.php";  
    
    class Cuenta implements ICrud{
//********************************************* ATRIBUTOS *********************************************
        public $idCuenta;
        public $tipoCuenta;//-->Se unifica tipo cuenta & moneda [CA$,CAU$S,CC$,CCU$S]
        public $saldo;
        public $estado;//-->Si esta activa (true), o no (false).
        public $moneda;
        public $nroDocumento;
        public $urlImagen;
        public $nombre;
        public $apellido;
        public $tipoDocumento;
        public $numeroDocumento;
        public $email;
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
        public function getNroDocumento(){
            return $this->nroDocumento;
        }
        public function getUrlImagen(){
            return $this->urlImagen;
        }
        public function getNombre(){
            return $this->nombre;
        }
        public function getApellido(){
            return $this->apellido;
        }
        public function getTipoDocumento(){
            return $this->tipoDocumento;
        }
        public function getNumeroDocumento(){
            return $this->numeroDocumento;
        }
        public function getEmail(){
            return $this->email;
        }
//********************************************* SETTERS *********************************************
        public function setID($id){
            if (isset($id) && is_numeric($id)){
                $this->idCuenta = $id;
            }
        }
        public function setNroDocumento($nroDocumento){
            if (isset($nroDocumento) && is_numeric($nroDocumento)){
                $this->nroDocumento = $nroDocumento;
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
        public function setNombre($nombre){
            if(isset($nombre) && !empty($nombre)) {
                $this->nombre = $nombre;
            }
        }
        public function setApellido($apellido){
            if(isset($apellido) && !empty($apellido)) {
                $this->apellido = $apellido;
            }
        }
        public function setTipoDocumento($tipoDocumento){
            if(isset($tipoDocumento) && !empty($tipoDocumento) && self::validarTipoDocumento($tipoDocumento)) {
                $this->tipoDocumento = $tipoDocumento;
            }
            else{
                echo 'tipo documento no valido se aceptan [DNI,LE,LC,CI], reingrese!<br>';
                exit;
            }
        } 
        public function setNumeroDocumento($numeroDocumento){
            if(isset($numeroDocumento) && !empty($numeroDocumento)) {
                $this->numeroDocumento = $numeroDocumento;
            }
        }
        public function setEmail($email){
            if(isset($email) && !empty($email)) {
                $this->email = $email;
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
            $consulta = $objAccesoDB->retornarConsulta("INSERT INTO cuentas (nombre, apellido, tipoDocumento, email, saldo, estado, tipoCuenta, moneda, nroDocumento, urlImagen)
            VALUES (:nombre, :apellido, :tipoDocumento, :email, :saldo, :estado, :tipoCuenta, :moneda, :nroDocumento, :urlImagen)");
            
            $consulta->bindValue(':nombre', $cuenta->getNombre(), PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $cuenta->getApellido(), PDO::PARAM_STR);
            $consulta->bindValue(':tipoDocumento', $cuenta->getTipoDocumento(), PDO::PARAM_STR);
            $consulta->bindValue(':nroDocumento', $cuenta->getNroDocumento(), PDO::PARAM_STR);

            $consulta->bindValue(':email', $cuenta->getEmail(), PDO::PARAM_STR);
            $consulta->bindValue(':saldo', $cuenta->getSaldo(), PDO::PARAM_INT);
            $consulta->bindValue(':estado', true, PDO::PARAM_BOOL);//-->Comienza activa
            $consulta->bindValue(':nroDocumento', $cuenta->getNroDocumento(), PDO::PARAM_STR);
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
         * Me permite validar un tipo de documeto
         * argentino:
         * Documento Nacional de Identidad -D.N.I. 
         * Libreta Cívica - L.C. 
         * Libreta de Enrolamiento - L.E. 
         * Cédula de Identidad -C.I.
         */
        private static function validarTipoDocumento($tipo){
            $tipos = ["DNI","LC","LE","CI"];
            if(in_array(strtoupper($tipo),$tipos))
                return true;
            else
                return false;
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
            $consulta = $objAccesoDB->retornarConsulta("SELECT idCuenta,saldo,
            estado,tipoCuenta,moneda,nroDocumento,urlImagen,nombre,apellido,email,tipoDocumento FROM cuentas WHERE idCuenta = :idCuenta");
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
                SELECT idCuenta, apellido, nombre, tipoDocumento, numeroDocumento, saldo,
                       estado, email, tipoCuenta, moneda, nroDocumento
                FROM 
                WHERE email = :email AND numeroDocumento = :numeroDocumento
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
            $consulta = $objAccesoDB->retornarConsulta("SELECT idCuenta,saldo,estado,tipoCuenta,moneda,nroDocumento,urlImagen
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
            tipoCuenta = :tipoCuenta,moneda = :moneda, nroDocumento = :nroDocumento,urlImagen = :urlImagen WHERE idCuenta = :idCuenta");
            $consulta->bindValue(':idCuenta', $cuenta->getIdCuenta(), PDO::PARAM_INT);
            $consulta->bindValue(':saldo', $cuenta->getSaldo(), PDO::PARAM_INT);
            $consulta->bindValue(':estado', $cuenta->getEstado(), PDO::PARAM_BOOL); 
            $consulta->bindValue(':tipoCuenta', $cuenta->getTipoCuenta(), PDO::PARAM_STR);
            $consulta->bindValue(':urlImagen', $cuenta->getUrlImagen(), PDO::PARAM_STR);
            $consulta->bindValue(':moneda', $cuenta->getMoneda(), PDO::PARAM_STR);
            $consulta->bindValue(':nroDocumento', $cuenta->getNroDocumento(), PDO::PARAM_INT);
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
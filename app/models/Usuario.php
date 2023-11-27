<?php

    class Usuario{
//********************************************* ATRIBUTOS *********************************************
        public $idUsuario;
        public $nombre;
        public $apellido;
        public $tipoDocumento;
        public $numeroDocumento;
        public $email;
        public $rol;
        public $clave;
//********************************************* GETTERS *********************************************
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
        public function getIdUsuario(){
            return $this->idUsuario;
        }
        public function getRol(){
            return $this->rol;
        }
        public function getClave(){
            return $this->clave;
        }
//********************************************* SETTERS *********************************************
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
        public function setRol($rol){
            if(isset($rol) && !empty($rol)) {
                $this->rol = $rol;
            }
        }
        public function setClave($clave){
            if(isset($clave) && !empty($clave)) {
                $this->clave = $clave;
            }
        }
//********************************************* FUNCIONES *********************************************
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

        public static function crear($usuario) {
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("INSERT INTO usuarios (nombre, apellido,tipoDocumento,numeroDocumento,
            email,rol,clave) VALUES (:nombre, :apellido,:tipoDocumento, :numeroDocumento,:email,:rol,:clave)");
            $consulta->bindValue(':nombre', $usuario->getNombre(), PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $usuario->getApellido(), PDO::PARAM_STR);
            $consulta->bindValue(':tipoDocumento', $usuario->getTipoDocumento(), PDO::PARAM_STR);
            $consulta->bindValue(':numeroDocumento', $usuario->getNumeroDocumento(), PDO::PARAM_STR);
            $consulta->bindValue(':email', $usuario->getEmail(), PDO::PARAM_STR);
            $consulta->bindValue(':rol', $usuario->getRol(), PDO::PARAM_STR);
            $consulta->bindValue(':clave', $usuario->getClave(), PDO::PARAM_STR);
            $consulta->execute();
            return $objAccesoDB->retornarUltimoInsertado();
        }

        public static function obtenerTodos() {
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT * FROM usuarios");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        }

        public static function obtenerUno($idUsuario){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT idUsuario,apellido,nombre,tipoDocumento,numeroDocumento,rol,
            email,rol,clave FROM usuarios WHERE idUsuario = :idUsuario");
            $consulta->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchObject('Usuario');
        }


        public static function obtenerUnoPorNroDocumento($numeroDocumento){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT idUsuario,apellido,nombre,tipoDocumento,numeroDocumento,rol,
            email,rol,clave FROM usuarios WHERE numeroDocumento = :numeroDocumento");
            $consulta->bindValue(':numeroDocumento', $numeroDocumento, PDO::PARAM_STR);
            $consulta->execute();

            return $consulta->fetchObject('Usuario');
        }

        public static function obtenerUsuarioMailClave($email,$clave){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT idUsuario,apellido,nombre,tipoDocumento,numeroDocumento,rol,
            email,rol,clave FROM usuarios WHERE email = :email AND clave = :clave");
            $consulta->bindValue(':email', $email, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
            $consulta->execute();

            return $consulta->fetchObject('Usuario');
        }

        public static function modificar($usuario){
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("UPDATE usuarios SET nombre = :nombre, apellido = :apellido,
            tipoDocumento = :tipoDocumento, numeroDocumento = :numeroDocumento,
            email = :email, rol = :rol, clave = :clave WHERE idUsuario = :idUsuario");
            $consulta->bindValue(':idUsuario', $usuario->getIdUsuario(), PDO::PARAM_INT);
            $consulta->bindValue(':nombre', $usuario->getNombre(), PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $usuario->getApellido(), PDO::PARAM_STR);
            $consulta->bindValue(':tipoDocumento', $usuario->getTipoDocumento(), PDO::PARAM_STR);
            $consulta->bindValue(':numeroDocumento', $usuario->getNumeroDocumento(), PDO::PARAM_STR);
            $consulta->bindValue(':email', $usuario->getEmail(), PDO::PARAM_STR);
            $consulta->bindValue(':rol', $usuario->getRol(), PDO::PARAM_STR); 
            $consulta->bindValue(':clave', $usuario->getClave(), PDO::PARAM_STR); 
            return $consulta->execute();
        }
    }
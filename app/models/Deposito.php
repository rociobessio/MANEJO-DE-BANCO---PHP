<?php
use Fpdf\Fpdf;
    require_once "./interfaces/ICrud.php";

    class Deposito implements ICrud{
//********************************************* ATRIBUTOS *********************************************
        public $idDeposito;
        public $numeroCuenta;
        public $tipoCuenta;
        public $importe;
        public $fechaDeposito;
        public $moneda;
        public $nroOperacion;
//********************************************* GETTERS *********************************************
        public function getIdDeposito(){
            return $this->idDeposito;
        }
        public function getNumeroCuenta(){
            return $this->numeroCuenta;
        }
        public function getTipoCuenta(){
            return $this->tipoCuenta;
        }
        public function getImporte(){
            return $this->importe;
        }
        public function getFechaDeposito(){
            return $this->fechaDeposito;
        }
        public function getMoneda(){
            return $this->moneda;
        }
        public function getNroOperacion(){
            return $this->nroOperacion;
        }
//********************************************* SETTERS *********************************************
        public function setIdDeposito($id){
            if (isset($id) && is_numeric($id)){
                $this->idDeposito = $id;
            }
        }
        public function setNumeroCuenta($numero){
            if (isset($numero) && is_numeric($numero)){
                $this->numeroCuenta = $numero;
            }
        }
        public function setTipoCuenta($tipo){
            if(isset($tipo) && !empty($tipo)) {
                $this->tipoCuenta = $tipo;
            }
        }
        public function setImporte($importe){
            if (isset($importe) && is_float($importe)){
                $this->importe= $importe;
            }
        }
        public function setFechaDeposito($fecha){
            if(isset($fecha) && !empty($fecha)) {
                $this->fechaDeposito = $fecha;
            }
        } 
        public function setMoneda($moneda){
            if(isset($moneda) && !empty($moneda)) {
                $this->moneda = $moneda;
            }
        }
        public function setNroOperacion($nroOperacion){
            if(isset($nroOperacion)) {
                $this->nroOperacion = $nroOperacion;
            }
        }
//********************************************* FUNCIONES *********************************************
        
        /**
         * El crear me permitira guardar una 
         * instancia de Retiro en la tabla
         * retiros.
         * @param Deposito $deposito el deposito a 
         * guardar.
         */
        public static function crear($deposito){
            $fechaDeposito = new DateTime(date("d-m-Y"));//-->Le asigno la fecha de deposito
            $accesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $accesoDB->retornarConsulta("INSERT INTO depositos (numeroCuenta,tipoCuenta,importe,fechaDeposito,moneda,nroOperacion)
            VALUES (:numeroCuenta,:tipoCuenta,:importe,:fechaDeposito,:moneda,:nroOperacion)");
            $consulta->bindValue(':numeroCuenta',$deposito->getNumeroCuenta(),PDO::PARAM_INT);
            $consulta->bindValue(':tipoCuenta',$deposito->getTipoCuenta(),PDO::PARAM_STR);
            $consulta->bindValue(':moneda',$deposito->getMoneda(),PDO::PARAM_STR);
            $consulta->bindValue(':importe',$deposito->getImporte(),PDO::PARAM_INT);
            $consulta->bindValue(':nroOperacion',$deposito->getNroOperacion(),PDO::PARAM_INT);
            $consulta->bindValue(':fechaDeposito',date_format($fechaDeposito, "Y-m-d"),PDO::PARAM_STR);
            $consulta->execute();
            return $accesoDB->retornarUltimoInsertado();
        }

        public static function obtenerTodos(){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT * FROM depositos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Deposito');
        }
        
        /**
         * Me permitira obtener un unico deposito de la
         * tabla depositos
         */
        public static function obtenerUno($idDeposito){
            $objAccesoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDB->retornarConsulta("SELECT idDeposito,numeroCuenta,tipoCuenta,importe,fechaDeposito,moneda,nroOperacion
            FROM depositos WHERE idDeposito = :idDeposito");
            $consulta->bindValue(':idDeposito', $idDeposito, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchObject('Deposito');
        }
        
        public static function modificar($deposito){
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("UPDATE depositos SET importe = :importe,
            tipoCuenta = :tipoCuenta, numeroCuenta = :numeroCuenta,fechaDeposito = :fechaDeposito, moneda = :moneda
            WHERE idDeposito = :idDeposito");
            $consulta->bindValue(':idDeposito',$deposito->getIdDeposito(),PDO::PARAM_INT);
            $consulta->bindValue(':numeroCuenta',$deposito->getNumeroCuenta(),PDO::PARAM_INT);
            $consulta->bindValue(':tipoCuenta',$deposito->getTipoCuenta(),PDO::PARAM_STR);
            $consulta->bindValue(':moneda',$deposito->getMoneda(),PDO::PARAM_STR);
            $consulta->bindValue(':importe',$deposito->getImporte(),PDO::PARAM_INT);
            $consulta->bindValue(':fechaDeposito',$deposito->getFechaDeposito(),PDO::PARAM_STR);
            $consulta->execute();
        }
        
        public static function borrar($idDeposito){
            $objAccesoDato = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDato->retornarConsulta("DELETE FROM depositos WHERE idDeposito = :idDeposito"); 
            $consulta->bindValue(':idDeposito', $idDeposito, PDO::PARAM_INT);
        
            return $consulta->execute();
        }

        public function verificarImporte($valor){
            return $this->getImporte() >= $valor;
        }

        /**
         * El total depositado (monto) por tipo de cuenta y moneda en un día en
         * particular (se envía por parámetro), si no se pasa fecha, se muestran las del día
         * anterior.
         */
        public static function calcularTotalDepositos($tipoCuenta, $fecha = null){
            $retorno = false;
            $objAccesoDato = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccesoDato->retornarConsulta("
                SELECT
                    tipoCuenta,
                    SUM(importe) as totalDepositado
                FROM
                    depositos
                WHERE
                    fechaDeposito = COALESCE(:fecha, DATE_SUB(CURDATE(), INTERVAL 1 DAY)) 
                    AND tipoCuenta = :tipoCuenta
                GROUP BY
                    tipoCuenta;
            "); 
            $consulta->bindParam(':fecha', $fecha, PDO::PARAM_STR); 
            $consulta->bindParam(':tipoCuenta', $tipoCuenta, PDO::PARAM_STR);
            $consulta->execute();
            
            while ($resultado = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $retorno = true;
                echo 'El monto total depositado con tipo de cuenta: ' . $resultado['tipoCuenta'] . ' en la fecha: ' . $fecha . '<br>' . 
                     'es: ' .  $resultado['totalDepositado'] . '<br>';
            } 
            return $retorno;
        }
        
        /**
         * El listado de depósitos para un usuario en particular.
         * Busco en depositos primero el numero de cuenta, luego
         * con innerjoin lo busco en la tabla cuentas y verifico
         * si el mail que me pasaron coincide con el de la cuenta
         * que realizo el deposito.
         * 
         * @param string $emailUsuario el mail del usuario a buscar.
         */
        public static function DepositosUsuarioParticular($emailUsuario){
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("
                SELECT depositos.*
                FROM depositos
                INNER JOIN cuentas ON depositos.numeroCuenta = cuentas.idCuenta
                WHERE cuentas.email = :emailUsuario
            ");
        
            $valorEmailUsuario = $emailUsuario ?? '';
        
            $consulta->bindParam(':emailUsuario', $valorEmailUsuario);
            $consulta->execute();
        
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }
        

        /**
         * c- El listado de depósitos entre dos fechas ordenado por nombre.
         */
        public static function DepositosEntreFechasOrdenadosPorNombre($fechaInicio, $fechaFin){
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("
                SELECT
                    cuentas.nombre AS nombreUsuario,
                    cuentas.apellido AS apellidoUsuario,
                    depositos.idDeposito,
                    depositos.numeroCuenta,
                    depositos.tipoCuenta,
                    depositos.importe,
                    depositos.fechaDeposito
                FROM
                    depositos
                INNER JOIN
                    cuentas ON depositos.numeroCuenta = cuentas.idCuenta 
                WHERE
                    depositos.fechaDeposito BETWEEN :fechaInicio AND :fechaFin
                ORDER BY
                    cuentas.nombre, cuentas.apellido, depositos.numeroCuenta, depositos.fechaDeposito;
            ");
            
            $consulta->bindParam(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
            $consulta->bindParam(':fechaFin', $fechaFin, PDO::PARAM_STR);
            $consulta->execute();
            
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }
        

        /**
         * El listado de depósitos por tipo de cuenta.
         */
        public static function DepositosPorTipoCuenta($tipoCuenta){
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("SELECT * FROM depositos
            WHERE tipoCuenta = :tipoCuenta");

            $consulta->bindParam(':tipoCuenta', $tipoCuenta, PDO::PARAM_STR);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function DepositosPorTipoMoneda($moneda){
            $objAccessoDB = AccesoDatos::obtenerObjetoAcceso();
            $consulta = $objAccessoDB->retornarConsulta("
            SELECT depositos.*
            FROM depositos
            INNER JOIN cuentas ON depositos.numeroCuenta = cuentas.idCuenta
            WHERE cuentas.moneda = :moneda
            "); 

            $consulta->bindParam(':moneda', $moneda);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }
        

        public static function DescargarPDF($directorio, $cantDepositos)
        {
            $depositos = self::obtenerTodos();

            if ($depositos) {
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0777, true);
                }

                $pdf = new FPDF();
                $pdf->AddPage();

                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Cell(0, 10, 'Segundo Parcial', 0, 1, 'L');
                $pdf->Cell(60, 0, '', 'T');
                $pdf->Ln(5);

                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 10, 'Depositos', 0, 1, 'L');
                $pdf->Ln(5);

                $header = array('ID', 'Nro. Cuenta', 'Tipo Cuenta', 'Importe', 'Fecha Deposito', 'Moneda', 'Nro. Operacion');
                $w = array(10, 30, 30, 25, 40, 20, 35);
                
                //-->Estilo del header
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetFillColor(210, 200, 225);//-->CoOlor del header
                for ($i = 0; $i < count($header); $i++) {
                    $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
                }
                $pdf->Ln();
                
                //Estilo de la row
                $pdf->SetFont('Arial', '', 10);
                $fill = false;
                foreach ($depositos as $deposito) {//-->Relleno
                    for ($i = 0; $i < count($header); $i++) {
                        $pdf->Cell($w[0], 6, $deposito->getIdDeposito(), 'LR', 0, 'C', $fill);
                        $pdf->Cell($w[1], 6, $deposito->getNumeroCuenta(), 'LR', 0, 'C', $fill);
                        $pdf->Cell($w[2], 6, $deposito->getTipoCuenta(), 'LR', 0, 'C', $fill);
                        $pdf->Cell($w[3], 6, $deposito->getImporte(), 'LR', 0, 'C', $fill);
                        $pdf->Cell($w[4], 6, $deposito->getFechaDeposito(), 'LR', 0, 'C', $fill);
                        $pdf->Cell($w[5], 6, $deposito->getMoneda(), 'LR', 0, 'C', $fill);
                        $pdf->Cell($w[6], 6, $deposito->getNroOperacion(), 'LR', 0, 'C', $fill);
                    }
                    $pdf->Ln();
                    $fill = !$fill;
                }
                $newFilename = $directorio . 'Depositos_' . date('Y_m_d') . '.pdf';
                $pdf->Output('F', $newFilename, 'I');

                $payload = json_encode(array("message" => 'PDF creado: ' . $newFilename));
            } else {
                $payload = json_encode(array("error" => 'No se pudo realizar el PDF'));
            }

            return $payload;
        }
    }


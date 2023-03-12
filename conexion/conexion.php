<?php
class ApptivaDB {
    private $host = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $db = "pedidossiPueden";
    public $conexion;
    
    public function __construct(){
        $this->conexion = new mysqli($this->host, $this->usuario, $this->clave, $this->db)
        or die(mysql_error());
        $this->conexion->set_charset("utf8");
    }

    public function insertar($tabla, $datos) {
        try {
            $resultado = $this->conexion->query("INSERT INTO $tabla VALUES(null, $datos)") or die();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

}

?>
<?php
class ApptivaDB {
    // private $host = "localhost";
    // private $usuario = "root";
    // private $clave = "";
    // private $db = "pedidossipueden";
    // public $conexion;

    private $host = "localhost";
    private $usuario = "fundaci_pedidos";
    private $clave = "pedidos.1379";
    private $db = "fundaci_pedidos";
    public $conexion;


    // $dsn = "mysql:dbname=fundaci_pedidos; host=localhost; port=21";
    // $usuario = "fundaci_pedidos";
    // $pass = "pedidos.1379";
    
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
            // return $th;
            return false;
        }
    }

}

?>
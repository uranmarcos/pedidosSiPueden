<?php
class ApptivaDB {
    private $host = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $db = "pedidossipueden";
    public $conexion;

    // private $host = "localhost";
    // private $usuario = "fundaci_pedidos";
    // private $clave = "pedidos.1379";
    // private $db = "fundaci_pedidos";
    // public $conexion;
    
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

    public function consultarLibros() {
        try {
            $resultado = $this->conexion->query("SELECT * FROM libros") or die();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function consultar($tabla, $condicion) {
        try {
            $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion") or die();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function eliminar($tabla, $condicion) {
        try {
            $resultado = $this->conexion->query("DELETE FROM $tabla WHERE $condicion") or die();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }


    public function buscarPorCategoria($idCategoria) {
        try {
            if ($idCategoria == 0) {
                $resultado = $this->conexion->query("SELECT * FROM libros") or die();
            } else {
                $resultado = $this->conexion->query("SELECT * FROM libros WHERE categoria = $idCategoria") or die();
            }
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function consultarcategorias() {
        try {
            $resultado = $this->conexion->query("SELECT * FROM categoriaslibros ORDER BY nombre ASC") or die();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function crearCategoria($data) {
        try {
            $resultado = $this->conexion->query($data) or die();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}

?>
<?php
class ApptivaDB {
    private $host = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $db = "vue";
    public $conexion;
    
    public function __construct(){
        $this->conexion = new mysqli($this->host, $this->usuario, $this->clave, $this->db)
        or die(mysql_error());
        $this->conexion->set_charset("utf8");
    }

    public function insertar($tabla, $datos) {
        // $resultado = $this->conexion->query("INSERT INTO $tabla VALUES(null, $datos)") or die($this->conexion->error);
        // if ($resultado) {
        //     return true;
        // }
        // return false;
        try {
            $resultado = $this->conexion->query("INSERT INTO $tabla VALUES(null, $datos)") or die();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function consultar($tabla, $condicion) {
        // $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion") or die($this->conexion->error);
        // if ($resultado) 
        //     return $resultado->fetch_all(MYSQLI_ASSOC);
        // return false;
        try {
            $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion") or die();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function consultarPedidos($tabla, $condicion) {
        try {
            $resultado = $this->conexion->query("SELECT P.pedido, P.fecha, P.enviado,
            CONCAT(S.localidad, ' - ', S.provincia) sede,
            CONCAT(U.nombre, ' ', U.segundoNombre, ' ', U.apellido, ' ', U.segundoApellido) usuario
            FROM pedidos P 
            LEFT JOIN sedes S ON P.sede = S.id
            LEFT JOIN usuarios U ON P.usuario = U.id
            WHERE $condicion
            ORDER BY fecha DESC") or die();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function actualizar($tabla, $campos, $condicion) {
        try {
            $resultado = $this->conexion->query("UPDATE $tabla SET $campos WHERE $condicion") or die();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function hayRegistro($tabla, $condicion) {
        try {
            $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion") or die();
            $resultado = $resultado->fetch_all(MYSQLI_ASSOC);
            $numero = count($resultado);
            return $numero;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function eliminar($tabla, $condicion) {
        // $resultado = $this->conexion->query("DELETE FROM $tabla WHERE $condicion") or die($this->conexion->error);
        // if ($resultado) {
        //     return true;
        // }
        // return false;

        try {
            $resultado = $this->conexion->query("DELETE FROM $tabla WHERE $condicion") or die();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }
    // public function insertar($tabla, $datos) {
    //     $resultado = $this->conexion->query("INSERT INTO $tabla VALUES(null, $datos)") or die($this->conexion->error);
    //     if ($resultado) {
    //         return true;
    //     }
    //     return false;
    // }
    public function login($usuario, $password) {
        try {
            $resultado = $this->conexion->query("SELECT * FROM usuarios WHERE dni = $usuario;") or die();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function borrar($tabla, $condicion) {
        $resultado = $this->conexion->query("DELETE FROM $tabla WHERE $condicion") or die($this->conexion->error);
        if ($resultado) {
            return true;
        }
        return false;
    }

    // public function actualizar($tabla, $campos, $condicion) {
    //     $resultado = $this->conexion->query("UPDATE $tabla SET $campos WHERE $condicion") or die($this->conexion->error);
    //     if ($resultado) {
    //         return true;
    //     }
    //     return false;
    // }

    public function buscar($tabla, $condicion) {
        $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion") or die($this->conexion->error);
        if ($resultado) 
            return $resultado->fetch_all(MYSQLI_ASSOC);
        return false;
    }
}
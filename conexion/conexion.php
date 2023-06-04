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

    public function getObjetos($tabla, $tipo, $idCategoria, $buscador, $inicio) {
        try {

            if ($tipo == "planificaciones") {
                if ($idCategoria == 0) {
                    $resultado = $this->conexion->query("SELECT id, nombre, categoria, descripcion FROM $tabla WHERE tipo = 'planificaciones' ORDER BY nombre limit 5 offset $inicio") or die();
                } else {
                    $condicion = '%-' . $idCategoria . '-%';
                    $resultado = $this->conexion->query("SELECT id, nombre, categoria, descripcion FROM $tabla WHERE tipo = 'planificaciones' AND categoria LIKE '$condicion' ORDER BY nombre limit 5 offset $inicio") or die();
                }
            } else {
                if ($buscador != "") {
                    $busqueda = '%' . $buscador . '%';
                    if ($idCategoria == 0) {
                        $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' AND nombre LIKE '$busqueda' ORDER BY nombre limit 6 offset $inicio") or die();
                    } else {
                        $condicion = '%-' . $idCategoria . '-%';
                        $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' AND categoria LIKE '$condicion' AND nombre LIKE '$busqueda' ORDER BY nombre limit 6 offset $inicio") or die();
                    }
                } else {
                    if ($idCategoria == 0) {
                        $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' ORDER BY nombre limit 6 offset $inicio") or die();
                    } else {
                        $condicion = '%-' . $idCategoria . '-%';
                        $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' AND categoria LIKE '$condicion' ORDER BY nombre limit 6 offset $inicio") or die();
                    }
                }
            }
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function contarObjetos($idCategoria, $buscador, $tipo) {
        try {
            if ($buscador != "") {
                $busqueda = '%' . $buscador . '%';
                if ($idCategoria == 0) {
                    $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = '$tipo' AND nombre LIKE '$busqueda'") or die();
                } else {
                    $condicion = '%-' . $idCategoria . '-%';
                    $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = '$tipo' AND categoria AND nombre LIKE '$busqueda' LIKE '$condicion'") or die();
                }
            } else {
                if ($idCategoria == 0) {
                    $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = '$tipo'") or die();
                } else {
                    $condicion = '%-' . $idCategoria . '-%';
                    $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = '$tipo' AND categoria LIKE '$condicion'") or die();
                }
            }
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function verPlanificacion($tabla, $condcion) {
        try {
            $resultado = $this->conexion->query("SELECT archivo FROM $tabla WHERE $condcion") or die();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function consultarCategorias($tabla, $condicion) {
        try {
            $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion ORDER BY nombre ASC") or die();
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

    // lo uso para crear categorias y pedidos
    public function insertar($tabla, $datos) {
        try {
            $resultado = $this->conexion->query("INSERT INTO $tabla VALUES(null, $datos)") or die();
            //return true;

            if ($resultado === false) {
                return false; // Error al ejecutar la sentencia SQL
            }
    
            $idInsertado = $this->conexion->insert_id;
    
            if ($idInsertado > 0) {
                return $idInsertado; // Devuelve el ID de la instancia insertada
            } else {
                return false; // No se insertó ninguna instancia o no se obtuvo el ID
            }
        } catch (\Throwable $th) {
            // return $th;
            return false;
        }
    }

    public function getPedidos() {
        try {
            $resultado = $this->conexion->query("SELECT id, voluntario, merendero, provincia, fecha, destino FROM sipueden
            WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 60 DAY) ORDER BY fecha DESC ") or die();

            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function verPedido($id) {
        try {
            $resultado = $this->conexion->query("SELECT fecha, direccion, ciudad, provincia,
            codigoPostal, telefono, pedido, voluntario, merendero, destino FROM sipueden WHERE id = '$id'") or die();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function consultarLimpieza() {
        try {
            $resultado = $this->conexion->query("SELECT COUNT(*) cantidad
            FROM sipueden
            WHERE fecha < DATE_SUB(CURDATE(), INTERVAL 60 DAY);") or die();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function limpiarPedidos() {
        try {
            $resultado = $this->conexion->query("DELETE FROM sipueden WHERE fecha < DATE_SUB(CURDATE(), INTERVAL 61 DAY);") or die();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

}

?>
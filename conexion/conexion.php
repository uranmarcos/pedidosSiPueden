<?php
class ApptivaDB {
    private $host = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $db = "pedidossipueden";
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
            // return $th;
            return false;
        }
    }

    // public function consultar($tabla, $condicion) {
    //     try {
    //         $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion ORDER BY nombre ASC") or die();
    //         return $resultado->fetch_all(MYSQLI_ASSOC);
    //     } catch (\Throwable $th) {
    //         return false;
    //     }
    // }



    public function eliminar($tabla, $condicion) {
        try {
            $resultado = $this->conexion->query("DELETE FROM $tabla WHERE $condicion") or die();
            return true;
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


    public function consultar($tabla, $tipo, $idCategoria, $inicio) {
        try {
            if ($idCategoria == 0) {
                $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' ORDER BY nombre limit 5 offset $inicio") or die();
            } else {
                $condicion = '%-' . $idCategoria . '-%';
                $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' AND categoria LIKE '$condicion' ORDER BY nombre limit 5 offset $inicio") or die();
            }
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function consultarLibros($tabla, $tipo, $idCategoria, $buscador, $inicio) {
        try {
            if ($buscador != "") {
                $busqueda = '%' . $buscador . '%';
                if ($idCategoria == 0) {
                    $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' AND nombre LIKE '$busqueda' ORDER BY nombre limit 5 offset $inicio") or die();
                } else {
                    $condicion = '%-' . $idCategoria . '-%';
                    $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' AND categoria LIKE '$condicion' AND nombre LIKE '$busqueda' ORDER BY nombre limit 5 offset $inicio") or die();
                }
            } else {
                if ($idCategoria == 0) {
                    $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' ORDER BY nombre limit 5 offset $inicio") or die();
                } else {
                    $condicion = '%-' . $idCategoria . '-%';
                    $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' AND categoria LIKE '$condicion' ORDER BY nombre limit 5 offset $inicio") or die();
                }
            }
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function contarLibros($idCategoria, $buscador) {
        try {
            if ($buscador != "") {
                $busqueda = '%' . $buscador . '%';
                if ($idCategoria == 0) {
                    // $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' AND nombre LIKE '$busqueda' ORDER BY nombre limit 5 offset $inicio") or die();
                    $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = 'libro' AND nombre LIKE '$busqueda'") or die();
                } else {
                    $condicion = '%-' . $idCategoria . '-%';
                    // $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE tipo = '$tipo' AND categoria LIKE '$condicion' AND nombre LIKE '$busqueda' ORDER BY nombre limit 5 offset $inicio") or die();
                    $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = 'libro' AND categoria AND nombre LIKE '$busqueda' LIKE '$condicion'") or die();
                }
            } else {
                if ($idCategoria == 0) {
                    $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = 'libro'") or die();
                } else {
                    $condicion = '%-' . $idCategoria . '-%';
                    $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = 'libro' AND categoria LIKE '$condicion'") or die();
                }
            }


            // if ($idCategoria == 0) {
            //     $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = 'libro'") or die();
            // } else {
            //     $condicion = '%-' . $idCategoria . '-%';
            //     $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = 'libro' AND categoria LIKE '$condicion'") or die();
            // }
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function consultarPlanificaciones($tabla, $idCategoria, $inicio) {
        try {
            if ($idCategoria == 0) {
                $resultado = $this->conexion->query("SELECT id, nombre, categoria, descripcion FROM $tabla WHERE tipo = 'planificaciones' ORDER BY nombre limit 5 offset $inicio") or die();
            } else {
                $condicion = '%-' . $idCategoria . '-%';
                $resultado = $this->conexion->query("SELECT id, nombre, categoria, descripcion FROM $tabla WHERE tipo = 'planificaciones' AND categoria LIKE '$condicion' ORDER BY nombre limit 5 offset $inicio") or die();
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

    public function buscarPorCategoria($idCategoria) {
        try {
            if ($idCategoria == 0) {
                $resultado = $this->conexion->query("SELECT * FROM recursos WHERE tipo = 'libro'") or die();
            } else {
                $condicion = '%-' . $idCategoria . '-%';
                // $resultado = $this->conexion->query("SELECT * FROM libros WHERE categoria = $idCategoria") or die();
                $resultado = $this->conexion->query("SELECT * FROM recursos WHERE tipo = 'libro' AND categoria LIKE '$condicion'") or die();
            }
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function contarRecursos($idCategoria) {
        try {
            if ($idCategoria == 0) {
                $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = 'recurso'") or die();
            } else {
                $condicion = '%-' . $idCategoria . '-%';
                $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = 'recurso' AND categoria LIKE '$condicion'") or die();
            }
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function contarPlanificaciones($idCategoria) {
        try {
            if ($idCategoria == 0) {
                $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = 'planificaciones'") or die();
            } else {
                $condicion = '%-' . $idCategoria . '-%';
                $resultado = $this->conexion->query("SELECT COUNT(*) total FROM recursos WHERE tipo = 'planificaciones' AND categoria LIKE '$condicion'") or die();
            }
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

}

?>
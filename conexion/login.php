<?php
session_start();
$_SESSION["login"] = false;
    $accion = "mostrar";
    $res = array("error" => false);
    
    if (isset($_GET["accion"])) {
        $accion = $_GET["accion"];
    }


    switch ($accion) {
        case 'login':
            $usuario    = $_POST["usuario"];
            $password   = $_POST["password"];     
            
            if ($usuario == "sipueden@fundacionsi.org.ar" && $password == 30712506829) {
                $_SESSION["login"] = true;
                $mensaje = "Login ok";
                $res["mensaje"] = $mensaje;
                $res["error"] = false;
            }  else {
                if ($usuario != 'sipueden@fundacionsi.org.ar' && $password != 30712506829) {
                    $error = "Los datos ingresados son incorrectos";
                    $res["mensaje"] = $error;
                    $res["error"] = true;
                    break;
                }  else if ($usuario != 'sipueden@fundacionsi.org.ar' && $password == 30712506829) {
                    $error = "El usuario ingresado es incorrecto";
                    $res["mensaje"] = $error;
                    $res["error"] = true;
                    break;
                }  else if ($usuario == 'sipueden@fundacionsi.org.ar' && $password != 30712506829) {
                    $error = "La contraseña ingresada es incorrecta";
                    $res["mensaje"] = $error;
                    $res["error"] = true;
                    break;
                }  

            }  
        break;

        default:
            # code...
            break;
    }

    echo json_encode($res);
?>
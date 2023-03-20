<?php
session_start();
    $_SESSION["login"] = false;
    $_SESSION["rol"] = null;
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
                $_SESSION["rol"] = "usuario";
                $_SESSION['login_time'] = time();
                $mensaje = "Login ok";
                $token = sha1("usuario", false);
                $res["mensaje"] = $mensaje;
                $res["error"] = false;
                $res["token"] = $token;
            }  else if (($usuario == "marcos@fundacionsi.org.ar" && $password == 30971843) || 
                ($usuario == "manuel@fundacionsi.org.ar" && $password == 30827879)) {
                    $_SESSION["login"] = true;
                    $_SESSION["rol"] = "admin";
                    $_SESSION['login_time'] = time();
                    $mensaje = "Login ok";
                    $token = sha1("admin", false);
                    $res["mensaje"] = $mensaje;
                    $res["error"] = false;
                    $res["token"] = $token;
            }  else {
                if ($usuario != 'sipueden@fundacionsi.org.ar' && $password != 30712506829) {
                    $error = "Los datos ingresados son incorrectos";
                    $res["mensaje"] = $error;
                    $res["error"] = true;
                    $res["usuario"] = $usuario;
                    $res["pas"] = $password;
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
<?php
    $accion = "mostrar";
    $res = array("error" => false);
    
    if (isset($_GET["accion"])) {
        $accion = $_GET["accion"];
    }


    switch ($accion) {
        case 'login':
            $usuario    = $_POST["usuario"];
            $password   = $_POST["password"];     
            
            if ($usuario == "sipueden" && $password == 123456) {
                $mensaje = "login ok";
                $res["mensaje"] = $mensaje;
                $res["error"] = false;
            }  else {
                if ($usuario != 'sipueden' && $password != 123456) {
                    $error = "Los datos ingresados son incorrectos";
                    $res["mensaje"] = $error;
                    $res["error"] = true;
                    break;
                }  else if ($usuario != 'sipueden' && $password == 123456) {
                    $error = "El usuario ingresado es incorrecto";
                    $res["mensaje"] = $error;
                    $res["error"] = true;
                    break;
                }  else if ($usuario == 'sipueden' && $password != 123456) {
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
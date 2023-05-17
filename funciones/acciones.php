<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require "../src/Exception.php";
    require "../src/PHPMailer.php";
    require "../src/SMTP.php";
    require("../conexion/conexion.php");
    $user = new ApptivaDB();

    $accion = "mostrar";
    $res = array("error" => false);
    $archivoPdf = null;
    
    if (isset($_GET["accion"])) {
        $accion = $_GET["accion"];
    }


    switch ($accion) {
        case 'enviarPedido':
            require("pdf.php");
          
            $nombreSiPueden     = $_POST["nombreSiPueden"]; 
            $nombreVoluntario   = $_POST["nombreVoluntario"]; 
            $direccionEnvio     = $_POST["direccionEnvio"]; 
            $ciudad             = $_POST["ciudad"];  
            $provincia          = $_POST["provincia"]; 
            $codigoPostal       = $_POST["codigoPostal"];
            $telefono           = $_POST["telefono"];
            $fecha              = $_POST["fecha"];
            $mail               = $_POST["mail"];
            $mailCopia          = $_POST["mailCopia"];
            $pedido             = $_POST["pedido"];
            $pedidoTabla        = explode(';', $pedido);
            $otros              = $_POST["otros"];

            $pedidoBase = $pedido . ", otros : " . $otros;
            date_default_timezone_set('America/Argentina/Cordoba');
            $date = date("Y-m-d H:i:s");

            $data = "'" . $date . "', '" . $direccionEnvio . "', '" . $ciudad . "', '" . $provincia . "', '" . $codigoPostal . "', '" . $telefono . "', '" . $pedidoBase . "', '" . $nombreVoluntario . "', '" . $nombreSiPueden . "'";
            // local:
            // $u = $user -> insertar("pedidos", $data);
            // prod
            $u = $user -> insertar("sipueden", $data);
         
            if ($u == false) { 
                $res["mensaje"] = "El pedido no pudo realizarse";
                // $res["mensaje"] = $u;
                $res["error"] = true;
               
            } else {
                $otrosFormateado = ""; 
                if ($otros != null) {
                    $cantidadRenglones = ceil(strlen($otros) / 95);
                    for ($i = 0; $i < $cantidadRenglones; $i++) {
                        $inicial = 95 * $i;
                        $final = 95;
                        if($final > strlen($otros)){
                            $final = strlen($otros);
                        }
                        $string = substr($otros, $inicial, $final) . "\n";  
                        $otrosFormateado = $otrosFormateado . $string;
                    }
                }
    
    
                try {
                    $pdf = new PDF();
                    $pdf->AliasNbPages();
                    $header = array('Listado de articulos pedidos');
                    $pdf->AddPage();
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(0,10,$fecha,0,1,'R');
                    $pdf->SetFont('Arial','B',12);
                    $pdf->Cell(0,5,"Nuevo pedido de " . utf8_decode($nombreSiPueden),0,1,'C');
                    
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetTextColor(255,255,255);
                    $pdf->Cell(0,10,'Datos de envio: ',0,1, 'L', true);
                    // $pdf->Cell(0,10,'Datos de envio: ',0,1);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Arial','',10);
                    $pdf->Cell(0,10, utf8_decode("Voluntario: ") . utf8_decode($nombreVoluntario), 0,1);
                    $pdf->Cell(0,10, utf8_decode("Dirección: ") . utf8_decode($direccionEnvio) . ", " . utf8_decode($ciudad) . ", " . utf8_decode($provincia), 0,1);
                   
                    $pdf->Cell(0,10, utf8_decode('Código postal: ') . utf8_decode($codigoPostal),0,1);
                    $pdf->Cell(0,10, utf8_decode('Teléfono: ') . utf8_decode($telefono),0,1);
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetTextColor(255,255,255);
                    $pdf->Cell(0,10,'Articulos pedidos: ',0,1, 'L', true);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Arial','',10);
                    $pdf->TablaSimple($header, $pedidoTabla);
    
                    foreach ($pedidoTabla as $key => $value) {
                        $pdf->SetFont('Arial','',10);
                        $pdf->Cell(0,10, utf8_decode($value),1,1);
                    }
                    
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    if($otrosFormateado != '' && $otrosFormateado != null) {
                        $pdf->Cell(0,10,'Otros: ',0,1);
                        $pdf->SetFont('Arial','',10);
                        $pdf->Multicell(190,10,utf8_decode($otrosFormateado),1);
                    }

                    $archivoPdf = $pdf->Output('','S');                  
    
                    $email_user = "pedidosresidencias@hotmail.com";
                    $email_password = "pedidos.1379";
    
                    $the_subject = "Nuevo pedido de " . utf8_decode($nombreSiPueden);
                    $address_to = $mail;
                    $from_name = "Si Pueden";
                    $phpmailer = new PHPMailer();
                    // ———- datos de la cuenta de Gmail ——————————-
                    $phpmailer->Username = $email_user;
                    $phpmailer->Password = $email_password; 
                    
                    $phpmailer->Host = "smtp.office365.com"; // GMail
                    $phpmailer->SMTPSecure = 'STARTTLS';
                    
                    $phpmailer->Port = 587;
                    $phpmailer->IsSMTP(); // use SMTP
                    $phpmailer->SMTPAuth = true;
                    $phpmailer->setFrom($phpmailer->Username,$from_name);
                    $phpmailer->AddAddress($address_to); // recipients email
                    if ($mailCopia) {
                        $phpmailer->AddBCC($mail);
                        $address_to = $mailCopia;
                    }
                    $phpmailer->Subject = $the_subject;	
    
                    $phpmailer->Body .="<p>Nuevo pedido de </p>" . utf8_decode($nombreSiPueden) . " - ";
                    $phpmailer->Body .= utf8_decode($ciudad) . ", " . utf8_decode($provincia);
                    $phpmailer->Body .= "<p>Fecha: " . $fecha ."</p>";
                    $phpmailer->IsHTML(true);
                    $phpmailer->AddStringAttachment($archivoPdf, utf8_decode($nombreSiPueden) . '.pdf','base64');
                    try {
                        $phpmailer->smtpConnect([
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            ]
                        ]);
                        $mensaje = "Pedido enviado correctamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = false;
                    } catch (\Throwable $th) {
                        $mensaje = "El pedido no se pudo enviar. Intente nuevamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = true;
                        die;
                    }
                    if(!$phpmailer->send()) { 
                        $mensaje = "El pedido no se pudo enviar. Intente nuevamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = true;
                        die;
                    }
                } catch (\Throwable $th) {
                    $mensaje = "Hubo un error al enviar el pedido. Intente nuevamente";
                    $res["mensaje"] = $mensaje;
                    $res["error"] = true;
                }
            }

        break;

        case 'enviarPedidoLibros':
            require("pdf.php");
          
            $nombreSiPueden     = $_POST["nombreSiPueden"]; 
            $nombreVoluntario   = $_POST["nombreVoluntario"]; 
            $direccionEnvio     = $_POST["direccionEnvio"]; 
            $ciudad             = $_POST["ciudad"];  
            $provincia          = $_POST["provincia"]; 
            $codigoPostal       = $_POST["codigoPostal"];
            $telefono           = $_POST["telefono"];
            $fecha              = $_POST["fecha"];
            $mail               = $_POST["mail"];
            $mailCopia          = $_POST["mailCopia"];
            $pedido             = $_POST["pedido"];
            $pedidoTabla        = explode(';', $pedido);
            $pedidoVacio        = [];

            date_default_timezone_set('America/Argentina/Cordoba');
            $date = date("Y-m-d H:i:s");

            $data = "'" . $date . "', '" . $direccionEnvio . "', '" . $ciudad . "', '" . $provincia . "', '" . $codigoPostal . "', '" . $telefono . "', '" . $pedido . "', '" . $nombreVoluntario . "', '" . $nombreSiPueden . "'";
            // local:
            // $u = $user -> insertar("pedidos", $data);
            // prod
            $u = $user -> insertar("sipueden", $data);
         
            if ($u == false) { 
                $res["mensaje"] = "El pedido no pudo realizarse";
                $res["error"] = true;
                          
            } else {   
    
                try {
                    $pdf = new PDF();
                    $pdf->AliasNbPages();
                    $header = array('Listado de libros pedidos');
                    $pdf->AddPage();
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(0,10,$fecha,0,1,'R');
                    $pdf->SetFont('Arial','B',12);
                    $pdf->Cell(0,5,"Nuevo pedido de " . utf8_decode($nombreSiPueden),0,1,'C');
                    
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetTextColor(255,255,255);
                    $pdf->Cell(0,10,'Datos de envio: ',0,1, 'L', true);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Arial','',10);
                    $pdf->Cell(0,10, utf8_decode("Voluntario: ") . utf8_decode($nombreVoluntario), 0,1);
                    $pdf->Cell(0,10, utf8_decode("Dirección: ") . utf8_decode($direccionEnvio) . ", " . utf8_decode($ciudad) . ", " . utf8_decode($provincia), 0,1);
                   
                    $pdf->Cell(0,10, utf8_decode('Código postal: ') . utf8_decode($codigoPostal),0,1);
                    $pdf->Cell(0,10, utf8_decode('Teléfono: ') . utf8_decode($telefono),0,1);
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetTextColor(255,255,255);
                    $pdf->Cell(0,10,'Libros pedidos: ',0,1, 'L', true);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Arial','',10);
                    $pdf->TablaSimple($header, $pedidoVacio);
    
                    foreach ($pedidoTabla as $key => $value) {
                        $pdf->SetFont('Arial','',10);
                        $pdf->Cell(0,10, utf8_decode($value),1,1);
                    }
                    
                    $pdf->Ln();

                    $archivoPdf = $pdf->Output('','S');                  
    
                    $email_user = "pedidosresidencias@hotmail.com";
                    $email_password = "pedidos.1379";
    
                    $the_subject = "Nuevo pedido de " . utf8_decode($nombreSiPueden);
                    $address_to = $mail;
                    $from_name = "Si Pueden";
                    $phpmailer = new PHPMailer();
                    // ———- datos de la cuenta de Gmail ——————————-
                    $phpmailer->Username = $email_user;
                    $phpmailer->Password = $email_password; 
                    
                    $phpmailer->Host = "smtp.office365.com"; // GMail
                    $phpmailer->SMTPSecure = 'STARTTLS';
                    
                    $phpmailer->Port = 587;
                    $phpmailer->IsSMTP(); // use SMTP
                    $phpmailer->SMTPAuth = true;
                    $phpmailer->setFrom($phpmailer->Username,$from_name);
                    $phpmailer->AddAddress($address_to); // recipients email
                    if ($mailCopia) {
                        $phpmailer->AddBCC($mail);
                        $address_to = $mailCopia;
                    }
                    $phpmailer->Subject = $the_subject;	
    
                    $phpmailer->Body .="<p>Nuevo pedido de </p>" . utf8_decode($nombreSiPueden) . " - ";
                    $phpmailer->Body .= utf8_decode($ciudad) . ", " . utf8_decode($provincia);
                    $phpmailer->Body .= "<p>Fecha: " . $fecha ."</p>";
                    $phpmailer->IsHTML(true);
                    $phpmailer->AddStringAttachment($archivoPdf, utf8_decode($nombreSiPueden) . '.pdf','base64');
                    try {
                        $phpmailer->smtpConnect([
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            ]
                        ]);
                        $mensaje = "Pedido enviado correctamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = false;
                    } catch (\Throwable $th) {
                        $mensaje = "El pedido no se pudo enviar. Intente nuevamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = true;
                        die;
                    }
                    if(!$phpmailer->send()) { 
                        $mensaje = "El pedido no se pudo enviar. Intente nuevamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = true;
                        die;
                    }
                } catch (\Throwable $th) {
                    $mensaje = "Hubo un error al enviar el pedido. Intente nuevamente";
                    $res["mensaje"] = $mensaje;
                    $res["error"] = true;
                }
            }

        break;

        case 'enviarPedidoMeriendas':
            require("pdf.php");
          
            $nombreSiPueden     = $_POST["nombreSiPueden"]; 
            $nombreVoluntario   = $_POST["nombreVoluntario"]; 
            $direccionEnvio     = $_POST["direccionEnvio"]; 
            $ciudad             = $_POST["ciudad"];  
            $provincia          = $_POST["provincia"]; 
            $codigoPostal       = $_POST["codigoPostal"];
            $telefono           = $_POST["telefono"];
            $fecha              = $_POST["fecha"];
            $mail               = $_POST["mail"];
            $mailCopia          = $_POST["mailCopia"];
            $pedido             = $_POST["pedido"];
            $pedidoTabla        = explode(';', $pedido);
            $pedidoVacio        = [];

            date_default_timezone_set('America/Argentina/Cordoba');
            $date = date("Y-m-d H:i:s");

            $data = "'" . $date . "', '" . $direccionEnvio . "', '" . $ciudad . "', '" . $provincia . "', '" . $codigoPostal . "', '" . $telefono . "', '" . $pedido . "', '" . $nombreVoluntario . "', '" . $nombreSiPueden . "'";
            // local:
            // $u = $user -> insertar("pedidos", $data);
            // prod
            $u = $user -> insertar("sipueden", $data);
         
            if ($u == false) { 
                $res["mensaje"] = "El pedido no pudo realizarse";
                $res["error"] = true;
                          
            } else {   
    
                try {
                    $pdf = new PDF();
                    $pdf->AliasNbPages();
                    $header = array('Listado de articulos pedidos');
                    $pdf->AddPage();
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(0,10,$fecha,0,1,'R');
                    $pdf->SetFont('Arial','B',12);
                    $pdf->Cell(0,5,"Nuevo pedido de " . utf8_decode($nombreSiPueden),0,1,'C');
                    
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetTextColor(255,255,255);
                    $pdf->Cell(0,10,'Datos de envio: ',0,1, 'L', true);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Arial','',10);
                    $pdf->Cell(0,10, utf8_decode("Voluntario: ") . utf8_decode($nombreVoluntario), 0,1);
                    $pdf->Cell(0,10, utf8_decode("Dirección: ") . utf8_decode($direccionEnvio) . ", " . utf8_decode($ciudad) . ", " . utf8_decode($provincia), 0,1);
                   
                    $pdf->Cell(0,10, utf8_decode('Código postal: ') . utf8_decode($codigoPostal),0,1);
                    $pdf->Cell(0,10, utf8_decode('Teléfono: ') . utf8_decode($telefono),0,1);
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetTextColor(255,255,255);
                    $pdf->Cell(0,10,'Articulos pedidos: ',0,1, 'L', true);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Arial','',10);
                    $pdf->TablaSimple($header, $pedidoVacio);
    
                    foreach ($pedidoTabla as $key => $value) {
                        $pdf->SetFont('Arial','',10);
                        $pdf->Cell(0,10, utf8_decode($value),1,1);
                    }
                    
                    $pdf->Ln();

                    $archivoPdf = $pdf->Output('','S');                  
    
                    $email_user = "pedidosresidencias@hotmail.com";
                    $email_password = "pedidos.1379";
    
                    $the_subject = "Nuevo pedido de " . utf8_decode($nombreSiPueden);
                    $address_to = $mail;
                    $from_name = "Si Pueden";
                    $phpmailer = new PHPMailer();
                    // ———- datos de la cuenta de Gmail ——————————-
                    $phpmailer->Username = $email_user;
                    $phpmailer->Password = $email_password; 
                    
                    $phpmailer->Host = "smtp.office365.com"; // GMail
                    $phpmailer->SMTPSecure = 'STARTTLS';
                    
                    $phpmailer->Port = 587;
                    $phpmailer->IsSMTP(); // use SMTP
                    $phpmailer->SMTPAuth = true;
                    $phpmailer->setFrom($phpmailer->Username,$from_name);
                    $phpmailer->AddAddress($address_to); // recipients email
                    if ($mailCopia) {
                        $phpmailer->AddBCC($mail);
                        $address_to = $mailCopia;
                    }
                    $phpmailer->Subject = $the_subject;	
    
                    $phpmailer->Body .="<p>Nuevo pedido de </p>" . utf8_decode($nombreSiPueden) . " - ";
                    $phpmailer->Body .= utf8_decode($ciudad) . ", " . utf8_decode($provincia);
                    $phpmailer->Body .= "<p>Fecha: " . $fecha ."</p>";
                    $phpmailer->IsHTML(true);
                    $phpmailer->AddStringAttachment($archivoPdf, utf8_decode($nombreSiPueden) . '.pdf','base64');
                    try {
                        $phpmailer->smtpConnect([
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            ]
                        ]);
                        $mensaje = "Pedido enviado correctamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = false;
                    } catch (\Throwable $th) {
                        $mensaje = "El pedido no se pudo enviar. Intente nuevamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = true;
                        die;
                    }
                    if(!$phpmailer->send()) { 
                        $mensaje = "El pedido no se pudo enviar. Intente nuevamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = true;
                        die;
                    }
                } catch (\Throwable $th) {
                    $mensaje = "Hubo un error al enviar el pedido. Intente nuevamente";
                    $res["mensaje"] = $mensaje;
                    $res["error"] = true;
                }
            }

        break;

        case 'enviarPedidoRecursos':
            require("pdf.php");
          
            $nombreSiPueden     = $_POST["nombreSiPueden"]; 
            $nombreVoluntario   = $_POST["nombreVoluntario"]; 
            $direccionEnvio     = $_POST["direccionEnvio"]; 
            $ciudad             = $_POST["ciudad"];  
            $provincia          = $_POST["provincia"]; 
            $codigoPostal       = $_POST["codigoPostal"];
            $telefono           = $_POST["telefono"];
            $fecha              = $_POST["fecha"];
            $mail               = $_POST["mail"];
            $mailCopia          = $_POST["mailCopia"];
            $pedido             = $_POST["pedido"];
            $pedidoTabla        = explode(';', $pedido);
            $pedidoVacio        = [];

            date_default_timezone_set('America/Argentina/Cordoba');
            $date = date("Y-m-d H:i:s");

            $data = "'" . $date . "', '" . $direccionEnvio . "', '" . $ciudad . "', '" . $provincia . "', '" . $codigoPostal . "', '" . $telefono . "', '" . $pedido . "', '" . $nombreVoluntario . "', '" . $nombreSiPueden . "'";
            // local:
            // $u = $user -> insertar("pedidos", $data);
            // prod
            $u = $user -> insertar("sipueden", $data);
         
            if ($u == false) { 
                $res["mensaje"] = "El pedido no pudo realizarse";
                $res["error"] = true;
                          
            } else {   
    
                try {
                    $pdf = new PDF();
                    $pdf->AliasNbPages();
                    $header = array('Listado de recursos pedidos');
                    $pdf->AddPage();
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(0,10,$fecha,0,1,'R');
                    $pdf->SetFont('Arial','B',12);
                    $pdf->Cell(0,5,"Nuevo pedido de " . utf8_decode($nombreSiPueden),0,1,'C');
                    
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetTextColor(255,255,255);
                    $pdf->Cell(0,10,'Datos de envio: ',0,1, 'L', true);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Arial','',10);
                    $pdf->Cell(0,10, utf8_decode("Voluntario: ") . utf8_decode($nombreVoluntario), 0,1);
                    $pdf->Cell(0,10, utf8_decode("Dirección: ") . utf8_decode($direccionEnvio) . ", " . utf8_decode($ciudad) . ", " . utf8_decode($provincia), 0,1);
                   
                    $pdf->Cell(0,10, utf8_decode('Código postal: ') . utf8_decode($codigoPostal),0,1);
                    $pdf->Cell(0,10, utf8_decode('Teléfono: ') . utf8_decode($telefono),0,1);
                    $pdf->Ln();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetTextColor(255,255,255);
                    $pdf->Cell(0,10,'Recursos pedidos: ',0,1, 'L', true);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Arial','',10);
                    $pdf->TablaSimple($header, $pedidoVacio);
    
                    foreach ($pedidoTabla as $key => $value) {
                        $pdf->SetFont('Arial','',10);
                        $pdf->Cell(0,10, utf8_decode($value),1,1);
                    }
                    
                    $pdf->Ln();

                    $archivoPdf = $pdf->Output('','S');                  
    
                    $email_user = "pedidosresidencias@hotmail.com";
                    $email_password = "pedidos.1379";
    
                    $the_subject = "Nuevo pedido de " . utf8_decode($nombreSiPueden);
                    $address_to = $mail;
                    $from_name = "Si Pueden";
                    $phpmailer = new PHPMailer();
                    // ———- datos de la cuenta de Gmail ——————————-
                    $phpmailer->Username = $email_user;
                    $phpmailer->Password = $email_password; 
                    
                    $phpmailer->Host = "smtp.office365.com"; // GMail
                    $phpmailer->SMTPSecure = 'STARTTLS';
                    
                    $phpmailer->Port = 587;
                    $phpmailer->IsSMTP(); // use SMTP
                    $phpmailer->SMTPAuth = true;
                    $phpmailer->setFrom($phpmailer->Username,$from_name);
                    $phpmailer->AddAddress($address_to); // recipients email
                    if ($mailCopia) {
                        $phpmailer->AddBCC($mail);
                        $address_to = $mailCopia;
                    }
                    $phpmailer->Subject = $the_subject;	
    
                    $phpmailer->Body .="<p>Nuevo pedido de </p>" . utf8_decode($nombreSiPueden) . " - ";
                    $phpmailer->Body .= utf8_decode($ciudad) . ", " . utf8_decode($provincia);
                    $phpmailer->Body .= "<p>Fecha: " . $fecha ."</p>";
                    $phpmailer->IsHTML(true);
                    $phpmailer->AddStringAttachment($archivoPdf, utf8_decode($nombreSiPueden) . '.pdf','base64');
                    try {
                        $phpmailer->smtpConnect([
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            ]
                        ]);
                        $mensaje = "Pedido enviado correctamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = false;
                    } catch (\Throwable $th) {
                        $mensaje = "El pedido no se pudo enviar. Intente nuevamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = true;
                        die;
                    }
                    if(!$phpmailer->send()) { 
                        $mensaje = "El pedido no se pudo enviar. Intente nuevamente";
                        $res["mensaje"] = $mensaje;
                        $res["error"] = true;
                        die;
                    }
                } catch (\Throwable $th) {
                    $mensaje = "Hubo un error al enviar el pedido. Intente nuevamente";
                    $res["mensaje"] = $mensaje;
                    $res["error"] = true;
                }
            }

        break;

        case 'getRecursos':
            $tipo = $_POST["recurso"];
            $idCategoria = $_POST["idCategoria"];
            $inicio = $_POST["inicio"];
            $condicion = "tipo = '". $tipo . "'";

            $u = $user -> consultar("recursos", $tipo, $idCategoria, $inicio);

            if ($u || $u == []) { 
                $res["archivos"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "Hubo un error al recuperar la información. Por favor recargue la página.";
                $res["error"] = true;
            } 

        break;

        case 'getLibros':
            $tipo = $_POST["recurso"];
            $idCategoria = $_POST["idCategoria"];
            $buscador = $_POST["buscador"];
            $inicio = $_POST["inicio"];
            $condicion = "tipo = '". $tipo . "'";
            
            $u = $user -> consultarLibros("recursos", $tipo, $idCategoria, $buscador, $inicio);



            if ($u || $u == []) { 
                $res["archivos"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "Hubo un error al recuperar la información. Por favor recargue la página.";
                $res["error"] = true;
            } 

        break;

        case 'getPlanificaciones':
            $idCategoria = $_POST["idCategoria"];
            $inicio = $_POST["inicio"];

            $u = $user -> consultarPlanificaciones("recursos", $idCategoria, $inicio);

            if ($u || $u == []) { 
                $res["archivos"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "Hubo un error al recuperar la información. Por favor recargue la página.";
                $res["error"] = true;
            } 

        break;

        case 'verPlanificacion':
            $id = $_POST["idPlanificacion"];
            $condicion = "id = '". $id . "'";

            $u = $user -> verPlanificacion("recursos", $condicion);

            if ($u || $u == []) { 
                $res["archivos"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "Hubo un error al recuperar la información. Por favor recargue la página.";
                $res["error"] = true;
            } 

        break;

        case 'getCategorias':
            $tipo = $_POST["recurso"];
            $condicion = "tipo = '". $tipo . "'";
                   
            $u = $user -> consultarCategorias("categoriasrecursos", $condicion);

            if ($u || $u == []) { 
                $res["categorias"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo recuperar las categorias";
                $res["error"] = true;
            } 

        break;

        case 'contarLibros':
            $categoria = $_POST["categoria"];
            $buscador = $_POST["buscador"];

            $u = $user -> contarLibros($categoria, $buscador);
            if ($u || $u == []) { 
                $res["cantidad"] = $u[0]["total"];
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "Hubo un error al recuperar la información. Actualice la página";
                $res["error"] = true;
            } 

        break;

        case 'contarRecursos':
            $categoria = $_POST["categoria"];

            $u = $user -> contarRecursos($categoria);
            if ($u || $u == []) { 
                $res["cantidad"] = $u[0]["total"];
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "Hubo un error al recuperar la información. Actualice la página";
                $res["error"] = true;
            } 

        break;

        case 'contarPlanificaciones':
            $categoria = $_POST["categoria"];

            $u = $user -> contarPlanificaciones($categoria);
            if ($u || $u == []) { 
                $res["cantidad"] = $u[0]["total"];
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "Hubo un error al recuperar la información. Actualice la página";
                $res["error"] = true;
            } 

        break;


        case 'postCategoria':
            $tipo = $_POST["tipo"];
            $categoria = $_POST["categoria"];

            $data = "'" . $tipo  ."', '" . $categoria  . "'";
            $u = $user -> insertar("categoriasrecursos", $data);

            if ($u) {
                $res["error"] = false;
                $res["mensaje"] = "La categoria se creó correctamente";
            } else {
                $res["mensaje"] = "No se pudo crear la categoria";
                $res["error"] = true;
            } 

        break;

        case 'crearRecurso':
            $tipo = $_POST["tipo"];
            $nombre = $_POST["nombre"];
            $categoria = $_POST["categoria"];
            $descripcion = $_POST["descripcion"];
            $archivo = $_POST['archivo'];
            $data = "'" . $tipo . "', '" . $nombre . "', '" . $categoria . "', '" . $descripcion . "', '" . $archivo . "'";
            
            $u = $user -> insertar("recursos", $data);
            //echo $u;
        
            if ($u) {
                $res["error"] = false;
                $res["mensaje"] = "El archivo se guardó correctamente";
            } else {
                $res["mensaje"] = "No se pudo guardar el archivo. Intente nuevamente";
                $res["error"] = true;
            } 

        break;

        case 'buscarLibrosPorCategoria':
            $idCategoria = $_POST["idCategoria"];

            $u = $user -> buscarPorCategoria($idCategoria);
            if ($u || $u == []) { 
                $res["libros"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo recuperar los pedidos";
                $res["error"] = true;
            } 

        break;

        case 'eliminarLibro':
            $idLibro = $_POST["idLibro"];

            $u = $user -> eliminar("recursos", "id = ". $idLibro);
            if ($u || $u == []) { 
                $res["libros"] = $u;
                $res["mensaje"] = "El libro se eliminó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo eliminar el libro";
                $res["error"] = true;
            } 

        break;

        case 'eliminarRecurso':
            $idRecurso = $_POST["idRecurso"];

            $u = $user -> eliminar("recursos", "id = ". $idRecurso);
            if ($u || $u == []) { 
                $res["recursos"] = $u;
                $res["mensaje"] = "El recurso se eliminó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo eliminar el recurso";
                $res["error"] = true;
            } 

        break;

        case 'eliminarPlanificacion':
            $idRecurso = $_POST["idPlanificacion"];

            $u = $user -> eliminar("recursos", "id = ". $idRecurso);
            if ($u || $u == []) { 
                $res["recursos"] = $u;
                $res["mensaje"] = "La planificación se eliminó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo eliminar la planificación";
                $res["error"] = true;
            } 

        break;

        case 'getPedidos':
            $u = $user -> getPedidos();

            if ($u || $u == []) { 
                $res["pedidos"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "Hubo un error al recuperar la información. Por favor recargue la página.";
                $res["error"] = true;
            } 

        break;

        default:
            # code...
            break;
    }


    echo json_encode($res);
?>
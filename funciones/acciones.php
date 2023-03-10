<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require "../src/Exception.php";
    require "../src/PHPMailer.php";
    require "../src/SMTP.php";
    $accion = "mostrar";
    $res = array("error" => false);
    $archivoPdf = null;
    
    if (isset($_GET["accion"])) {
        $accion = $_GET["accion"];
    }


    switch ($accion) {
        case 'enviarPedido':
            require("pdf.php");
          
            $nombreSiPueden = $_POST["nombreSiPueden"]; 
            $direccionEnvio = $_POST["direccionEnvio"]; 
            $ciudad         = $_POST["ciudad"];  
            $provincia      = $_POST["provincia"]; 
            $codigoPostal   = $_POST["codigoPostal"];
            $telefono       = $_POST["telefono"];
            $fecha          = $_POST["fecha"];
            $mail           = $_POST["mail"];
            $mailCopia      = $_POST["mailCopia"];
            $pedidoTabla    = [];
            try {
                $pdf = new PDF();
                $pdf->AliasNbPages();
                $header = array('Listado de articulos pedidos');
                $pdf->AddPage();
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(0,5,"Nuevo pedido de " . utf8_decode($nombreSiPueden),0,1,'C');
                $pdf->Cell(0,10,'Fecha: ' . $fecha,0,1);
                $pdf->Cell(0,10, utf8_decode('Dirección: ') . utf8_decode($direccionEnvio),0,1);
                $pdf->Cell(0,10,'Ciudad: ' . utf8_decode($ciudad),0,1);
                $pdf->Cell(0,10,'Provincia: ' . utf8_decode($provincia),0,1);
                $pdf->Cell(0,10, utf8_decode('Código postal: ') . utf8_decode($codigoPostal),0,1);
                $pdf->Cell(0,10, utf8_decode('Teléfono: ') . utf8_decode($telefono),0,1);
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(0,10,'Articulos pedidos: ' ,0,1);
                $pdf->SetFont('Arial','',10);
                $pdf->TablaSimple($header, $pedidoTabla);
              
                $archivoPdf = $pdf->Output('','S');                  

                $email_user = "pedidosresidencias@hotmail.com";
                $email_password = "pedidos.1379";

                $the_subject = "Nuevo pedido de " . $nombreSiPueden;
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

                $phpmailer->Body .="<p>Nuevo pedido de </p>" . $nombreSiPueden;
                $phpmailer->Body .= $ciudad . " " . $provincia;
                $phpmailer->Body .= "<p>Fecha: " . $fecha ."</p>";
                $phpmailer->IsHTML(true);
                $phpmailer->AddStringAttachment($archivoPdf, $nombreSiPueden . '.pdf','base64');
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
        break;

        default:
            # code...
            break;
    }


    echo json_encode($res);
?>
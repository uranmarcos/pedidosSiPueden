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
            $pedidoTabla    = [];
            try {
                $pdf = new PDF();
                $pdf->AliasNbPages();
                $header = array('Listado de articulos pedidos');
                $pdf->AddPage();
                $pdf->SetFont('Arial','B',12);
                $pdf->Cell(0,5,"Nuevo pedido de " . utf8_decode($nombreSiPueden),0,1,'C');
                $pdf->Cell(0,10,'Fecha: ' . $fecha,0,1);
                $pdf->Cell(0,10, utf8_decode('Dirección: ') . utf8_decode($direccionEnvio),0,1);
                $pdf->Cell(0,10,'Ciudad: ' . utf8_decode($ciudad),0,1);
                $pdf->Cell(0,10,'Provincia: ' . utf8_decode($provincia),0,1);
                $pdf->Cell(0,10, utf8_decode('Código postal: ') . utf8_decode($codigoPostal),0,1);
                $pdf->Cell(0,10, utf8_decode('Teléfono: ') . utf8_decode($telefono),0,1);
                $pdf->SetFont('Arial','B',12);
                $pdf->Cell(0,10,'Articulos pedidos: ' ,0,1);
                $pdf->SetFont('Arial','',12);
                $pdf->TablaSimple($header, $pedidoTabla);
                // $pdf->SetFont('Arial','B',12);
                // $pdf->Cell(0,10,'Otros: ' ,0,1);
                // $pdf->SetFont('Arial','',12);
                // $pdf->Multicell(190,10,utf8_decode($otrosFormateado),1);
                // $pdf->Output();
                $archivoPdf = $pdf->Output('','S');                  

                $email_user = "pedidosresidencias@hotmail.com";
                $email_password = "pedidos.1379";

                // $the_subject = "Nuevo pedido de ". utf8_decode($sede[0]["provincia"]) . ", " .utf8_decode($sede[0]["localidad"]);
                $the_subject = "Nuevo pedido de " . $nombreSiPueden;
                // $address_to = "marcos_uran@hotmail.com";
                $address_to = $mail;
                // $address_to = "manuel@fundacionsi.org.ar";
                // $from_name = "Residencia: " . utf8_decode($sede[0]["provincia"]) . ", " .utf8_decode($sede[0]["localidad"]);
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
                $phpmailer->Subject = $the_subject;	
                
                $phpmailer->Body .="<p>Prueba</p>";
                $phpmailer->Body .= "<p>Fecha: " . $fecha ."</p>";
                $phpmailer->IsHTML(true);
                // $phpmailer->AddAttachment($pdf); // attachment
                $phpmailer->AddStringAttachment($archivoPdf, $nombreSiPueden . '.pdf','base64');
                try {
                    $phpmailer->smtpConnect([
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        ]
                    ]);
                    $mensaje = "Pedido enviado";
                    $res["mensaje"] = $mensaje;
                    $res["error"] = false;
                } catch (\Throwable $th) {
                    $mensaje = "El pedido no se pudo enviar";
                    $res["mensaje"] = $mensaje;
                    $res["error"] = true;
                    die;
                }
                if(!$phpmailer->send()) { 
                    $mensaje = "El pedido no se pudo enviar";
                    $res["mensaje"] = $mensaje;
                    $res["error"] = true;
                    die;
                }
                
                // echo "<script>window.open($archivoPdf, '_blank');</script>";
                // $res["archivoPdf"] = $archivoPdf;
                // $res["pdf"] = $pdf;
            } catch (\Throwable $th) {
                $mensaje = "Hubo un error al generar el pdf";
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
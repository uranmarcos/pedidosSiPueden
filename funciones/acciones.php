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
    
                    $phpmailer->Body .="<p>Nuevo pedido de </p>" . $nombreSiPueden . " - ";
                    $phpmailer->Body .= $ciudad . ", " . $provincia;
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
            }

        break;

        case 'consultarLibros':
            $u = $user -> consultarLibros();
            if ($u || $u == []) { 
                $res["libros"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo recuperar los pedidos";
                $res["error"] = true;
            } 

        break;

        case 'consultarRecursos':
            $tipo = $_POST["recurso"];
            $u = $user -> consultar("recursos", $tipo);

            if ($u || $u == []) { 
                $res["planificaciones"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo recuperar las planificaciones";
                $res["error"] = true;
            } 

        break;


        //
        //
        //
        case 'getRecursos':
            $tipo = $_POST["recurso"];
            $u = $user -> consultar("recursos", $tipo);

            if ($u || $u == []) { 
                $res["planificaciones"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo recuperar las planificaciones";
                $res["error"] = true;
            } 

        break;

        case 'getCategorias':
            $tipo = $_POST["recurso"];
            $condicion = "tipo = '". $tipo . "'";
            // "id = ". $idLibro
            // echo $condicion;
            // echo "<br>";
            // // echo $tipo;

         
            $u = $user -> consultar("categoriasrecursos", $condicion);

            if ($u || $u == []) { 
                $res["categorias"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo recuperar las categorias";
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


        //
        //
        //
        case 'buscarPorCategoria':
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

            $u = $user -> eliminar("libros", "id = ". $idLibro);
            if ($u || $u == []) { 
                $res["libros"] = $u;
                $res["mensaje"] = "El libro se eliminó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo eliminar el libro";
                $res["error"] = true;
            } 

        break;

        case 'consultarCategorias':
            $u = $user -> consultarCategorias();

            if ($u || $u == []) { 
                $res["categorias"] = $u;
                $res["mensaje"] = "La consulta se realizó correctamente";
            } else {
                $res["u"] = $u;
                $res["mensaje"] = "No se pudo recuperar las categorias";
                $res["error"] = true;
            } 

        break;

        case 'crearCategoria':
            $categoria = $_POST["categoria"];
            $data = "'" . $categoria  . "'";
            $u = $user -> insertar("categoriaslibros", $data);

            if ($u) {
                $res["error"] = false;
                $res["mensaje"] = "La creación se se realizó correctamente";
            } else {
                $res["mensaje"] = "No se pudo crear la categoria";
                $res["error"] = true;
            } 

        break;

        case 'crearLibro':
            $descripcion = $_POST["descripcion"];
            $categoria = $_POST["categoria"];
            $nombre = $_POST["nombre"];
            $imagen = $_POST['imagen'];
            
            $data = "'" . $nombre . "', '" . $categoria . "', '" . $descripcion . "', '" . $imagen . "'";
            
            $u = $user -> insertar("libros", $data);
        
            if ($u) {
                $res["error"] = false;
                $res["mensaje"] = "La creación se realizó correctamente";
            } else {
                $res["mensaje"] = "No se pudo crear el libro";
                $res["error"] = true;
            } 

        break;

        default:
            # code...
            break;
    }


    echo json_encode($res);
?>
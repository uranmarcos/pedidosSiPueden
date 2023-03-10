<?php 
    $otrosTabla = "";
    $pedidoTabla = [];

    try {
        $nombreSiPueden = $_POST["nombreSiPueden"]; 
            $direccionEnvio = $_POST["direccionEnvio"]; 
            $ciudad         = $_POST["ciudad"];  
            $provincia      = $_POST["provincia"]; 
            $codigoPostal   = $_POST["codigoPostal"];
            $telefono       = $_POST["telefono"];
            $fecha          = $_POST["fecha"];
            $pedidoTabla    = $_POST["pedido"];
          
                $pdf = new PDF();
                $pdf->AliasNbPages();
                $header = array('Listado de articulos pedidos');
                $pdf->AddPage();
                $pdf->SetFont('Arial','B',12);
                $pdf->Cell(0,5,"Nuevo pedido de " . utf8_decode($nombreSiPueden),0,1,'C');
                $pdf->Cell(0,10,'Fecha: ' . $fecha,0,1);
                $pdf->Cell(0,10,'Dirección: ' . utf8_decode($direccionEnvio),0,1);
                $pdf->Cell(0,10,'Ciudad: ' . utf8_decode($ciudad),0,1);
                $pdf->Cell(0,10,'Provincia: ' . utf8_decode($provincia),0,1);
                $pdf->Cell(0,10,'Código postal: ' . utf8_decode($codigoPostal),0,1);
                $pdf->Cell(0,10,'Teléfono: ' . utf8_decode($telefono),0,1);
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
             
        } catch (\Throwable $th) {
            $alertErrorConexion= "show";
        }
        
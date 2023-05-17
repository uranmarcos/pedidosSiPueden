<?php
session_start();
require("../conexion/conexion.php");
$user = new ApptivaDB();
// require("funciones/pedidos.php");



// START ACCION VER PDF - ABRIR PEDIDO //        
    $id = $_POST["id"];
    $u = $user -> verPedido($id);
    require("pdf.php");
  
    $user = new ApptivaDB();
    $u = $user -> verPedido($id);

    if ($u || $u == []) { 
                
        $nombreSiPueden = $u[0]["merendero"];
        $nombreVoluntario = $u[0]["voluntario"];
        $direccionEnvio = $u[0]["direccion"];
        $codigoPostal = $u[0]["codigoPostal"];
        $telefono = $u[0]["telefono"];
        $pedido = $u[0]["pedido"];
        $ciudad = $u[0]["ciudad"];
        $provincia = $u[0]["provincia"];
        $fecha = $u[0]["fecha"];
        $date = date("Y-m-d H:i:s");

        $originalDate = "2017-03-08";
        $fecha = date("d/m/Y H:i:s", strtotime($fecha));
        $pedidoTabla = [];
        $pedidoTabla  = explode(';', $pedido);
 

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
    
            foreach ($pedidoTabla as $key => $value) {
                $pdf->SetFont('Arial','',10);
                $pdf->Cell(0,10, utf8_decode($value),1,1);
            }
                    
            $pdf->Ln();
            $pdf->SetFont('Arial','B',11);
            $pdf->Output();
        } catch (\Throwable $th) {
            $alertErrorConexion= "show";
        }
    }
// END ACCION VER PDF - ABRIR PEDIDO //  

?>
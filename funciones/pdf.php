<?php
require('../pdf/fpdf.php');
class PDF extends FPDF
{
//Cabecera de página
    function Header()
    {
        $this->Image('../img/logohor.jpg',87,8,33);
        $this->Ln(20);
    }
   function TablaSimple($header, $data)
   {
    foreach($data as $row)
    {
        foreach($row as $col){
            $this->Cell(190,8,$col,1);
            $this->Ln();
        }
    }
   }   
}
?>
<?php
    require("conexion.php");
    $user = new ApptivaDB();

    $accion = "mostrar";
    $res = array("error" => false);
    if (isset($_GET["accion"])) {
        $accion = $_GET["accion"];
    }

    switch ($accion) {

        // START CASOS ABM SEDES 
            case 'insertarSede':
                $provincia  = $_POST["provincia"];
                $localidad  = $_POST["localidad"];
                $casas      = $_POST["casas"];

                $dataValidar = " provincia LIKE '$provincia' and localidad LIKE '$localidad'"; 
                $validacion = $user -> hayRegistro("sedes", $dataValidar);  
                    
                if ($validacion > 0) {
                    $res["mensaje"] = "La sede ya existe";
                    $res["error"] = true; 
                    break;
                }
                    
                if ($validacion == false) {
                    $res["mensaje"] = "La creación no pudo realizarse";
                    $res["error"] = true;
                    break;
                }

                $data = "'" . $provincia . "', '" . $localidad . "', '" . $casas . "'";
                $u = $user -> insertar("sedes", $data);
                    
                if ($u) { 
                    $res["mensaje"] = "La creación se realizó correctamente";
                } else {
                    $res["mensaje"] = "La creación no pudo realizarse";
                    $res["error"] = true;
                } 
            break;

            case 'consultarSedes':
                $u = $user -> consultar("sedes", 1);
                    
                if ($u) { 
                    $res["sedes"] = $u;
                    $res["mensaje"] = "La consulta se realizó correctamente";
                } else {
                    $res["mensaje"] = "No se pudo recuperar las sedes";
                    $res["error"] = true;
                } 
            break;

            case 'editarSede':
                $id     = $_POST["id"];
                $casas  = $_POST["casas"];
                
                $data="casas = " . $casas;
                $u = $user -> actualizar("sedes", $data, "id = " . $id);        
                    
                if ($u) { 
                    $res["mensaje"] = "La modificación se realizó correctamente";
                } else {
                    $res["mensaje"] = "La modificación no pudo realizarse";
                    $res["error"] = true;
                } 
            break;

            case 'eliminarSede':
                $id = $_POST["id"]; 
                
                $u = $user -> eliminar("sedes", "id = ". $id);
            
                if ($u) { 
                    $res["mensaje"] = "La eliminación se realizó correctamente";
                } else {
                    $res["mensaje"] = "La eliminación no pudo realizarse";
                    $res["error"] = true;
                } 
            break;
        // END CASOS ABM SEDES


        // START CASOS ABM USUARIO
            case 'consultarUsuarios':
                $u = $user -> consultar("usuarios", 1);
                    
                $res["mensaje"] =$u;
                //break;

                if ($u) { 
                    $res["usuarios"] = $u;
                    $res["mensaje"] = "La consulta se realizó correctamente";
                } else {
                    $res["mensaje"] = "No se pudo recuperar los usuarios";
                    $res["error"] = true;
                } 
            break;

            case 'insertarUsuario':
                $primerNombre       = $_POST["primerNombre"];
                $segundoNombre      = $_POST["segundoNombre"];
                $primerApellido     = $_POST["primerApellido"];
                $segundoApellido    = $_POST["segundoApellido"];
                $dni                = $_POST["dni"];
                $sede               = $_POST["sede"];
                $mail               = $_POST["mail"];

                $dataValidar = " dni LIKE '$dni'"; 
                $validacion = $user -> hayRegistro("usuarios", $dataValidar);  
                    
                if ($validacion > 0) {
                    $res["mensaje"] = "El dni ya se encuentra registrado";
                    $res["error"] = true; 
                    break;
                }

                $dataValidar = " mail LIKE '$mail'"; 
                $validacion = $user -> hayRegistro("usuarios", $dataValidar);  
                    
                if ($validacion > 0) {
                    $res["mensaje"] = "El mail ya se encuentra registrado";
                    $res["error"] = true; 
                    break;
                }
                    
                if ($validacion === false) {
                    $res["mensaje"] = "La creación no pudo realizarse";
                    $res["error"] = true;
                    break;
                }

                $data = "'" . $primerNombre . "', '" . $segundoNombre . "', '" . $primerApellido . "', '" . $segundoApellido . "', '" . $dni . "', '" . $sede . "', '" . $mail . "', '" . $dni . "'";
                $u = $user -> insertar("usuarios", $data);
                    
                if ($u) { 
                    $res["mensaje"] = "La creación se realizó correctamente";
                } else {
                    $res["mensaje"] = "La creación no pudo realizarse";
                    $res["error"] = true;
                } 
            break;

            case 'editarUsuario':
                $id     = $_POST["id"];
                $nombre  = $_POST["nombre"];
                $segundoNombre  = $_POST["segundoNombre"];
                $apellido  = $_POST["apellido"];
                $segundoApellido  = $_POST["segundoApellido"];
                $mail  = $_POST["mail"];
                $dni  = $_POST["dni"];
                $sede  = $_POST["sede"];
                
                $dataValidar = " dni LIKE '$dni' AND id NOT LIKE '$id'"; 
                $validacion = $user -> hayRegistro("usuarios", $dataValidar);  
                    
                if ($validacion > 0) {
                    $res["mensaje"] = "El dni ya se encuentra registrado";
                    $res["error"] = true; 
                    break;
                }

                $dataValidar = " mail LIKE '$mail' AND id NOT LIKE '$id'"; 
                $validacion = $user -> hayRegistro("usuarios", $dataValidar);  

                if ($validacion > 0) {
                    $res["mensaje"] = "El mail ya se encuentra registrado";
                    $res["error"] = true; 
                    break;
                }
                    
                if ($validacion === false) {
                    $res["mensaje"] = "La modificacion no pudo realizarse";
                    $res["error"] = true;
                    break;
                }

                $data = "nombre = '" . $nombre . "', segundoNombre = '" . $segundoNombre . "', apellido = '" . $apellido . "', segundoApellido = '" . $segundoApellido . "', dni = '" . $dni . "', mail = '" . $mail . "', sede = " . $sede;
                $u = $user -> actualizar("usuarios", $data, "id = " . $id);        
                    
                if ($u) { 
                    $res["mensaje"] = "La modificación se realizó correctamente";
                } else {
                    $res["mensaje"] = "La modificación no pudo realizarse";
                    $res["error"] = true;
                } 
            break;

            case 'eliminarUsuario':
                $id = $_POST["id"]; 
                
                $u = $user -> eliminar("usuarios", "id = ". $id);
            
                if ($u) { 
                    $res["mensaje"] = "La eliminación se realizó correctamente";
                } else {
                    $res["mensaje"] = "La eliminación no pudo realizarse";
                    $res["error"] = true;
                } 
            break;
        // END CASOS ABM USUARIO



        // START CASOS ABM ARTICULOS
            case 'insertarArticulo':
                $descripcion       = $_POST["descripcion"];
                $categoria      = $_POST["categoria"];
                $medida     = $_POST["medida"];

                $data = "'" . $descripcion . "', '" . $categoria . "', '" . $medida .  "', 1" ;
                $u = $user -> insertar("articulos", $data);
                    
                if ($u) { 
                    $res["mensaje"] = "La creación se realizó correctamente";
                } else {
                    $res["mensaje"] = "La creación no pudo realizarse";
                    $res["error"] = true;
                } 
            break;

            case 'consultarArticulos':
                $u = $user -> consultar("articulos", 1);
                    
                $res["mensaje"] =$u;
                //break;

                if ($u) { 
                    $res["articulos"] = $u;
                    $res["mensaje"] = "La consulta se realizó correctamente";
                } else {
                    $res["mensaje"] = "No se pudo recuperar los articulos";
                    $res["error"] = true;
                } 
            break;

            case 'editarArticulo':
                $id             = $_POST["id"];
                $descripcion    = $_POST["descripcion"];
                $categoria      = $_POST["categoria"];
                $medida         = $_POST["medida"];
                $vigente         = $_POST["vigente"];

                $data = "descripcion = '" . $descripcion . "', categoria = '" . $categoria . "', medida = '" . $medida . "', vigente = '" . $vigente . "'";
                $u = $user -> actualizar("articulos", $data, "id = " . $id);        
                    
                if ($u) { 
                    $res["mensaje"] = "La modificación se realizó correctamente";
                } else {
                    $res["mensaje"] = "La modificación no pudo realizarse";
                    $res["error"] = true;
                } 
            break;
        // END CASOS ABM ARTICULOS

        // START CASOS GENERAR PEDIDO
            case 'generarPedido':
                $sede       = $_POST["sede"];
                $usuario    = $_POST["usuario"];
                $pedido     = $_POST["pedido"];

                $data = "'" . $sede . "', '" . $usuario . "', '" . $pedido . "', " . 'NOW()' .  ", 0";
                $u = $user -> insertar("pedidos", $data);

                if ($u) { 
                    $res["mensaje"] = "El pedido se generó correctamente";
                } else {
                    $res["mensaje"] = "El pedido no pudo realizarse";
                    $res["error"] = true;
                } 
            break;

            case 'consultarPedidos':
                $u = $user -> consultarPedidos("pedidos", 1);
                    
                $res["mensaje"] =$u;

                if ($u) { 
                    $res["pedidos"] = $u;
                    $res["mensaje"] = "La consulta se realizó correctamente";
                } else {
                    $res["mensaje"] = "No se pudo recuperar los pedidos";
                    $res["error"] = true;
                } 
            break;



        // END CASOS GENERAR PEDIDO





        ////
        ////
        ////
        ////


        case 'mostrar':
            $u = $user -> buscar("paisajes", 1);
            if($u): 
                $res["paisajes"] = $u;
                $res["mensaje"] = "exito";
            else: 
                $res["mensaje"] = "Falló la consulta";
                $res["error"] = true;
            endif;
            # code...
            break;

        case 'editar':
            $id             = $_POST["eid"];
            $nombre         = $_POST["enombre"];
            $descripcion    = $_POST["edescripcion"];
            $foto           = "";

            if(isset($_FILES["efoto"]["name"])) {
                $foto           = $_FILES["efoto"]["name"];
                $target_dir = "img/";
                $target_file =$target_dir.basename($foto);
                move_uploaded_file($_FILES['efoto']['tmp_name'], $target_file);
                $foto = ", foto = '" . $_FILES["efoto"]["name"]."'";
            }


            $data="nombre='" . $nombre . "', descripcion='" . $descripcion . "'" . $foto;
            $u = $user -> actualizar("paisajes", $data, "id = " . $id);

            if($u): 
                $res["mensaje"] = "edicion exitosa";
            else: 
                $res["mensaje"] = "Falló la edicion";
                $res["error"] = true;
            endif;
            # code...
            break;


        case 'eliminar':
            $id             = $_POST["did"];

            $u = $user -> borrar("paisajes", "id = ". $id);

            if($u): 
                $res["mensaje"] = "eliminacion exitosa";
            else: 
                $res["mensaje"] = "Falló la eliminacion";
                $res["error"] = true;
            endif;
            # code...
            break;

        case 'insertar':
            $nombre         = $_POST["nombre"];
            $descripcion    = $_POST["descripcion"];
            $foto           = $_FILES["foto"]["name"];

            $target_dir = "img/";
            $target_file =$target_dir.basename($foto);
            move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);

            $data="'" . $nombre . "', '" . $descripcion . "', '" . $foto . "'";
            $u = $user -> insertar("paisajes", $data);

            if($u): 
                $res["mensaje"] = "creacion exitosa";
            else: 
                $res["mensaje"] = "Falló el insert";
                $res["error"] = true;
            endif;
            # code...
            break;

        default:
            # code...
            break;
    }

    echo json_encode($res);
    die();
?>
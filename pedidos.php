<?php
session_start();
$rol = "usuario";
if (!$_SESSION["login"]) {
    header("Location: index.html");
}

if ($_SESSION["rol"] != "admin" && $_SESSION["rol"] != "superAdmin") {
    header("Location: home.php");
}

if ($_SESSION["rol"] == "admin" ) {
    $rol = "admin";
}
if ($_SESSION["rol"] == "superAdmin" ) {
    $rol = "superAdmin";
}
if(time() - $_SESSION['login_time'] >= 1000){
    session_destroy(); // destroy session.
    header("Location: index.html");
    die(); 
} else {        
   $_SESSION['login_time'] = time();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI PEDIDOS</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.21/vue.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.2.1/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <link href="css/home.css" rel="stylesheet"> 
    <link href="css/notificacion.css" rel="stylesheet"> 
    <link href="css/modal.css" rel="stylesheet"> 
    <script src="funciones/pdf.js" crossorigin="anonymous"></script>
</head>
<body>
    <div id="app">
        <?php require("shared/header.html")?>
        
        <div class="container">
            <!-- START BREADCRUMB -->
            <div class="col-12 p-0">
                <div class="breadcrumb">
                    <span class="pointer mx-2" @click="irAHome()">Inicio</span>  -  <span class="mx-2 grey"> Pedidos realizados </span>
                </div>
            </div>
            <!-- END BREADCRUMB -->
           

            <div class="row mt-6">
                <!-- START BREADCRUMB -->
                <div class="col-12 px-3" v-if="rol == 'superAdmin'">
                    <div v-if="consultandoLimpieza" class="limpiezaPedidos">
                        Verificando si existen pedidos para eliminar...
                    </div>
                    <div v-else class="atencionLimpieza">
                        <div  class="atencionLimpieza px-2">
                            {{pedidosALimpiar}} pedidos para limpiar  
                            <button 
                                type="button" 
                                class="botonLimpiar" 
                                @click="limpiarPedidos" 
                                v-if="pedidosALimpiar != 0"
                            >
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>
                <!-- END BREADCRUMB -->
                <div class="col-12">
                    <!-- START COMPONENTE LOADING BUSCANDO pedidos -->
                    <div class="contenedorLoading" v-if="buscandoPedidos">
                        <div class="loading">
                            <div class="spinner-border" role="status">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- END COMPONENTE LOADING BUSCANDO pedidos -->
                    <div v-else>
                        <div v-if="pedidos.length != 0" class="row contenedorPlanficaciones d-flex justify-content-around">
                            <span class="observacion">Se listan los pedidos realizados en los últimos 60 días*</span>
                            <table class="table">
                                <thead>
                                    <tr class="trHead">
                                        <th scope="col">Número</th>
                                        <th scope="col">Voluntario</th>
                                        <th scope="col">Merendero</th>
                                        <th scope="col">Provincia</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Ver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <div>
                                        <tr v-for="pedido in pedidos">
                                            <td>{{pedido.id}}</td>
                                            <td>{{pedido.voluntario}}</td>
                                            <td>{{pedido.merendero}}</td>
                                            <td>{{pedido.provincia}}</td>
                                            <td>{{formatearFecha(pedido.fecha)}}</td>
                                            <td>{{pedido.destino}}</td>
                                            <td class="py-0">
                                                <button 
                                                    type="button" 
                                                    class="btn botonSmallEye" 
                                                    @click="verPedido(pedido.id)" 
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </div>
                                </tbody>
                            </table>
                        </div> 
                        <div class="contenedorTabla" v-else>         
                            <span class="sinResultados">
                                NO SE ENCONTRÓ RESULTADOS PARA MOSTRAR
                            </span>
                        </div>       
                    <!-- END TABLA pedidos -->
                    </div>
                </div>

                
                          
                <!-- NOTIFICACION -->
                <div role="alert" id="mitoast" aria-live="assertive" @mouseover="ocultarToast" aria-atomic="true" class="toast">
                    <div class="toast-header">
                        <!-- Nombre de la Aplicación -->
                        <div class="row tituloToast" id="tituloToast">
                            <strong class="mr-auto">{{tituloToast}}</strong>
                        </div>
                    </div>
                    <div class="toast-content">
                        <div class="row textoToast">
                            <strong >{{textoToast}}</strong>
                        </div>
                    </div>
                </div>
              
            </div>
            <span class="ir-arriba" v-if="scroll" @click="irArriba">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
                </svg>
            </span>

        </div>
        
    </div>

    <style scoped>
        .limpiezaPedidos{
            color: grey;
            font-size: 12px;
        }
        .atencionLimpieza{
            color: rgb(238, 100, 100);
            font-size: 13px;
        }
        .botonLimpiar{
            height: 25px;
            color: rgb(238, 100, 100);
            border: solid 1px rgb(238, 100, 100);
            background: white;
        }
        .botonLimpiar:hover{
            color: white;
            background: rgb(238, 100, 100);
        }
        .categoria{
            font-size: 0.8em;
        }
        .hide{
            display: none;
        }
        .ir-arriba {
            background-color: #7C4599;;
            width: 35px;
            height: 35px;
            font-size:20px;
            border-radius: 50%;
            color:#fff;
            cursor:pointer;
            position: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            bottom:20px;
            right:20%;
        }   
        .contenedorABM{
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
            border: solid 1px #7C4599;
            border-radius: 5px;
        }
        .botonSmall{
            font-size: 12px;
            color: #7C4599;
        }
        .botonSmall:hover{
            font-size: 13px;
            color: #7C4599;
        }
        .botonSmallEye{
            width: 40px;
            height: 30px;
            border: solid 1px rgb(124, 69, 153);;
            font-size: 12px;
            color:rgb(124, 69, 153);
            padding: 0;
            margin: 5px 0;
        }
        .botonSmallEye:hover{
            font-size: 13px;
            background-color: rgb(124, 69, 153);
            color: white;
        }
        .contenedorPlanficaciones{
            width: 100%;
            margin:10px auto;
        }
        #mitoast{
            z-index:60;
        }
        .sinResultados{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 10px 20px;
            text-align: center;
        }
        .contenedorTabla{
            color: rgb(124, 69, 153);
            border: solid 1px rgb(124, 69, 153);
            border-radius: 10px;
            padding: 10xp;
            margin-top: 24px;
            width: 100%;
        }    
        .table{
            font-size: 14px;
            text-align: center
        }
        tr{
            border: solid 1px lightgrey;
        }
        .observacion{
            font-size: 12px;
            color: grey;
        }
    </style>
    <script>
        var app = new Vue({
            el: "#app",
            components: {                
            },
            data: {
                buscandoPedidos: false,
                pedidos: [],
                scroll: false,
                tituloToast: null,
                textoToast: null,
                rol : null,
                pedidosALimpiar: null,
                consultandoLimpieza: false

            },
            mounted () {
                this.getPedidos();
                this.rol = "<?php echo $rol; ?>";
                if (this.rol == 'superAdmin'){
                    this.existenPedidosParaEliminar();
                }
            },
            beforeUpdate(){
                window.onscroll = function (){
                    // Obtenemos la posicion del scroll en pantall
                    var scroll = document.documentElement.scrollTop || document.body.scrollTop;
                }
            },
            methods:{
                getPedidos() {
                    this.buscandoPedidos = true;
                    let formdata = new FormData();

                    axios.post("funciones/acciones.php?accion=getPedidos", formdata)
                    .then(function(response){ 
                        app.buscandoPedidos = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            if (response.data.pedidos != false) {
                                app.pedidos = response.data.pedidos;
                            } else {
                                app.pedidos = []
                            }
                        }
                    });
                },
                existenPedidosParaEliminar () {
                    this.consultandoLimpieza = true;
                    axios.post("funciones/acciones.php?accion=consultandoLimpieza")
                    .then(function(response){ 
                        console.log(response.data);
                        app.consultandoLimpieza = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.pedidosALimpiar = response.data.cantidad;
                            // if (response.data.cantidad != false) {
                            // } else {
                            //     app.pedidos = null
                            // }
                        }
                    });
                },
                limpiarPedidos () {
                    this.limpiandoPedidos = true;
                    axios.post("funciones/acciones.php?accion=limpiarPedidos")
                    .then(function(response){ 
                        console.log(response.data);
                        app.limpiandoPedidos = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.mostrarToast("Éxito", "La limpieza se realizó correctamente");
                            app.existenPedidosParaEliminar();
                        }
                    });
                },
                verPedido(id) {
                    let formdata = new FormData();
                    formdata.append("idPedido", id);
                
                    axios.post("funciones/acciones.php?accion=verPedido", formdata)
                    .then(function(response){  
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            if (response.data.pedido != false) {
                                app.armarPdf(response.data.pedido[0])
                            }
                        }
                    }).catch( error => {
                        app.mostrarToast("Error", "No se pudo visualizar el archivo. Intente nuevamente");
                    });
                },
                armarPdf(pedido){
                    pedido.pedido = pedido.pedido.replaceAll("**", "'")
                    let fecha = this.formatearFecha(pedido.fecha);
                    let merendero = pedido.merendero;
                    let voluntario = pedido.voluntario;
                    let direccion = pedido.direccion;
                    let ciudad = pedido.ciudad;
                    let provincia = pedido.provincia;
                    let codigoPostal = pedido.codigoPostal;
                    let telefono = pedido.telefono;
                    let destino = pedido.destino;
                    let articulosPedidos = pedido.pedido.split(";");
                    try {
                        // ARMADO PDF
                        const doc = new jsPDF();
                        var image = new Image()
                        const font = 'Arial';
                        const backgroundColor = '#F2F2F2'; // Color de fondo gris claro
                        doc.setFont(font);
    
                        image.src = 'img/logohor.jpg'
    
                        doc.addImage(image,80,10,50,16)
    
                        doc.setFontSize(11);
                        doc.text(175, 35, fecha );
                        
                        doc.setFontSize(12);
                        doc.text(20, 45, 'Nuevo pedido de: ');
                        doc.setFontSize(13);
                        doc.text(20, 53, merendero.toUpperCase());
                        
                        doc.setFontSize(13);
                        doc.setFillColor(backgroundColor);
                        doc.rect(20, 60, 173, 7, 'F'); // Rectángulo de fondo gris claro
                        doc.text(20, 65, 'DATOS DE ENVIO');
                        doc.line(20,67,193,67);
    
                        
                        doc.setFontSize(10);
    
                        doc.setFontType('bold');
                        doc.text(20, 75, 'Voluntario:');
    
                        doc.setFontType('regular');
                        doc.text(50, 75, voluntario);
    
    
                        doc.setFontType('bold');
                        doc.text(20, 82, 'Dirección: ');
    
                        doc.setFontType('regular');
                        doc.text(50, 82, direccion);    
    
                        doc.setFontType('bold');
                        doc.text(20, 89, 'Ciudad/Provincia: ');
    
                        doc.setFontType('regular');
                        doc.text(50, 89, ciudad + " / " +provincia);
                        
    
                        doc.setFontType('bold');
                        doc.text(20, 96, 'Código postal: ');
    
                        doc.setFontType('regular');
                        doc.text(50, 96, codigoPostal);
    
    
                        doc.setFontType('bold');
                        doc.text(20, 103, 'Teléfono:');
    
                        doc.setFontType('regular');
                        doc.text(50, 103, telefono);
    
    
                        doc.setFontSize(13);
                        doc.setFillColor(backgroundColor);
                        doc.rect(20, 110, 173, 7, 'F'); // Rectángulo de fondo gris claro
                        if (destino == "biblioteca") {
                            doc.text(20, 115, 'LIBROS PEDIDOS');
                        } else if (destino == "recursos") {
                            doc.text(20, 115, 'RECURSOS PEDIDOS');
                        } else if (destino == "meriendas") {
                            doc.text(20, 115, 'ARTICULOS PEDIDOS');
                        } else if (destino == "materiales") {
                            doc.text(20, 115, 'MATERIALES PEDIDOS');
                        } else {
                            doc.text(20, 115, 'LISTADO PEDIDO');
                        }
                        doc.line(20,117,193,117);

                        let contador = 1;
                        let posicionVertical = 125;
                        let currentPage = 1;
                        const maxWidth = doc.internal.pageSize.width - 30; // Ancho máximo del texto (margen izquierdo y derecho de 20)
                       
                        articulosPedidos.forEach(element => {
                            if (element.trim() != "" && element != null) {
                                const lines = doc.splitTextToSize(contador + ".- " + element, maxWidth);
                                if (posicionVertical + lines.length * 7 >= doc.internal.pageSize.height - 10) {
                                    doc.addPage();
                                    currentPage++;
                                    posicionVertical = 20; // Reiniciar la posición vertical en la nueva página
                                }
                                doc.setFontSize(10);
                                lines.forEach(line => {
                                    doc.text(20, posicionVertical, line);
                                    posicionVertical += 7;
                                });
                                contador++;
                            }
                        })
                        var pdfData = doc.output('blob');

                        // Abrir el PDF en una nueva pestaña
                        var url = URL.createObjectURL(pdfData);
                        window.open(url);
                    } catch (error) {
                        this.mostrarToast("Error", "No se pudo visualizar el archivo. Intente nuevamente");
                    }
                },
                formatearFecha (fecha) {
                    let dia = fecha.split(" ")[0];
                    //let hora = fecha.split(" ")[1];
                    dia = dia.split("-").reverse().join("-");

                    return dia ;
                },
                irAHome () {
                    window.location.href = 'home.php';    
                },
                irArriba () {
                    window.scrollTo(0, 0);   
                },
                         
                // END FUNCIONES PLANIFICACION
                mostrarToast(titulo, texto) {
                    app.tituloToast = titulo;
                    app.textoToast = texto;
                    var toast = document.getElementById("mitoast");
                    var tituloToast = document.getElementById("tituloToast");
                    toast.classList.remove("toast");
                    toast.classList.add("mostrar");
                    setTimeout(function(){ toast.classList.toggle("mostrar"); }, 10000);
                    if (titulo == 'Éxito') {
                        toast.classList.remove("bordeError");
                        toast.classList.add("bordeExito");
                        tituloToast.className = "exito";
                    } else {
                        toast.classList.remove("bordeExito");
                        toast.classList.add("bordeError");
                        tituloToast.className = "errorModal";
                    }
                },
                ocultarToast() {
                    this.tituloToast = "";
                    this.textoToast = "";
                    var toast = document.getElementById("mitoast");
                    toast.classList.remove("mostrar");
                    toast.classList.add("toast");
                }
            }
        })
        window.addEventListener('scroll', function(evt) {
            let blur = window.scrollY / 10;
            if (blur == 0) {
                app.scroll = false;
            } else {
                app.scroll = true;
            }
        }, false);
    </script>
</body>
</html>
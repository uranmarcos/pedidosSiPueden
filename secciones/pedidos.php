<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEDIDOS</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.21/vue.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.2.1/axios.min.js"></script>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="../css/tabla.css" rel="stylesheet">
    <link href="../css/opciones.css" rel="stylesheet">
    <link href="../css/modal.css" rel="stylesheet">

</head>
<body>
    
    <div id="app">

        <?php require("../shared/header.html")?>

        <div class="container">

            <div v-if="rol == 'admin'">
                <?php require("../shared/opciones.html")?>
            </div>


            <div class="breadcrumb">
                <span>
                    INICIO - PEDIDOS
                </span>

                <button type="button" @click="iniciarPedido()" class="btn boton">
                    Nuevo Pedido
                </button>
            </div>
            
            <!-- START COMPONENTE LOADING BUSCANDO PEDIDOS -->
            <div class="contenedorLoading" v-if="buscandoPedidos">
                <div class="loading">
                    <div class="spinner-border" role="status">
                        <span class="sr-only"></span>
                    </div>
                </div>
            </div>
            <!-- END COMPONENTE LOADING BUSCANDO PEDIDOS -->
           
            <!-- START TABLA PEDIDOS -->
            <div class="contenedorTabla" v-else>
                <div class="alert alert-dark" role="alert">
                    Transcurridos los 4 meses desde su envio, el pedido será eliminado. Si lo desea puede descargarlo para futuras consultas.
                </div>
                <div class="d-none d-sm-none d-md-block">
                    <table class="table table-hover ">
                        <thead class="tituloColumna">
                            <th class="hide">
                                ID
                            </th>
                            <th>
                                Sede
                            </th>
                            <th>
                                Usuario
                            </th>
                            <th>
                                Fecha
                            </th>
                            <th>
                                Enviado
                            </th>
                            <th style="width: 150px">
                                ACCIONES
                            </th>
                        </thead>
                        <tbody v-if="pedidos.length != 0">
                            <tr v-for="pedido in pedidos">
                                <td class="hide">
                                    {{pedido.id}}
                                </td>
                                <td>
                                    {{pedido.sede}}
                                </td>
                                <td>
                                    {{pedido.usuario}}
                                </td>
                                <td>
                                    <span v-html="formatDate(pedido.fecha)" class="spanFecha"></span>
                                </td>
                                <td>
                                    {{pedido.enviado != 0 ? "Sí" : "No"}}
                                </td>
                                <td>
                                    <button class="botonAccion botonEdit" @click="ver(pedido)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                        </svg>
                                    </button>
                                    <button class="botonAccion botonEdit" @click="descargar(pedido)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class=" d-block d-sm-block d-md-none">
                    <table class="table table-hover">
                        <div v-if="pedidos.length != 0">
                            <div v-for="pedido in pedidos" style="color: black; margin-left: 20px; border-bottom: 1px solid grey; margin-right: 20px; padding-bottom: 10px; padding-top: 10px;">
                                <div class="hide">
                                    {{pedido.id}}
                                </div>
                                <div>
                                    Sede: {{pedido.sede}}
                                </div>
                                <div>
                                    Usuario: {{pedido.usuario}}
                                </div>
                                <div>
                                    Fecha: <span v-html="formatDate(pedido.fecha)" class="spanFecha"></span>
                                </div>
                                <div>
                                    Enviado: {{pedido.enviado != 0 ? "Sí" : "No"}}
                                </div>
                                <div>
                                    <button class="botonAccion botonEdit" @click="ver(pedido)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                        </svg>
                                    </button>
                                    <button class="botonAccion botonEdit" @click="descargar(pedido)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </table>
                </div>
                <div v-if="pedidos.length == 0">
                    <span class="sinResultados">
                       NO SE ENCONTRÓ RESULTADOS PARA MOSTRAR
                    </span>
                </div>
            </div>
            <!-- END TABLA PEDIDOS -->

            <div v-if="modal">
                <!-- The Modal -->
                <div id="myModal" class="modal">

                <!-- Modal content -->
                <div class="modal-content">
                    <div class="">
                        <div class="row d-flex justify-content-center tituloModal">    
                            <h5 class="d-flex justify-content-center">CONFIRMACION</h5>
                        </div>

                        <div class="row d-flex justify-content-center my-3">
                            <b class="d-flex justify-content-center">¿Desea {{accionModal}} el articulo?</b>
                        </div>

                        <div class="row d-flex justify-content-center my-3">
                            Descripcion: {{app.descripcion}} 
                            <br>
                            Categoria: {{categorias.filter(element => element.id == app.categoria)[0]["descripcion"]}}
                            <br>
                            Medida: {{medidas.filter(element => element.id == app.medida)[0]["descripcion"]}}
                            <br>
                            Vigente: {{app.vigente == 0 ? "No" : "Sí"}}
                            <br>
                        </div>
                        
                        <div class="row d-flex justify-content-around">
                            <div class="col-sm-12 col-md-6 d-flex justify-content-center">
                                <button type="button" @click="cancelarModal()" class="btn boton" >Cancelar</button>
                            </div>

                            <div class="col-sm-12 col-md-6 d-flex justify-content-center mt-sm-3 mt-md-0">
                                <!-- BOTONES CONFIRMACION CREACION -->
                                <button type="button" @click="confirmarCreacion()" class="btn botonConfirm" v-if="accionModal == 'crear' && !creando">Crear</button>
                                <button type="button" class="btn botonConfirm" v-if="accionModal == 'crear' && creando">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only"></span>
                                    </div>
                                </button>
                                <!-- BOTONES CONFIRMACION CREACION -->

                                <!-- BOTONES CONFIRMACION EDICION -->
                                <button type="button" @click="confirmarEdicion()" class="btn botonConfirm" v-if="accionModal == 'modificar' && !editando">Editar</button>
                                <button type="button" class="btn botonConfirm" v-if="accionModal == 'modificar' && editando">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only"></span>
                                    </div>
                                </button>
                                <!-- BOTONES CONFIRMACION EDICION -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            </div>

            <div role="alert" id="mitoast" aria-live="assertive" aria-atomic="true" class="toast">
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

    </div>
    <style>
        .hide{
            display: none;
        }
        .fecha {
            height: 20px;
            line-height: 20px;
        }
        .spanFecha{
            height: 100% !important;
            margin: auto;
        }
        td{
            height: 40px !important;
        }
        .alert-dark{
            padding: 8px 16px;
            font-size: 14px;
            text-align: center;
            border: none;
            border-radius: 10px 10px 0 0;
        }
        #mitoast {
            visibility: hidden;
            position: fixed;
            z-index: 4;
            left:10px;
            bottom: 5%;
            border: 1px solid rgba(0,0,0,.1);
            border-radius: .25rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,.1);
            max-width: 250px;
            width: 250px;
            height: auto;
            /* background-color: #ffffff; */
            background-color: #F2F3F4;
            opacity: 1;
        }
        div.toast-content{
            min-height: 80px !important;
        }
        div.row.textoToast{
            min-height: 80px !important;
        }
        .bordeExito {
            border-left: 10px solid green !important;
        }
        .bordeError {
            border-left: 10px solid red !important;;
        }
        div.row.textoToast >> strong{
            display: flex;
            align-items: center
        }
        .exito {
            width: 100%;
            text-align: center;
            color: green;
            margin: 10px auto !important;
        }
        .errorModal {
            width: 100%;
            text-align: center;
            margin: 10px auto !important;
            color: red;
            border: none;
        }
        #mitoast.mostrar {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 4.6s;
            animation: fadein 0.5s, fadeout 0.5s 4.6s;
        }
        div.toast-header{
            text-align:center !important;
        }
 
        @keyframes fadein {
            0%   {background-color:white; left:10px; bottom:0px;}
            100% {background-color:white; left:10px; bottom:5%;}
        }
        .tituloToast{
            width: 100%;
            height: 20%;
            line-height: 20px;
            padding: 10px 0;
            margin: auto !important;
            text-align: center !important;
            color: green;
        }
        .textoToast{
            width: 100%;
            height: 80%;
            margin: auto;
            text-align: center;
            
        }
        .errorLabel{
            color: red;
            font-size: 10px;
        }






        /* ESTILOS LOADING */
        .contenedorLoading {
            color: rgb(124, 69, 153);
            border: solid 1px rgb(124, 69, 153);
            border-radius: 10px;
            padding: 10xp;
            margin-top: 24px;
            width: 100%;
            height: 200px;
        }
        .loading {
            width: 100%;
            height: 100% !important;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        /* ESTILOS LOADING */

    </style>
    <script>

        var app = new Vue({
            el: "#app",
            components: {
            },
            data: {
                tituloToast: "",
                textoToast: "",
                modal: false,
                idPedido:null,
                buscandoPedidos: false,
                //
                confirm: false,
                enviando: false,
                pedidos: [],
                rol: null
            },
            mounted: function() {
                this.consultarPedidos();
                this.rol = "<?php echo $_SESSION['rol'] ?>";
                if (this.rol == 'admin') {
                    document.getElementById("navPedidos").classList.add("active");
                }
            },
            methods:{
                iniciarPedido () {
                    window.location.href = 'http://localhost/proyectos/pedidos2/secciones/iniciar.php';   
                },
                formatDate(fecha) {
                    var nuevaFecha = "<b><div class='fecha'>" + fecha.split(" ")[0].split("-").reverse().join("-") + "</div></b>";
                    nuevaFecha = nuevaFecha + "<div class='fecha'>" + fecha.split(" ")[1] + " hs </div>";
                    return nuevaFecha;
                },
                // editar (articulo) {
                //     app.editable = true;
                //     app.mostrarABM = true;
                //     app.idArticulo = articulo.id;
                //     app.descripcion = articulo.descripcion;
                //     app.categoria = articulo.categoria;
                //     app.medida = articulo.medida;
                //     app.vigente = articulo.vigente;
                // },
                cancelarModal(){
                    app.modal = false;
                },
                // confirmarCreacion () {
                //     app.creando = true;
                //     let formdata = new FormData();
                //     formdata.append("descripcion", app.descripcion);
                //     formdata.append("medida", app.medida);
                //     formdata.append("categoria", app.categoria);
                //     axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=insertarArticulo", formdata)
                //     .then(function(response){
                //         if (response.data.error) {
                //             app.mostrarToast("Error", response.data.mensaje);
                //         } else {
                //             app.mostrarToast("Éxito", response.data.mensaje);
                //             app.modal = false;
                //             app.mostrarABM = false;
                //             app.descripcion = null;
                //             app.categoria = null;
                //             app.medida = null;
                //             app.consultarArticulos();
                //         }
                //         app.creando = false;
                //     }).catch( error => {
                //         app.creando = false;
                //         app.mostrarToast("Error", response.data.mensaje);
                //     })
                // },
                // confirmarEdicion () {
                //     app.editando = true;
                //     let formdata = new FormData();
                //     formdata.append("id", app.idArticulo);
                //     formdata.append("categoria", app.categoria);
                //     formdata.append("descripcion", app.descripcion);
                //     formdata.append("medida", app.medida);
                //     formdata.append("vigente", app.vigente);
                    
                //     axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=editarArticulo", formdata)
                //     .then(function(response){
                //         if (response.data.error) {
                //             app.mostrarToast("Error", response.data.mensaje);
                //         } else {
                //             app.mostrarToast("Éxito", response.data.mensaje);
                //             app.modal = false;
                //             app.mostrarABM = false;
                //             app.idArticulo= null;
                //             app.descripcion = null;
                //             app.categoria = null;
                //             app.medida = null;
                //             app.vigente = null;
                //             app.consultarArticulos();
                //         }
                //         app.editando = false;
                //     }).catch( error => {
                //         app.editando = false;
                //         app.mostrarToast("Error", response.data.mensaje);
                //     })
                // },
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
                consultarPedidos() {
                    this.buscandoPedidos = true;
                    axios.get("http://localhost/proyectos/pedidos2/conexion/api.php?accion=consultarPedidos")
                    .then(function(response){
                        console.log(response.data);
                        app.buscandoPedidos = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.pedidos = response.data.pedidos;
                        }
                    })
                    
                }
            }
        })
    </script>
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
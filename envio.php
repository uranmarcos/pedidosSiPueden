<?php
session_start();
$rol = "usuario";
if (!$_SESSION["login"] ) {
    header("Location: index.html");
}
if (!$_SESSION["pedido"] ) {
    header("Location: index.html");
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
    <!-- <meta charset="iso-8859-1"> -->
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
                    <span class="pointer mx-2" @click="irA('home')">
                        Inicio
                    </span>  
                    -
                    <span class="pointer mx-2" @click="irA(perfil)">
                        {{perfil}}
                    </span> 
                    -  
                    <span class="mx-2 grey"> Datos de envio </span>
                </div>
            </div>
            <!-- END BREADCRUMB -->
          

            <div class="contenedorABM" id="carrito">    
                <article>
                    <div class="mt-3 row rowDatos">
                        <div class="col-sm-12 col-md-6 pl-0 pr-0 pr-md-2">
                            <label for="nombre">Nombre Sí Pueden (*) <span class="errorLabel" v-if="errorNombre">{{errorNombre}}</span></label>
                            <input class="form-control" autocomplete="off" maxlength="60" id="nombre" v-model="envio.nombre">
                        </div>
                        <div class="col-sm-12 col-md-6  mt-3 mt-md-0 pl-0 pl-md-2">
                            <label for="nombre">Nombre y apellido del voluntario (*) <span class="errorLabel" v-if="errorNombreVoluntario">{{errorNombreVoluntario}}</span></label>
                            <input class="form-control" autocomplete="off" maxlength="60" id="nombreVoluntario" v-model="envio.nombreVoluntario">
                        </div>
                        <div class="col-12 subtitleEnvio">
                            <label>Dirección (del voluntario)</label>
                        </div>
                                
                        <div class="col-sm-12 col-md-6 mt-3">
                            <label for="direccion">Calle y número (*)<span class="errorLabel" v-if="errorDireccion">{{errorDireccion}}</span></label>
                            <input class="form-control" autocomplete="off" maxlength="50" id="direccion" v-model="envio.direccion">
                        </div>
                        <div class="col-6 col-md-3 mt-3">
                            <label for="piso">Piso</label>
                            <input class="form-control" autocomplete="off" maxlength="5" id="direccion" v-model="envio.piso">
                        </div>
                        <div class="col-6 col-md-3 mt-3">
                            <label for="dpto">Dpto.</label>
                            <input class="form-control" autocomplete="off" maxlength="5" id="ciudad" v-model="envio.dpto">
                        </div>
                        <div class="col-sm-12 col-md-6 mt-3">
                            <label for="ciudad">Ciudad (*) <span class="errorLabel" v-if="errorCiudad">{{errorCiudad}}</span></label>
                            <input class="form-control" autocomplete="off" maxlength="30" id="ciudad" v-model="envio.ciudad">
                        </div>
                        <div class="col-sm-12 col-md-6 mt-3">
                            <label for="provincia">Provincia (*) <span class="errorLabel" v-if="errorProvincia">{{errorProvincia}}</span></label>
                            <select class="form-control" name="provincia" id="provincia" v-model="envio.provincia">
                                <option v-for="provincia in provincias" v-bind:value="provincia" >{{provincia}}</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mt-3">
                            <label for="ciudad">Código Postal (*) <span class="errorLabel" v-if="errorCodigoPostal">{{errorCodigoPostal}}</span></label>
                            <input class="form-control" autocomplete="off" maxlength="8" id="codigoPostal" v-model="envio.codigoPostal">
                        </div>
                        <div class="col-sm-12 col-md-6 mt-3">
                            <label for="provincia">Teléfono (*) <span class="errorLabel" v-if="errorTelefono">{{errorTelefono}}</span></label>
                            <div class="row">
                                <div class="col-9 col-sm-3">
                                    <input class="form-control" autocomplete="off" maxlength="4" id="telefono" v-model="envio.caracteristica">
                                </div> 
                                <div class="col-3 col-sm-1">
                                    -
                                </div> 
                                <div class="col-12 col-sm-8">
                                    <input class="col-sm-9 form-control" autocomplete="off" maxlength="9" id="telefono" v-model="envio.telefono">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 mt-3 row rowBotonesEnvio">
                        <div class="col-12 py-2">
                            <input type="checkbox" v-model="recordarDatos">
                            <label @click="recordarDatos = !recordarDatos" class="pointer">Recordar datos para futuros envios</label>
                        </div>
                        <div class="col-12 px-0 d-flex justify-content-between">
                            <button type="button" @click="irA(perfil)" class="btn boton">
                                Volver
                            </button>
                            <button type="button" @click="continuar()" class="btn boton">
                                Generar pedido
                            </button>
                        </div>
                    </div>
                </article>                        
            </div>

            <div v-if="modalPedido">
                <div id="myModal" class="modal">
                    <div class="modal-content p-0">
                        <section v-if="enviarPedido">
                            <div class="modal-header  d-flex justify-content-center">
                                <h5 class="modal-title" id="ModalLabel">CONFIRMACIÓN</h5>
                            </div>

                            <div class="modal-body row d-flex justify-content-center" v-if="!loading">
                                <div class="col-12 confirmacion">
                                    ¿Desea enviar el pedido?
                                </div>    
                                <div class="col-12 descargarPedido" @click="descargarPedido = !descargarPedido" :class="descargarPedido ? 'selected' : ''">
                                    <input type="checkbox" v-model="descargarPedido" v-if="!descargarPedido">
                                    Descargar una copia del pedido al finalizar
                                </div>   
                            </div>   
                            
                            <div class="modal-body row d-flex justify-content-center" v-if="loading">
                                <div class="col-12 confirmacion">
                                    {{accionPedido}}
                                </div>    
                            </div>   

                            <div class="modal-footer d-flex justify-content-between" v-if="!pedidoEnviado">                                
                                <button type="button" class="btn boton botonResponsive" @click="cancelarModalPedido()" :disabled="loading">Cancelar</button>
                                
                                <button type="button" @click="confirmar()" class="btn boton botonResponsive" v-if="!loading">
                                    Confirmar
                                </button>

                                <button 
                                    class="btn boton"
                                    v-if="loading" 
                                >
                                    <div class="loading">
                                        <div class="spinner-border" role="status">
                                            <span class="sr-only"></span>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </section>

                        <section v-if="errorPdf || errorMail || errorGuardar">
                            <div class="modal-header d-flex justify-content-center">
                                <h5 class="modal-title error" id="ModalLabel">ERROR</h5>
                            </div>

                            <div class="modal-body row d-flex justify-content-center">
                                <div class="col-12 mensajeError">
                                    {{mensajeError}}
                                </div>    
                            </div>                            

                            <div class="modal-footer d-flex justify-content-between"> 
                                <button type="button" class="btn boton" @click="cancelarModalPedido()" :disabled="loading" data-dismiss="modal">Cancelar</button>
                                
                                <button type="button" @click="armarPdf()" class="btn boton" v-if="!loading && errorPdf">
                                    Reintentar
                                </button>
                                
                                <button type="button" @click="enviarMail()" class="btn boton" v-if="!loading && errorMail">
                                    Reintentar
                                </button>

                                <button type="button" @click="confirmar()" class="btn boton" v-if="!loading && errorGuardar">
                                    Reintentar
                                </button>

                                <button 
                                    class="btn boton"
                                    v-if="loading" 
                                >
                                    <div class="loading">
                                        <div class="spinner-border" role="status">
                                            <span class="sr-only"></span>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </section>

                        <section v-if="pedidoEnviado">
                            <div class="modal-header  d-flex justify-content-center">
                                <h5 class="modal-title" id="ModalLabel">CONFIRMACIÓN</h5>
                            </div>

                            <div class="modal-body row d-flex justify-content-center">
                                <div class="col-12 confirmacion">
                                    ¡El pedido se envió correctamente! :)
                                </div>    
                            </div>

                            <div class="modal-footer d-flex justify-content-center">
                                <button type="button" @click="terminar()" class="btn boton" v-if="!loading">
                                    Aceptar
                                </button>
                            </div>
                        </section>
                    </div>    
                </div>
            </div>
                
                          
            <!-- NOTIFICACION -->
            <div role="alert" id="mitoast" aria-live="assertive" @mouseover="ocultarToast" aria-atomic="true" class="toast">
                <div class="toast-header">
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

    <style scoped>  
        /* ABM LIBROS */
        .contenedorABM{
            width: 100%;
           padding: 0px;
           border: solid 1px grey;
           border-radius: 5px
        }
        .rowBotonesEnvio{
            width: 100%;
            padding: 0;
            margin-bottom: 12px;
        }
        .rowDatos{
            width: 100%;
            margin: auto;
            padding: 0;
        }
        .form-control{
            border: none !important;
            border-bottom: solid 1px lightgrey !important;
            border-radius: 0px;
        }
        .error{
            background: rgb(238, 100, 100) !important;
        }
        .mensajeError{
            display: flex;
            justify-content: center;
            text-align: center;
            color: rgb(238, 100, 100);;
        }
        label{
            font-size: 12px;
        }
        .rowBotones{
            width: 100%;
            margin:auto;
        }
        #mitoast{
            z-index:60;
        }
        .boton{
            border-radius: 5px !important;
            font-size: 0.8em;
            padding: 0 12px
        }
        .boton:hover{
            background: rgb(124, 69, 153);
            color: white;
            box-shadow: none
        }
        .descargarPedido{
            margin-top: 5px;
            text-align: center;
            font-size: 12px;
            color: rgb(124, 69, 153);
        }
        .descargarPedido:hover{
            cursor: pointer;
        }
        .selected:before {
            content: '✓';
        }  

        @media (max-width: 530px) {
            .modal-content{
                width: 90%;
                font-size: 0.7em;
            }
            .modal-title{
                font-size: 1em;
            }
            .modal-dialog {
                width: 90% !important;
            }
            .botonResponsive{
                width: 100%;
            }
        }
        @media (max-width: 700px) {
            .modal-content{
                width: 70%;
            }
            .modal-dialog {
                width: 90% !important;
            }
        }
    </style>
    <script>
        var app = new Vue({
            el: "#app",
            components: {                
            },
            data: {
                modalPedido:false,
                enviarPedido: false,
                errorPdf: false,
                errorMail: false,
                errorGuardar: false,
                pedidoEnviado: false,
                accionPedido: "", 
                pdf: null,
                descargarPedido: false,
                tituloToast: null,
                textoToast: null,
                provincias: [
                    "Buenos Aires",
                    "CABA",
                    "Catamarca",
                    "Chaco",
                    "Chubut",
                    "Córdoba",
                    "Corrientes",
                    "Entre Ríos",
                    "Formosa",
                    "Jujuy",
                    "La Pampa",
                    "La Rioja",
                    "Mendoza",
                    "Misiones",
                    "Neuquén",
                    "Río Negro",
                    "Salta",
                    "San Juan",
                    "San Luis",
                    "Santa Cruz",
                    "Santa Fe",
                    "Santiago del Estero",
                    "Tierra del Fuego",
                    "Tucumán"
                ],
                recordarDatos: false,
                envio: {
                    nombre: null,
                    nombreVoluntario: null,
                    direccion: null,
                    ciudad: null,
                    provincia: null,
                    codigoPostal: null,
                    caracteristica: null,
                    telefono: null,
                },
                errorNombre: null,
                errorDireccion: null,
                errorCiudad: null,
                errorProvincia: null,
                errorTelefono: null,
                errorCodigoPostal: null,
                errorNombreVoluntario: null,
                modal: false,
                loading: false,
                articulosPedidos: [],
                mensajeError: "",
                perfil: null,
                idPedido: null
            },
            mounted () {
                this.perfil = localStorage.getItem("perfil");
                console.log(this.perfil);
                let pedido = JSON.parse(localStorage.getItem("pedido"));
                if (pedido) {
                    this.articulosPedidos = pedido.articulos;
                }
                let envio = JSON.parse(localStorage.getItem("datosEnvio"));
                if (envio) {
                    this.envio.nombre = envio.nombre;
                    this.envio.nombreVoluntario = envio.nombreVoluntario;
                    this.envio.direccion = envio.direccion;
                    this.envio.piso = envio.piso;
                    this.envio.dpto = envio.dpto;
                    this.envio.ciudad = envio.ciudad;
                    this.envio.provincia = envio.provincia;
                    this.envio.codigoPostal = envio.codigoPostal;
                    this.envio.caracteristica = envio.caracteristica;
                    this.envio.telefono = envio.telefono;
                    this.recordarDatos = true;
                }
            },
            methods:{
                irA (destino) {
                    switch (destino) {
                        case "home":
                            window.location.href = 'home.php';    
                            break;
                        case "biblioteca":
                            window.location.href = 'banco.php';    
                            break;
                        case "meriendas":
                            window.location.href = 'meriendas.php';    
                            break;
                        case "recursos":
                            window.location.href = 'banco.php';    
                            break;
                        case "planificaciones":
                            window.location.href = 'banco.php';    
                            break;
                        case "materiales":
                            window.location.href = 'materiales.php';    
                            break;
                    
                        default:
                            break;
                    }
                },
                continuar () {
                    this.modalPedido = false;
                  
                    this.resetErrores();
                    if (this.envio.nombre != null && this.envio.nombre.trim() != '' &&
                        this.envio.nombreVoluntario != null && this.envio.nombreVoluntario.trim() != '' &&
                        this.envio.direccion != null && this.envio.direccion.trim() != '' &&
                        this.envio.ciudad != null && this.envio.ciudad.trim() != '' &&
                        this.envio.provincia != null && this.envio.provincia.trim() != '' &&
                        this.envio.codigoPostal != null && this.envio.codigoPostal.trim() != '' &&
                        this.envio.caracteristica != null && this.envio.caracteristica.trim() != '' &&
                        this.envio.telefono != null && this.envio.telefono.trim() != '')
                    {
                        this.modalPedido = true;
                        this.enviarPedido = true;
                        if (this.recordarDatos) {
                            localStorage.setItem("datosEnvio", JSON.stringify(this.envio))
                        } else {
                            localStorage.removeItem("datosEnvio")
                        }
                    } else {
                        if (this.envio.nombre == null || this.envio.nombre.trim() == '') {
                            this.errorNombre = "Campo requerido";
                        }
                        if (this.envio.nombreVoluntario == null || this.envio.nombreVoluntario.trim() == '') {
                            this.errorNombreVoluntario = "Campo requerido";
                        }
                        if (this.envio.direccion == null || this.envio.direccion.trim() == '') {
                            this.errorDireccion = "Campo requerido";
                        }
                        if (this.envio.ciudad == null || this.envio.ciudad.trim() == '') {
                            this.errorCiudad = "Campo requerido";
                        }
                        if (this.envio.provincia == null || this.envio.provincia.trim() == '') {
                            this.errorProvincia = "Campo requerido";
                        }
                        if (this.envio.codigoPostal == null || this.envio.codigoPostal.trim() == '') {
                            this.errorCodigoPostal = "Campo requerido";
                        }
                        if (this.envio.caracteristica == null || this.envio.caracteristica.trim() == '' || this.envio.telefono == null || this.envio.telefono.trim() == '') {
                            this.errorTelefono = "Campo requerido";
                        }
                    }
                },
                resetErrores() {
                    this.errorNombre= null
                    this.errorDireccion= null
                    this.errorCiudad= null
                    this.errorProvincia= null
                    this.errorTelefono= null
                    this.errorCodigoPostal= null                
                },
                cancelarModalPedido() {
                    this.modalPedido = false;
                    this.loading= false;
                    this.errorMail = false;
                    this.accionPedido= "";
                    this.enviarPedido = false;
                    this.mensajeError = "";
                    this.errorPdf = false;
                    this.errorMail = false;
                },
                confirmar () {
                    this.loading = true;
                    this.errorGuardar = false;
                    this.accionPedido = "Guardando el pedido..."
                    let formdata = new FormData();
                        const tiempoTranscurrido = Date.now();
                        const hoy = new Date(tiempoTranscurrido);
                        let fecha = hoy.getDate() + "/" + (hoy.getMonth() + 1) + "/" + hoy.getFullYear();

                    formdata.append("nombreSiPueden", this.envio.nombre);
                    formdata.append("nombreVoluntario", this.envio.nombreVoluntario);
                    formdata.append("direccionEnvio", this.envio.direccion);
                    
                    let direccion = this.envio.direccion;
                    if (this.envio.piso != null && this.envio.piso.trim() != '') {
                        direccion = direccion + ". Piso: " + this.envio.piso;
                    }
                    if (this.envio.dpto != null && this.envio.dpto.trim() != '') {
                        direccion = direccion + ". Dpto: " + this.envio.dpto;
                    }
                    formdata.append("direccionEnvio", direccion);

                    formdata.append("ciudad", this.envio.ciudad);
                    formdata.append("provincia", this.envio.provincia);
                    formdata.append("codigoPostal", this.envio.codigoPostal);
                    formdata.append("telefono", this.envio.caracteristica + " - " + this.envio.telefono );
                    formdata.append("fecha", fecha);
                    
                    // // formdata.append("mail", "marcos_uran@hotmail.com");
                    // formdata.append("mail", "gdfgdfgdf@gdfgdgd.com");
                    // // formdata.append("mail", "biblioteca@fundacionsi.org.ar");
                    // "laurapecorelli@hotmail.com.ar"
                    
                    let pedido = '';
                    
                    if (this.perfil == "materiales") {
                        this.articulosPedidos.forEach(element => {
                            if (element.cantidad != null && element.cantidad != false && element.cantidad != 0 && element.categoria != 'otros') {
                                let elemento = '';
                                if (element.medible) {
                                    elemento = element.nombre + ": " + element.cantidad + "; "; 
                                } else {
                                    elemento = element.nombre + "; ";
                                }
                                  
                                pedido = pedido + elemento;
                            }
                            if (element.cantidad != null && element.categoria == 'otros') {
                                otros = element.cantidad;
                                pedido = pedido + ", otros : " + otros;
                            }
                        });
                    } else {
                        this.articulosPedidos.forEach(element => {                              
                            pedido = pedido + element.nombre+ "; ";
                        });
                    }

                    let pedidoModificado = pedido.replaceAll("'", "**");
                    formdata.append("pedido", pedidoModificado);
                    formdata.append("destino", this.perfil);                    
                    axios.post("funciones/acciones.php?accion=guardarPedido", formdata)
                    .then(function(response){  
                        if (response.data.error) {
                            app.enviarPedido = false;
                            app.mensajeError = response.data.mensaje;
                            app.errorGuardar = true;
                            app.loading = false;
                        } else {
                            app.idPedido = response.data.mensaje;
                            app.armarPdf();
                        }
                    }).catch( error => {
                        app.mensajeError = "Hubo un error, por favor intente nuevamente.";
                        app.errorGuardar = true;
                        app.loading = false;
                    })          
                },
                armarPdf(){
                    this.loading = true;
                    this.errorPdf = false;
                    this.accionPedido = "Armando el pdf...";
                    try {
                        // VARIABLES A UTILIZAR
                        const tiempoTranscurrido = Date.now();
                        const hoy = new Date(tiempoTranscurrido);
                        let fecha = hoy.getDate() + "/" + (hoy.getMonth() + 1) + "/" + hoy.getFullYear();
    
                        const font = 'Arial';
                        const backgroundColor = '#F2F2F2'; // Color de fondo gris claro
                        const doc = new jsPDF();
                        
                        doc.setFont(font);
                        
                        var image = new Image()
    
                        image.src = 'img/logohor.jpg'
    
                        doc.addImage(image,80,10,50,16)
    
                        doc.setFontSize(11);
                        doc.text(175, 35, fecha );
                        
                        doc.setFontSize(12);
                        doc.text(20, 45, 'Nuevo pedido de: ');
                        doc.setFontSize(13);
                        doc.text(20, 53, this.envio.nombre.toUpperCase());
                        
                        doc.setFontSize(13);
                        doc.setFillColor(backgroundColor);
                        doc.rect(20, 60, 173, 7, 'F'); // Rectángulo de fondo gris claro
                        doc.text(20, 65, 'DATOS DE ENVIO');
                        doc.line(20,67,193,67);
    
                        
                        doc.setFontSize(10);
    
                        doc.setFontType('bold');
                        doc.text(20, 74, 'Voluntario:');
    
                        doc.setFontType('regular');
                        doc.text(50, 74, this.envio.nombreVoluntario);
    
    
                        doc.setFontType('bold');
                        doc.text(20, 81, 'Dirección: ');
    
                        doc.setFontType('regular');
                        doc.text(50, 81, this.envio.direccion);
    
                        doc.setFontType('bold');
                        doc.text(20, 88, 'Piso: ');
    
                        doc.setFontType('regular');
                        doc.text(50, 88, this.envio.piso ? this.envio.piso : "-");
    
                        doc.setFontType('bold');
                        doc.text(80, 88, 'Dpto: ');
    
                        doc.setFontType('regular');
                        doc.text(100, 88, this.envio.dpto ? this.envio.dpto : "-");
    
    
                        doc.setFontType('bold');
                        doc.text(20, 95, 'Ciudad/Provincia: ');
    
                        doc.setFontType('regular');
                        doc.text(50, 95, this.envio.ciudad + " / " +this.envio.provincia);
                        
    
                        doc.setFontType('bold');
                        doc.text(20, 101, 'Código postal: ');
    
                        doc.setFontType('regular');
                        doc.text(50, 101, this.envio.codigoPostal);
    
    
                        doc.setFontType('bold');
                        doc.text(20, 108, 'Teléfono:');
    
                        doc.setFontType('regular');
                        doc.text(50, 108, this.envio.caracteristica + "-" + this.envio.telefono);
    
    
                        doc.setFontSize(13);
                        doc.setFillColor(backgroundColor);
                        doc.rect(20, 113, 173, 7, 'F'); 
                        if (this.perfil == "biblioteca") {
                            doc.text(20, 118, 'LIBROS PEDIDOS');
                        }
                        if (this.perfil == "recursos") {
                            doc.text(20, 118, 'RECURSOS PEDIDOS');
                        }
                        if (this.perfil == "meriendas") {
                            doc.text(20, 118, 'ARTICULOS PEDIDOS');
                        }
                        if (this.perfil == "materiales") {
                            doc.text(20, 118, 'MATERIALES PEDIDOS');
                        }
                        doc.line(20,120,193,120);

                        let contador = 1;
                        let posicionVertical = 127;
                        let currentPage = 1;
                        const maxWidth = doc.internal.pageSize.width - 30; // Ancho máximo del texto (margen izquierdo y derecho de 20)

                        if (this.perfil == "materiales") {
                            this.articulosPedidos.forEach(element => {
                                if (element.cantidad != null && element.cantidad != false && element.cantidad != 0 && element.categoria != 'otros') {
                                const lines = doc.splitTextToSize(contador + ".- " + element.nombre + ": " + element.cantidad, maxWidth);
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
                                if (element.cantidad != null && element.categoria == 'otros') {
                                const lines = doc.splitTextToSize(contador + ".- otros : " + otros, maxWidth);
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
                            });
                        } else {
                            this.articulosPedidos.forEach(element => {
                                const lines = doc.splitTextToSize(contador + ".- " + element.nombre, maxWidth);
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
                            });
                        }
                        this.pdf = doc
                    } catch (error) {
                        this.enviarPedido = false;
                        this.loading = false;
                        this.errorPdf = true;
                        this.mensajeError = "El pedido se guardó (PEDIDO NÚMERO: " + this.idPedido + "), pero hubo un error y no se pudo enviar. Por favor presioná 'REINTENTAR'. Si el problema se mantiene avisanos, pero no es necesario que cargues nuevamente el pedido."
                        return;
                    }
                    this.enviarMail();
                },
                enviarMail() {
                    this.enviarPedido = true;
                    this.loading = true;
                    this.errorPdf = false;
                    this.mensajeError = "";
                    this.accionPedido = "Enviando el mail..."
                    this.errorMail = false;

                    var pdfBlob = this.pdf.output('blob');
                    // this.pdf.save('luceritos');
                  
                    // Convertir el Blob en una matriz de bytes
                    var reader = new FileReader();
                    reader.onloadend = function() {
                        var pdfBytes = new Uint8Array(reader.result);

                        // Crear un objeto FormData
                        let formdata = new FormData();

                        const tiempoTranscurrido = Date.now();
                        const hoy = new Date(tiempoTranscurrido);
                        let fecha = hoy.getDate() + "/" + (hoy.getMonth() + 1) + "/" + hoy.getFullYear();

                        formdata.append("nombreSiPueden", app.envio.nombre);
                        formdata.append("ciudad", app.envio.ciudad);
                        formdata.append("provincia", app.envio.provincia);                        
                        formdata.append("fecha", fecha);
                        
                        if (this.perfil == "biblioteca") {
                            formdata.append("mail", "biblioteca@fundacionsi.org.ar");
                        } else if (this.perfil == "materiales") {
                            formdata.append("mail", "laurapecorelli@hotmail.com.ar");
                        } else if (this.perfil == "recursos") {
                            formdata.append("mail", "biblioteca@fundacionsi.org.ar");
                        } else {
                            formdata.append("mail", "giribone@fundacionsi.org.ar");
                        }

                        // if (app.perfil == "biblioteca") {
                        //     formdata.append("mail", "uranmarcos@gmail.com");
                        // } else if (app.perfil == "materiales") {
                        //     formdata.append("mail", "marcos_uran@hotmail.com");
                        // } else if (app.perfil == "recursos") {
                        //     formdata.append("mail", "uranmarcos@gmail.com");
                        // } else {
                        //     formdata.append("mail", "cordoba@fundacionsi.org.ar");
                        // }
                        // Agregar los datos adicionales
                    
                        // Agregar el archivo PDF
                        formdata.append('archivoPdf', new Blob([pdfBytes], { type: 'application/pdf' }), app.envio.nombre+'.pdf');

                        // Enviar la solicitud POST al servidor
                        axios.post("funciones/acciones.php?accion=enviarMail", formdata)
                        .then(response => {
                            if (response.data.error) {
                                app.enviarPedido = false;
                                app.errorMail = true;
                                app.mensajeError = "El pedido se guardó (PEDIDO NÚMERO: " + this.idPedido + "), pero hubo un error y no se pudo enviar. Por favor presioná 'REINTENTAR'. Si el problema se mantiene avisanos, pero no es necesario que cargues nuevamente el pedido."  
                                app.errorGuardar = false;
                                app.pedidoEnviado= false;
                                app.loading = false;
                            } else {
                                if (app.descargarPedido) {
                                    app.pdf.save(app.envio.nombre+'.pdf');
                                }
                                app.enviarPedido = false;
                                app.errorMail = false;
                                app.mensajeError = "";
                                app.errorGuardar = false;
                                app.loading = false;
                                app.accionPedido = "";
                                app.pedidoEnviado = true;
                                app.verCarrito = false;
                                app.pdf = null;
                                app.descargarPedido= false;
                                app.idPedido = null;
                                localStorage.removeItem("pedido");
                                localStorage.removeItem("listadoPedido");
                            }
                        })
                        .catch(error => {
                            app.mensajeError = "El pedido se guardó (PEDIDO NÚMERO: " + this.idPedido + "), pero hubo un error y no se pudo enviar. Por favor presioná 'REINTENTAR'. Si el problema se mantiene avisanos, pero no es necesario que cargues nuevamente el pedido."  
                            app.errorMail = true;
                            app.pedidoEnviado = false;
                            app.loading = false;
                        });
                    };
                    reader.readAsArrayBuffer(pdfBlob);
                },
                terminar () {
                    window.location.href = 'home.php'; 
                },
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
    </script>
</body>
</html>
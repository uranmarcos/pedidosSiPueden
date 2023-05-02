<?php
session_start();
$rol = "usuario";
if (!$_SESSION["login"] ) {
    header("Location: index.html");
}
if ($_SESSION["rol"] == "admin" ) {
    $rol = "admin";
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
</head>
<body>
    <div id="app">
        <?php require("shared/header.html")?>
        
        <div class="container">
            <!-- START BREADCRUMB -->
            <div class="col-12 p-0">
                <div class="breadcrumb">
                    <span class="pointer mr-2" @click="irAHome()">Inicio</span>  -  <span class="ml-2 grey"> Desayunos / Meriendas </span>
                </div>
            </div>
            <!-- END BREADCRUMB -->           

            <div class="row mt-6">
                <div class="col-12">

                    <div class="contenedorABM py-3" v-if="verListado">    
                        <span class="subtituloCard">ARTICULOS DISPONIBLES</span>
                        <div class="row contenedorLibros d-flex justify-content-around">
                            <article class="col-10 col-md-5 articulo" :class="articulo.checked ? 'remarcado' : ''" @click="articulo.checked = !articulo.checked, habilitarAvanzar()" v-for="articulo  in articulos">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16" v-if="articulo.checked">
                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                                </svg>
                                <div class="textoArticulo">{{articulo.nombre}}</div>
                            </article>
                            <div class="col-10 col-md-5 d-flex justify-content-center align-items-center">
                                <button type="button" @click="avanzar()" :disabled="habilitarBtnAvanzar" class="btn boton" v-if="!loading">
                                    Continuar
                                </button>
                            </div>
                        </div>
                                           
                    </div>

                    <div class="contenedorABM py-3" v-if="verEnvio">  
                        <article>
                            <span class="subtituloCard">DATOS PARA EL ENVIO</span>
                            <div class="px-3 mt-3 row">
                                <div class="col-sm-12 col-md-6">
                                    <label for="nombre">Nombre Sí Pueden (*) <span class="errorLabel" v-if="errorNombre">{{errorNombre}}</span></label>
                                    <input class="form-control" autocomplete="off" maxlength="60" id="nombre" v-model="envio.nombre">
                                </div>
                                <div class="col-sm-12 col-md-6  mt-3 mt-md-0">
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
                                <div class="col-sm-6 col-md-3 mt-3">
                                    <label for="piso">Piso</label>
                                    <input class="form-control" autocomplete="off" maxlength="5" id="direccion" v-model="envio.piso">
                                </div>
                                <div class="col-sm-6 col-md-3 mt-3">
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
                                        <div class="col-3">
                                            <input class="form-control" autocomplete="off" maxlength="4" id="telefono" v-model="envio.caracteristica">
                                        </div> 
                                        <div class="col-1">
                                            -
                                        </div> 
                                        <div class="col-8">
                                            <input class="col-sm-9 form-control" autocomplete="off" maxlength="9" id="telefono" v-model="envio.telefono">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-3 mt-3 row rowBotonesEnvio">
                                <div class="col-6 p-0">
                                    <input type="checkbox" v-model="recordarDatos">
                                    <label @click="recordarDatos = !recordarDatos" class="pointer">Recordar</label>
                                </div>
                                <div class="col-12 pr-0 d-flex justify-content-between">
                                    <button type="button" @click="volver()" class="btn boton">
                                         Volver
                                    </button>
                                    <button type="button" @click="continuar()" class="btn boton">
                                        Generar pedido
                                    </button>
                                </div>
                            </div>
                        </article>    
                    </div>



                </div>

              

             

                <!-- Modal -->
                <div v-if="modalPedido">
                    <div id="myModal" class="modal">
                        <div class="modal-content p-0">
                            <div class="modal-header  d-flex justify-content-center">
                                <h5 class="modal-title" id="ModalLabel">CONFIRMACIÓN</h5>
                            </div>

                            <div class="modal-body row d-flex justify-content-center" v-if="!errorEnvio && !pedidoEnviado">
                                <div class="col-12 confirmacion">
                                    ¿Desea enviar el pedido?
                                </div>    
                                <div class="col-12 copia">
                                    <input type="checkbox" @click="mailCopia = null" v-model="enviarCopia">
                                    <label>Quiero recibir una copia en mi correo</label>
                                </div>                              <br>    
                                <div class="col-12" v-if="enviarCopia">
                                    <input class="inputCopia" :class="errorMail ? 'inputError' : ''" type="text" v-model="mailCopia">
                                </div>
                            </div>

                            <div class="modal-body row d-flex justify-content-center" v-if="errorEnvio">
                                <div class="col-12 confirmacion">
                                    Hubo un error y el pedido no se pudo enviar. Por favor intente nuevamente.
                                </div>    
                            </div>

                            <div class="modal-body row d-flex justify-content-center" v-if="pedidoEnviado && !errorEnvio">
                                <div class="col-12 confirmacion">
                                    ¡El pedido se envió correctamente! :)
                                </div>    
                            </div>
                            

                            <div class="modal-footer d-flex justify-content-between" v-if="!pedidoEnviado">
                                <button type="button" class="btn boton" @click="errorEnvio =false, modalPedido= false" :disabled="loading" data-dismiss="modal">Cancelar</button>
                                
                                <button type="button" @click="confirmar()" class="btn boton" v-if="!loading">
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

                            <div class="modal-footer d-flex justify-content-center" v-if="!errorEnvio && pedidoEnviado">

                                <button type="button" @click="terminar()" class="btn boton" v-if="!loading">
                                    Aceptar
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
                
                          
                <!-- NOTIFICACION -->
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
    </div>

    <style scoped>  
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
        /* ABM LIBROS */
        .contenedorABM{
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
            border: solid 1px #7C4599;
            border-radius: 5px;
        }


        /*  color: #7C4599;  LIBROS */
        .articulo{
            height:50px;
            border: solid 1px grey;
            border-radius: 10px;
            margin: 10px 0px;
            display:flex;
            color: grey;
            font-weight: bolder;
            align-items: center;
            justify-content: center;
            padding: 0!important;
        }
        .articulo:hover{
            cursor: pointer;
        }
        .remarcado{
            background-color: #7C4599;
            color: white;
            border: solid 1px #7C4599;
        }
        .textoArticulo{
            font-size: 1em;
            margin-top:5px;
            text-transform: uppercase;
            text-align: center;
            padding-left: 5px;
        }
        .rowCard{
            border-radius: 5px;
            border: solid 1px grey;
            padding: 2px;
            width: 99%;
            height:100%;
            margin:auto;
        }
        .contenedorLibros{
            width: 100%;
            margin:10px auto;
        }

        #mitoast{
            z-index:60;
        }
    </style>
    <script>
        var app = new Vue({
            el: "#app",
            components: {                
            },
            data: {
                modalPedido:false,
                habilitarBtnAvanzar: true,
                verListado:true,
                verEnvio: false,
                mailCopia: null,
                errorEnvio: false,
                pedidoEnviado: false,
                errorMail: false,
                tituloToast: null,
                textoToast: null,
                recordarDatos: false,
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
                //
                articulos: [
                    {
                        nombre: "Alfajores",
                        checked: false
                    },
                    {
                        nombre: "Cacao",
                        checked: false
                    },
                    {
                        nombre: "Galletitas",
                        checked: false
                    },
                    {
                        nombre: "Leche",
                        checked: false
                    },
                    {
                        nombre: "Mate cocido",
                        checked: false
                    },
                    {
                        nombre: "Té",
                        checked: false
                    },
                    {
                        nombre: "Turrones",
                        checked: false
                    }
                ],
                categorias: [],
                libro: {
                    nombre: null,
                    imagen: null,
                    nombreImagen: null,
                    descripcion: null,
                    categoria: null
                },
                modal: false,
                modalLibros: false,
                //
                loading: false,
                loadingLibro: false,
                confirmLibro: false,
                enviarCopia: false,
                librosPedidos: [],
            },
            mounted() {
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
                habilitarAvanzar () {
                    let marcados = this.articulos.filter(element => element.checked);
                    if (marcados.length != 0) {
                        return this.habilitarBtnAvanzar = false
                    } 
                    this.habilitarBtnAvanzar = true;
                },
                avanzar () {
                    this.verListado = false;
                    this.verEnvio = true;
                },
                volver () {
                    this.verListado = true;
                    this.verEnvio = false;
                },
                irAHome () {
                    window.location.href = 'home.php';    
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
                validarMail (mail) {
                    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
                        return (true)
                    }
                    return (false)
                },
                confirmar () {
                    this.errorMail = false;
                    if (this.enviarCopia) {
                        if (this.mailCopia == null || this.mailCopia.trim() == '') {
                            this.errorMail = true;
                            return;
                        } else {
                            if (!this.validarMail(this.mailCopia)) {
                                this.errorMail = true;
                                return;
                            }
                        }
                    }
                    this.loading= true;
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
                    // formdata.append("mail", "marcos_uran@hotmail.com");
                    formdata.append("mail", "giribone@fundacionsi.org.ar");
                    formdata.append("mailCopia", this.mailCopia);
                    
                    let pedido = '';
                  
                    this.articulos.forEach(element => {  
                        if (element.checked) {
                            pedido = pedido + element.nombre + "; ";
                        }                            
                    });
                    formdata.append("pedido", pedido);
                   
                   
                    axios.post("funciones/acciones.php?accion=enviarPedidoMeriendas", formdata)
                    .then(function(response){    
                        if (response.data.error) {
                            app.errorEnvio= true;
                            app.pedidoEnviado= false;
                        } else {
                            app.errorEnvio = false;
                            app.pedidoEnviado = true;
                        }
                        app.loading = false;
                    }).catch( error => {
                        app.errorEnvio= true;
                        app.pedidoEnviado= false;
                        app.loading = false;
                    })                
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
            }
        })
    </script>
</body>
</html>
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
    <script src="funciones/pdf.js" crossorigin="anonymous"></script>
</head>
<body>
    <div id="app">
        <?php require("shared/header.html")?>
        
        <div class="container">
            <!-- START BREADCRUMB -->
            <div class="col-12 p-0">
                <div class="breadcrumb">
                    <span class="pointer mx-2" @click="irA('home')">Inicio</span> 
                    -
                    <span class="pointer mx-2" @click="irA(perfil)">
                        {{perfil}}
                    </span> 
                    -  
                    <span class="mx-2 grey">
                        CREACIÓN
                    </span>
                </div>
            </div>
            <!-- END BREADCRUMB -->

            <!-- START OPCIONES -->
            <nav class="nav">
                <span
                    class="opcionNav"
                    :class="perfil == 'biblioteca' ? 'active' : '' "
                    @click = "cambiarPerfil('biblioteca')"
                >
                    NUEVO LIBRO
                </span>
                <span 
                    class="opcionNav"
                    :class="perfil == 'recursos' ? 'active' : '' "
                    @click = "cambiarPerfil('recursos')"    
                >
                    NUEVO RECURSO
                </span>
                <span 
                    class="opcionNav"
                    :class="perfil == 'videos' ? 'active' : '' "
                    @click = "cambiarPerfil('videos')"    
                >
                    NUEVO VIDEO
                </span>
                <span 
                    class="opcionNav" 
                    :class="perfil == 'planificaciones' ? 'active' : '' "
                    @click = "cambiarPerfil('planificaciones')"
                >
                    NUEVA PLANIFICACIÓN
                </span>
            </nav>
            <!-- END OPCIONES -->

            <!-- START COMPONENTE CREACION -->
            <div class="contenedorABM">    
                <div class="titleABM">
                    {{perfil == "biblioteca" ? 'NUEVO LIBRO' : perfil == "recursos" ? 'NUEVO RECURSO' : perfil == "videos" ? 'NUEVO VIDEO' : "NUEVA PLANIFICACIÓN"}}

                    <button type="button" class="btn botonAgregar" @click="modal=true" >
                        AGREGAR CATEGORIA
                    </button> 
                </div>

                <div class="row rowBotones d-flex justify-content-center">
                    <div class="col-sm-12 col-md-3 mt-3"  v-if="perfil != 'planificaciones' && perfil != 'videos'">
                        <div class="previsualizacion">
                            <img id="imagenPrevisualizacion" src="img/sinImagen.png" alt="sin imagen">   
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-9 mt-3">
                        <div class="pr-3" v-if="perfil != 'videos'">
                            <label for="nombre">
                                {{perfil == 'planificaciones' ? 'Archivo (*)' : 'Imagen (*)'}} 
                                <span class="errorLabel" v-if="errorImagen">
                                    {{errorImagen}}
                                </span>
                            </label>                 
                            <input 
                                class="form-control" 
                                type="file" 
                              
                                :accept = "perfil == 'planificaciones' ? '.pdf' : 'image/*'"
                                capture
                                name="imagen"
                                ref="imagen"
                                @change="processFile($event)"
                                id="seleccionArchivos" 
                                :value="articulo.nombreImagen"
                                @input="updateNombreImagen($event.target.value)"
                                
                            >
                        </div>
                        <div class="pr-3" v-else>
                            <label for="nombre">
                                Link (*)
                                <span class="errorLabel" v-if="errorImagen">
                                    {{errorImagen}}
                                </span>
                            </label>    
                            <input class="form-control" autocomplete="off" id="link" v-model="articulo.archivo">
                        </div>
                        <div class="mt-2">
                            <label for="nombre">Nombre (*) <span class="errorLabel" v-if="errorNombre">{{errorNombre}}</span></label>
                            <input class="form-control" autocomplete="off" maxlength="60" id="nombre" v-model="articulo.nombre">
                        </div>
                        <div>
                            <label for="nombre">Descripción (*) <span class="errorLabel" v-if="errorDescripcion">{{errorDescripcion}}</span></label>
                            <textarea class="form-control textareaDescripcion" maxlength="700" v-model="articulo.descripcion"></textarea>
                        </div>
                    </div>

                    <!-- <div class="col-sm-12"> -->
                    <div :class="perfil == 'planificaciones' || perfil == 'videos' ? 'col-3' : 'col-12'">
                        <div class="mt-2">
                            <label for="nombre">Categoria (*) 
                            <span class="errorLabel" v-if="errorCategoria">{{errorCategoria}}</span></label>
                            <div class="row my-3 contenedorCategorias" v-if="!buscandoCategorias">
                                <div 
                                    class="chip my-1" 
                                    :class="categoria.checked ? 'selected' : ''"
                                    v-for="(categoria, index) in categorias" 
                                    :key="index" 
                                    @click="changeCategoria(index)"
                                >
                                    <span>
                                        {{ categoria.nombre }}
                                    </span>
                                </div>
                            </div> 
                            <div class="contenedorLoading" v-if="buscandoCategorias">
                                <div class="loading">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only"></span>
                                    </div>
                                </div>
                            </div>                           
                        </div>
                    </div>
                </div>                   

                <div class="footerABM">
                    <button type="button" class="botonGeneral botonEliminar" @click="irA(perfil)">VOLVER</button>
                    <button type="button" @click="crearArticulo" class="botonGeneral">
                        CREAR
                    </button>
                </div>                
            </div>
            <!-- END COMPONENTE CREACION -->
           

            <div class="row mt-6">
                <!-- MODAL CATEGORIAS -->
                <div v-if="modal">
                    <div id="myModal" class="modal">
                        <div class="modal-content p-0">
                            <div class="modal-header  d-flex justify-content-center">
                                <h5 class="modal-title" id="ModalLabel">NUEVA CATEGORIA</h5>
                            </div>

                            <div class="modal-body row d-flex justify-content-center">
                                <div class="col-sm-12 mt-3">
                                    
                                    <div class="row rowCategoria d-flex justify-space-around">
                                        <label for="nombre" class="labelCategoria">Nombre categoria(*)</label>
                                        <input class="inputCategoria" :disabled="confirmCategorias" @input="errorNuevaCategoria = false" v-model="nuevaCategoria">
                                    </div>
                                    <span class="errorLabel" v-if="errorNuevaCategoria">Campo requerido</span>
                                </div>
                                <select class="form-control verCategorias">
                                    <option value="0" style="color: light-grey" >VER CATEGORIAS CREADAS</option>
                                    <option v-for="categoria in categorias">{{categoria.nombre}}</option>
                                </select>
                                
                            </div>


                            <div class="modal-footer d-flex justify-content-between" v-if="!confirmCategorias">
                                <button type="button" class="botonGeneral botonEliminar" @click="cancelarCategorias" data-dismiss="modal">CANCELAR</button>
                                
                                <button type="button" @click="confirmarNuevaCategoria" class="botonGeneral">
                                    CREAR
                                </button>
                            </div>

                            <div class="modal-footer d-flex justify-content-between" v-if="confirmCategorias">
                                <div class="row rowBotones f-dlex justify-content-center my-3">
                                    ¿Confirma la creación de la categoria?
                                </div>

                                <button type="button" class="botonGeneral botonEliminar" :disabled="creandoCategorias" @click="confirmCategorias = false">CANCELAR</button>
                                
                                <button type="button" @click="crearCategorias" class="botonGeneral" v-if="!creandoCategorias">
                                    CONFIRMAR
                                </button>

                                <button 
                                    class="botonGeneral"
                                    v-if="creandoCategorias" 
                                >
                                    <div class="creandoCategorias">
                                        <div class="spinner-border" role="status">
                                            <span class="sr-only"></span>
                                        </div>
                                    </div>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>    
                <!-- MODAL CATEGORIAS -->

                <!-- MODAL CONFIRMACION ARTICULO -->
                <div v-if="modalArticulo">
                    <div id="myModal" class="modal">
                        <div class="modal-content p-0">
                            <div class="modal-header  d-flex justify-content-center">
                                <h5 class="modal-title" id="ModalLabel">
                                    CONFIRMACIÓN
                                </h5>
                            </div>

                            <div class="modal-body row d-flex justify-content-center">
                                ¿Desea crear {{perfil == 'biblioteca' ? ' el libro' : perfil == 'videos' ? ' el video' : perfil == 'recursos' ? ' el recurso' : ' la planificación'}}?
                                
                            </div>

                            <div class="modal-footer d-flex justify-content-between">

                                <button type="button" class="botonGeneral botonEliminar" :disabled="creandoArticulo" @click="modalArticulo = false">CANCELAR</button>
                                
                                <button type="button" @click="confirmarArticulo" class="botonGeneral" v-if="!creandoArticulo">
                                    CONFIRMAR
                                </button>

                                <button 
                                    class="botonGeneral"
                                    v-if="creandoArticulo" 
                                >
                                    <div class="creandoArticulo">
                                        <div class="spinner-border" role="status">
                                            <span class="sr-only"></span>
                                        </div>
                                    </div>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>    
                <!-- MODAL CATEGORIAS -->
                          
                <!--START NOTIFICACION -->
                <div role="alert" id="mitoast" aria-live="assertive" @mouseover="ocultarToast" aria-atomic="true" class="toast">
                    <div class="toast-header">
                        <div class="row tituloToast" id="tituloToast">
                            <strong class="mr-auto">{{tituloToast}}</strong>
                        </div>
                    </div>
                    <div class="toast-content">
                        <div class="row textoToast">
                        <strong>{{textoToast}}</strong>
                        </div>
                    </div>
                </div>
                <!--END NOTIFICACION -->

            </div>
        </div>
        
    </div>

    <style scoped> 
        .selected {
            background: #7C4599;
            color: white;
        }
        .active{
            background: grey;
            color: white;
        } 
        .opcionNav{
            width: 25%;
            border-bottom: solid 1px grey;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 35px;
        }
        .opcionNav:hover{
            cursor: pointer;
        }
        .chip{
            width: auto;
            font-size: 12px;
            border: solid 1px grey;
            margin: 5px;
            border-radius: 5px;   
        }
        .chip:hover{
            cursor: pointer
        }
        .contenedorCategorias{
            width: 100%;
            margin: auto;
            font-size: 12px;
        }
        .verCategorias{
            width: 95%;
            font-size: 0.8em;
            margin-top: 10px;
        }
        .contenedorABM{
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
            border: solid 1px #7C4599;
            border-radius: 5px;
        }
        .titleABM{
            width: 100%;
            height: 40px;
            font-size: 1.2em;
            line-height: 40px;
            padding-left: 10px;
            border-bottom: solid 1px #7C4599;
            color: #7C4599;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footerABM{
            width: 100% !important;
            background-color: white;
            display: flex;
            margin: 10px auto;
            padding: 0 30px;
            justify-content: space-between
        }
        .previsualizacion{
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #imagenPrevisualizacion {
            width: 150px;
            height: 230px;
        }

        .btn{
            width: 140px;
            height: 34px;
            font-size: 12px
        }
        .botonVolver{
            height: 38px;
            font-size: 0.8em;
            color: rgb(238, 100, 100);
            border: solid 1px rgb(238, 100, 100);
        }
        .botonVolver:hover{
            color: white;
            background: rgb(238, 100, 100);
        }
        .botonAgregar{
            width: auto;
            color: rgb(124, 69, 153);;
            border: solid 1px rgb(124, 69, 153);;
        }
        .botonAgregar:hover{
            background-color: rgb(124, 69, 153);;
            color: white;
        }
        .textareaDescripcion{
            margin:0!important;
            width: 100%;
            font-size: 11px;
            height: 150px;
        }
        .error{
            background: rgb(238, 100, 100) !important;
        }
        .rowBotones{
            width: 100%;
            margin:auto;
        }
        #mitoast{
            z-index:60;
        }
        .inputCategoria{
            border: solid 1px rgb(124, 69, 153);;
            border-radius: 5px;
            height: 40px;
        }
        .inputCategoria:focus{
           outline: none;
        }
        .labelCategoria{
            padding-left: 0 !important;
            color: grey;
        }
        .rowCategoria{
            width:100%;
            margin: auto;
        }
        label{
            font-size: 12px;
        }
    </style>
    <script>
        var app = new Vue({
            el: "#app",
            components: {                
            },
            data: {
                scroll: false,
                tituloToast: null,
                textoToast: null,
                creandoCategorias: false,
                creandoArticulo: false,
                categorias: [],
                articulo: {
                    nombre: null,
                    imagen: null,
                    nombreImagen: null,
                    descripcion: null,
                    categoria: null
                },
                pdfbase64: null,
                errorCategoria: null,
                errorImagen: null,
                errorNombre: null,
                errorDescripcion: null,
                errorNuevaCategoria: false,
                nuevaCategoria: null,  
                confirmCategorias: false,
                modal: false,
                perfil : null,
                buscandoCategorias: false,
                modalArticulo: false
            },
            computed: {
                verArchivo() {
                    return this.pdfbase64;
                }
            },
            mounted () {
                this.perfil = localStorage.getItem("perfil")
                this.consultarCategorias();
            },
            methods:{
                cambiarPerfil(param) {
                    this.perfil = param;
                    this.resetErrores();
                    this.resetArticulo();
                    this.consultarCategorias();
                },
                resetArticulo() {
                    this.articulo.imagen = null;
                    this.categorias.forEach(element => {
                        element.checked = false;
                    });
                    this.articulo.nombre = null;
                    this.articulo.descripcion = null;
                    this.articulo.nombreImagen = "",
                    this.articulo.categoria = null;
                    this.articulo.archivo = null;
                    this.pdfbase64 = null;
                    this.archivo = null;
                    if (this.perfil != 'planificaciones' && this.perfil != 'videos') {
                        $imagenPrevisualizacion = document.querySelector("#imagenPrevisualizacion");
                        $imagenPrevisualizacion.src = "img/sinImagen.png";
                    }
                },
                resetErrores() {
                    this.errorImagen = null;
                    this.errorCategoria = null;
                    this.errorNombre = null;
                    this.errorDescripcion = null;
                },
                irA (destino) {
                    switch (destino) {
                        case "home":
                            window.location.href = 'home.php';    
                            break;
                        
                        case "biblioteca":
                            window.location.href = 'banco.php';    
                            break;

                        case "videos":
                            window.location.href = 'banco.php';    
                            break;

                        case "planificaciones":
                            window.location.href = 'banco.php';    
                            break;
                        
                        case "recursos":
                            window.location.href = 'banco.php';    
                            break;

                        case "envio":
                            window.location.href = 'envio.php';    
                            break;
                    
                        default:
                            break;
                    }
                },
                consultarCategorias () {
                    this.buscandoCategorias = true;
                    let formdata = new FormData();
                    if (this.perfil == "biblioteca") {
                        formdata.append("recurso", "libros");
                    } else if (this.perfil == "recursos") {
                        formdata.append("recurso", "recurso");
                    } else if (this.perfil == "videos") {
                        formdata.append("recurso", "videos");
                    } else {
                        formdata.append("recurso", "planificaciones");
                    }
                    axios.post("funciones/acciones.php?accion=getCategorias", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.categorias = response.data.categorias;
                            app.categorias.forEach(element => {
                                element.checked = false;
                            });
                        }
                        app.buscandoCategorias = false;
                    })
                },
                changeCategoria(index) {
                    this.categorias = this.categorias.map((categoria, i) => {
                        if (i === index) {
                        return { ...categoria, checked: !categoria.checked };
                        }
                        return categoria;
                    });
                },
                subirImagen () {
                    const $seleccionArchivos = document.querySelector("#seleccionArchivos"),
                    $imagenPrevisualizacion = document.querySelector("#imagenPrevisualizacion");
                    const archivos = $seleccionArchivos.files;
                    // Si no hay archivos salimos de la función y quitamos la imagen
                    if (!archivos || !archivos.length) {
                        $imagenPrevisualizacion.src = "img/sinImagen.png";
                        return;
                    }
                    // Ahora tomamos el primer archivo, el cual vamos a previsualizar
                    const primerArchivo = archivos[0];
                    // Lo convertimos a un objeto de tipo objectURL
                    const objectURL = URL.createObjectURL(primerArchivo);
                 
                    this.articulo.archivo = objectURL
                    // Y a la fuente de la imagen le ponemos el objectURL
                    $imagenPrevisualizacion.src = objectURL;
                },

                // START FUNCIONES CATEGORIA
                cancelarCategorias () {
                    this.selectAnadir = 0;
                    this.modal = false;
                    this.errorNuevaCategoria = false;
                    this.nuevaCategoria = null;
                },
                confirmarNuevaCategoria () {
                    this.errorNuevaCategoria = false;
                    if (this.nuevaCategoria == null || this.nuevaCategoria.trim() == ''){
                        this.errorNuevaCategoria = true;
                    } else {
                        this.confirmCategorias = true;
                    }
                },
                crearCategorias () {
                    app.creandoCategorias = true;
                    let formdata = new FormData();
                    formdata.append("categoria", app.nuevaCategoria);
                    if (this.perfil == "biblioteca") {
                        formdata.append("tipo", "libros");
                    } else if (this.perfil == "recursos") {
                        formdata.append("tipo", "recurso");
                    } else if (this.perfil == "videos") {
                        formdata.append("tipo", "videos");
                    } else {
                        formdata.append("tipo", "planificaciones");
                    }
          
                    axios.post("funciones/acciones.php?accion=postCategoria", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.modal = false;
                            app.confirmCategorias = false;
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.nuevaCategoria = null;
                            app.consultarCategorias();
                        }
                        app.creandoCategorias = false;
                    }).catch( error => {
                        app.creandoCategorias = false;
                        app.mostrarToast("Error", "No se pudo crear la categoria");
                    })
                },
                // END FUNCIONES CATEGORIA


                processFile(event) {
                    if (event != undefined) {
                        this.archivo = event;
                        let reader = new FileReader();
                        try {
                            reader.readAsDataURL(event.target.files[0]);
                            reader.onload = () => {
                                this.pdfbase64 = reader.result
                                .replace("data:", "")
                                .replace(/^.+,/, "");
                          
                            }
                        } catch (e) {
                            this.mostrarToast("Error", "El archivo no se cargó correctamente");
                            return false;
                        }
                    }
                    // this.articulo.nombreImagen = event.target.files[0].name
                    if (this.perfil != "planificaciones") {
                        this.subirImagen()
                    }
                },  
                updateNombreImagen(value) {
                    this.articulo.nombreImagen = value;
                },
                crearArticulo () {
                    let error = false;
                    this.resetErrores();
                    if (this.articulo.archivo == null) {
                        this.errorImagen = "Campo requerido";
                        error = true;
                    }
                    if (this.categorias.filter(element => element.checked).length == 0) {
                        this.errorCategoria = "Campo requerido";
                        error = true;
                    }
                    if (this.articulo.nombre == null || this.articulo.nombre.trim() == '') {
                        this.errorNombre = "Campo requerido";
                        error = true;
                    } 
                    if (this.articulo.descripcion == null || this.articulo.descripcion.trim() == '') {
                        this.errorDescripcion = "Campo requerido";
                        error = true;
                    } 
                    if (!error) {
                        this.modalArticulo = true;
                    }
                },
                confirmarArticulo () {
                    app.creandoArticulo = true;
                    let formdata = new FormData();
                    if (this.perfil == 'biblioteca') {
                        formdata.append("tipo", "libro");
                    }
                    if (this.perfil == 'recursos') {
                        formdata.append("tipo", "recurso");
                    }
                    if (this.perfil == 'planificaciones') {
                        formdata.append("tipo", "planificaciones");
                    }
                    if (this.perfil == 'videos') {
                        formdata.append("tipo", "videos");
                    }

                    let categorias = "-";
                    app.categorias.forEach(element => {
                        if (element.checked) {
                            categorias = categorias + element.id + "-"
                        }    
                    });
                    formdata.append("categoria", categorias);
                    formdata.append("archivo", app.articulo.archivo);
                    formdata.append("nombre", app.articulo.nombre);
                    formdata.append("descripcion", app.articulo.descripcion);
                    axios.post("funciones/acciones.php?accion=crearRecurso", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.categorias.forEach(element => {
                                element.checked = false;
                            });
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.modalArticulo = false;
                            app.resetArticulo();
                        }
                        app.creandoArticulo = false;
                    }).catch( error => {
                        app.creandoArticulo = false;
                        app.mostrarToast("Error", "No se pudo crear el articulo");
                    })
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
            },
            watch: {
                verArchivo () {
                    if (this.pdfbase64) {
                        this.articulo.archivo = this.pdfbase64;
                    }
                }
            }
        })
    </script>
</body>
</html>
<?php
session_start();
if (!$_SESSION["login"] ) {
    header("Location: index.html");
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
                    <span class="pointer mr-2" @click="irAHome()">Inicio</span>  -  <span class="ml-2 grey"> Biblioteca </span>
                </div>
            </div>
            <!-- END BREADCRUMB -->

            <div class="row mt-6">
                <div class="col-12">
                    <!-- START COMPONENTE LOADING BUSCANDO ARTICULOS -->
                    <div class="contenedorLoading" v-if="buscandoLibros">
                        <div class="loading">
                            <div class="spinner-border" role="status">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                    </div>
                    <!-- END COMPONENTE LOADING BUSCANDO ARTICULOS -->

                  
                    <!-- START TABLA LIBROS -->
                    <div class="row rowBotones d-flex justify-content-between">
                        <button type="button" class="btn boton" data-toggle="modal" data-target="#Modal">
                            Nuevo libro
                        </button>
                        <button type="button" class="btn boton" @click="modal=true" data-toggle="modal" data-target="#ModalCategoria">
                            Nueva categoria
                        </button>
                    </div>

                    <div class="contenedorTabla">
                        <div v-if="libros.length != 0">
                            <div class="card" style="width: 18rem;">
                                <img class="card-img-top" src="..." alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">Card title</h5>
                                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                    <a href="#" class="btn btn-primary">Go somewhere</a>
                                </div>
                            </div>
                        </div> 
                        <div v-else>
                            <span class="sinResultados">
                                NO SE ENCONTRÓ RESULTADOS PARA MOSTRAR
                            </span>
                        </div>
                    </div>       
                    <!-- END TABLA LIBROS -->
                </div>

                <div v-if="modal">
                    <div id="myModal" class="modal">
                        <div class="modal-content p-0">
                            <div class="modal-header  d-flex justify-content-center">
                                <h5 class="modal-title" id="ModalLabel">NUEVA(S) CATEGORIA(S)</h5>
                            </div>

                            <div class="modal-body row d-flex justify-content-center">
                                <div class="col-sm-12 mt-3">
                                    
                                    <div class="row rowCategoria d-flex justify-space-around" v-for="(categoria, index) in nuevasCategorias">
                                        <label for="nombre" :class="index != 0 ? 'mt-2 ' : ''" class="labelCategoria">Nombre categoria {{index + 1 }} (*)</label>
                                        <input class="inputCategoria" @input="errorNuevasCategorias = false" :class="index + 1 == nuevasCategorias.length ? 'col-10' : 'col-12'" autocomplete="off" maxlength="60" v-model="nuevasCategorias[index]">
                                        <svg xmlns="http://www.w3.org/2000/svg" @click="addCategoria(categoria)" width="40" height="40" fill="currentColor" v-if="index + 1 == nuevasCategorias.length" class="col-1 bi bi-plus-circle addCategoria" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" @click="removeCategoria" width="40" height="40" fill="currentColor" v-if="index + 1 == nuevasCategorias.length && index != 0" class="col-1 bi bi-dash-circle removeCategoria" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                            <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                                        </svg>
                                    </div>
                                    <span class="errorLabel" v-if="errorNuevasCategorias">Complete el campo para crear la/s categoria/s</span>
                                </div>
                            </div>
                            

                            <div class="modal-footer d-flex justify-content-between" v-if="!confirmCategorias">
                                <button type="button" class="btn boton" @click="cancelarCategorias" data-dismiss="modal">Cancelar</button>
                                
                                <button type="button" @click="confirmarNuevasCategorias" class="btn boton" v-if="!loading">
                                    Crear
                                </button>
                            </div>

                            <div class="modal-footer d-flex justify-content-between" v-if="confirmCategorias">
                                <div class="row rowBotones f-dlex justify-content-center my-3">
                                    ¿Confirma la creación de la/s categoria/s?
                                </div>

                                <button type="button" class="btn boton" :disabled="loading" @click="confirmCategorias = false">Cancelar</button>
                                
                                <button type="button" @click="crearCategorias" class="btn boton" v-if="!loading">
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
        .addCategoria{
            padding: 0;
            width: 35px;
            margin: auto;
            height: 35px;
            color: rgb(124, 69, 153);;
        }
        .removeCategoria{
            padding: 0;
            width: 35px;
            margin: auto;
            height: 35px;
            color: black;
        }
        .addCategoria:hover{
            cursor: pointer;
        }
        .rowCategoria{
            width:100%;
            margin: auto;
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
    </style>
    <script>
        var app = new Vue({
            el: "#app",
            components: {
                
            },
            data: {
                tituloToast: null,
                textoToast: null,
                buscandoLibros: false,
                libros: [],
                libro: {
                    nombre: null,
                    imagen: null,
                    descripion: null
                },
                errorCategoria: null,
                errorImagen: null,
                errorNombre: null,
                errorDescripcion: null,
                errorNuevasCategorias: false,
                nuevasCategorias:[""],
                confirmCategorias: false,
                modal: false,
                //
                loading: false,
                enviarCopia: false
            },
            mounted () {
                this.consultarLibros();
                // let envio = JSON.parse(localStorage.getItem("datosEnvio"));
                // if (envio) {
                //     this.envio.nombre = envio.nombre;
                //     this.envio.nombreVoluntario = envio.nombreVoluntario;
                //     this.envio.direccion = envio.direccion;
                //     this.envio.piso = envio.piso;
                //     this.envio.dpto = envio.dpto;
                //     this.envio.ciudad = envio.ciudad;
                //     this.envio.provincia = envio.provincia;
                //     this.envio.codigoPostal = envio.codigoPostal;
                //     this.envio.caracteristica = envio.caracteristica;
                //     this.envio.telefono = envio.telefono;
                //     this.recordarDatos = true;
                // }
            },
            methods:{
                consultarLibros() {
                    this.buscandoLibros = true;
                    axios.post("http://localhost/proyectos/pedidosSiPueden/funciones/acciones.php?accion=consultarLibros")
                    .then(function(response){
                        console.log(response.data);
                        app.buscandoLibros = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.libros = response.data.libros;
                        }
                    })
                },
                cancelarCategorias () {
                    this.nuevasCategorias = [""];
                },
                addCategoria (param) {
                    this.errorNuevasCategorias = false;
                    if (param.trim() == '') {
                        this.errorNuevasCategorias = true;
                        return;
                    }
                    this.nuevasCategorias.push("")
                },
                removeCategoria () {
                    this.errorNuevasCategorias = false;
                    this.nuevasCategorias.pop()
                },
                confirmarNuevasCategorias () {
                    this.errorNuevasCategorias = false;
                    if (this.nuevasCategorias.filter(element => element.trim() == '').length != 0) {
                        this.errorNuevasCategorias = true;
                    } else {
                        this.confirmCategorias = true;
                    }
                },
                crearCategorias () {
                    app.creandoCategorias = true;
                    let formdata = new FormData();
                    formdata.append("categorias", app.nuevasCategorias);
          
                    axios.post("http://localhost/proyectos/pedidosSiPueden/funciones/acciones.php?accion=crearCategorias", formdata)
                    .then(function(response){
                        console.log(response.data);
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.modal = false;
                            app.confirmCategorias = false;
                            app.mostrarToast("Éxito", response.data.mensaje);
                            // app.modal = false;
                            app.nuevasCategorias = [""];
                            // app.consultarArticulos();
                        }
                        app.creandoCategorias = false;
                    }).catch( error => {
                        app.creandoCategorias = false;
                        app.mostrarToast("Error", "No se pudo crear la/s categoria/s");
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
            }
        })
    </script>
</body>
</html>
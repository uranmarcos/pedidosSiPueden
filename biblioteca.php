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
                    <span class="pointer mr-2" @click="irAHome()">Inicio</span>  -  <span class="ml-2 grey"> Biblioteca </span>
                </div>
            </div>
            <!-- END BREADCRUMB -->
           

            <!-- START OPCIONES ADMIN -->
            <div class="row rowBotones d-flex justify-content-between mb-3" v-if="usuarioAdmin == 'admin'">  
                <button type="button" class="btn boton" @click="showABMLibro(modalLibros)" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg> Libro
                </button>

                <button type="button" class="btn boton" @click="modal=true" data-toggle="modal" data-target="#ModalCategoria">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg> Categoria
                </button> 
            </div>
            <!-- END OPCIONES ADMIN -->

            <!-- START ABM LIBROS -->
            <div class="contenedorABM" v-if="modalLibros">    
                <div class="titleABM">
                    NUEVO LIBRO
                </div>

                <div class="row rowBotones d-flex justify-content-center">
                    <div class="col-sm-12 col-md-3 mt-3">
                        <div class="previsualizacion">
                            <img id="imagenPrevisualizacion">   
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-5 mt-3">
                        <div class="pr-3">
                            <label for="nombre">Imagen (*) <span class="errorLabel" v-if="errorImagen">{{errorImagen}}</span></label>                 
                            <input 
                                class="form-control" 
                                type="file" 
                                accept="image/*"
                                capture
                                name="imagen"
                                ref="imagen"
                                @change="processFile($event)"
                                id="seleccionArchivos" 
                                value="libro.nombreImagen"
                            >
                        </div>
                        <div class="mt-2">
                            <label for="nombre">Categoria (*) <span class="errorLabel" v-if="errorCategoria">{{errorCategoria}}</span></label>
                            <select class="form-control" @change="changeCategoria(event.target.value)" name="categoria" id="categoria" v-model="libro.categoria">
                                <option v-for="categoria in categorias" v-bind:value="categoria.id" >{{categoria.nombre}}</option>
                                <option value="crearCategoria">Crear categoria</option>
                            </select>
                            <button type="button" class="btn botonSmall" @click="modal=true" data-toggle="modal" data-target="#ModalCategoria">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg> AGREGAR CATEGORIA
                            </button> 
                        </div>
                        <div class="mt-2">
                            <label for="nombre">Nombre (*) <span class="errorLabel" v-if="errorNombre">{{errorNombre}}</span></label>
                            <input class="form-control" autocomplete="off" maxlength="60" id="nombre" v-model="libro.nombre">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 mt-3">
                        <div>
                            <label for="nombre">Descripción (*) <span class="errorLabel" v-if="errorDescripcion">{{errorDescripcion}}</span></label>
                            <textarea class="form-control textareaDescripcion" maxlength="400" v-model="libro.descripcion"></textarea>
                        </div>
                    </div>
                </div>                   

                <div class="footerABM" v-if="!confirmLibro">
                    <button type="button" class="btn botonABM" @click="cancelarModalLibro">Cancelar</button>
                    <button type="button" @click="crearLibro" class="btn botonABM">
                        Crear
                    </button>
                </div>
                <div class="footerABM" v-if="confirmLibro">
                    <button type="button" class="btn boton" :disabled="loadingLibro" @click="confirmLibro = false">Cancelar</button>
                    <div class="row rowBotones f-dlex justify-content-center my-3">
                        ¿Confirma la creación del libro?
                    </div>
                    
                    <button type="button" @click="confirmarLibro" class="btn boton" v-if="!loadingLibro">
                        Confirmar
                    </button>
                    <button 
                        class="btn boton"
                        v-if="loadingLibro" 
                    >
                        <div class="loading">
                            <div class="spinner-border" role="status">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                    </button>
                </div>
                
            </div>
            <!-- END ABM LIBROS -->

            <div class="row rowBotones d-flex justify-content-between">     
                <select class="form-control selectCategoria" @change="buscarPorCategoria(event.target.value)" v-model="categoriaBusqueda">
                    <option value="0" >Todas las categorias</option>
                    <option v-for="categoria in categorias" v-bind:value="categoria.id" >{{categoria.nombre}}</option>
                </select>
            </div>
           

            <div class="row mt-6">
                <div class="col-12">
                    <!-- START COMPONENTE LOADING BUSCANDO LIBROS -->
                    <div class="contenedorLoading" v-if="buscandoLibros">
                        <div class="loading">
                            <div class="spinner-border" role="status">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                    </div>
                    <!-- END COMPONENTE LOADING BUSCANDO LIBROS -->

                  
                
                    <div v-if="libros.length != 0" class="row contenedorLibros d-flex justify-content-around">
                        <article class="col-12 col-lg-6" v-for="libro  in libros">
                            <div class="row rowCard">
                                <div class="col-6 col-md-4 p-0" :id="'libro' + libro.id" >
                                    <div class="imgCard">
                                        <img  :src="retornarImagen(libro)"/>
                                      
                                        <button type="button" class="btn botonSmallTrash" @click="eliminarLibro(libro.id, libro.nombre)" data-toggle="modal" data-target="#ModalCategoria" v-if="usuarioAdmin == 'admin'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                            </svg> ELIMINAR LIBRO
                                        </button>

                                        <button type="button" class="btn botonSmallAdd" @click="agregarLibroPedido(libro.id, libro.nombre)" v-if="usuarioAdmin != 'admin' && librosPedidos.filter(element =>element.id == libro.id).length == 0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">
                                                <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z"/>
                                                <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                            </svg> AGREGAR

                                        </button> 

                                        <button type="button" class="btn botonSmallTrash" @click="eliminarLibroPedido(libro.id, libro.nombre)" v-if="usuarioAdmin != 'admin' && librosPedidos.filter(element =>element.id == libro.id).length != 0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-dash" viewBox="0 0 16 16">
                                                <path d="M6.5 7a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"/>
                                                <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                            </svg> ELIMINAR
                                        </button> 

                                        <button type="button" class="btn botonSmallAgregado" readonly v-if="usuarioAdmin != 'admin' && librosPedidos.filter(element =>element.id == libro.id).length != 0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                                                <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                                                <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                            </svg> AGREGADO
                                        </button> 
                                      
                                    </div>
                                </div>
                                <div class="col-6 col-md-8 p-0" :id="'descripcion' + libro.id" >
                                    <div class="descripcionCard">
                                        <div class="tituloLibro">{{libro.nombre}} 
                                        <div class="descripcionLibro">
                                            {{libro.descripcion}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div> 
                    <div class="contenedorTabla" v-else>
                        <span class="sinResultados">
                            NO SE ENCONTRÓ RESULTADOS PARA MOSTRAR
                        </span>
                    </div>       
                    <!-- END TABLA LIBROS -->
                </div>

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
                                        <input class="inputCategoria" @input="errorNuevaCategoria = false"  v-model="nuevaCategoria">
                                    </div>
                                    <span class="errorLabel" v-if="errorNuevaCategoria">Complete el campo para crear la/s categoria/s</span>
                                </div>
                                <select class="form-control verCategorias">
                                    <option value="0" style="color: light-grey" >VER CATEGORIAS CREADAS</option>
                                    <option v-for="categoria in categorias">{{categoria.nombre}}</option>
                                </select>
                                
                            </div>


                            <div class="modal-footer d-flex justify-content-between" v-if="!confirmCategorias">
                                <button type="button" class="btn boton" @click="cancelarCategorias" data-dismiss="modal">Cancelar</button>
                                
                                <button type="button" @click="confirmarNuevaCategoria" class="btn boton" v-if="!loading">
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
                <!-- MODAL CATEGORIAS -->

                <!-- MODAL ELIMINAR LIBRO -->
                <div v-if="modalEliminar">
                    <div id="myModal" class="modal">
                        <div class="modal-content p-0">
                            <div class="modal-header  d-flex justify-content-center">
                                <h5 class="modal-title" id="ModalLabel">ELIMINAR LIBRO</h5>
                            </div>

                            <div class="modal-body row d-flex justify-content-center">
                                <div class="col-sm-12 mt-3 d-flex justify-content-center">
                                    ¿Desea eliminar el libro {{libroEliminable.nombre}} ?
                                </div>                                
                            </div>


                            <div class="modal-footer d-flex justify-content-between" v-if="!confirmCategorias">
                                <button type="button" class="btn boton" @click="cancelarEliminarLibro" >Cancelar</button>
                                
                                <button type="button" @click="confirmarEliminarLibro" class="btn boton" v-if="!eliminandoLibro">
                                    Confirmar
                                </button>

                                <button 
                                    class="btn boton"
                                    v-if="eliminandoLibro" 
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
                <!-- MODAL CATEGORIAS -->
                
                          
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
            <span class="ir-arriba" v-if="scroll" @click="irArriba">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
                </svg>
            </span>

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
        .verCategorias{
            width: 95%;
            margin-top: 10px;
        }
        /* ABM LIBROS */
        .contenedorABM{
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
            border: solid 1px #7C4599;
            border-radius: 5px;
        }
        .titleABM{
            width: 100%;
            color: white;
            height: 40px;
            font-size: 1.2em;
            line-height: 40px;
            padding-left: 10px;
            background-color: #7C4599;
        }
        .footerABM{
            width: 100%;
            background-color: white;
            display: flex;
            margin-top: 10px;
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
        .botonABM{
            color: #7C4599;
            border: none;
            margin: 1px;
            text-transform: uppercase;
            font-size: 1em;    
        }
        .botonABM:hover{
           font-weight: bolder;
           color: #7C4599
        }
        .botonSmall{
            font-size: 12px;
            color: #7C4599;
        }
        .botonSmall:hover{
            font-size: 13px;
            color: #7C4599;
        }
        .botonSmallAdd{
            font-size: 12px;
            color: #7C4599;
            padding: 0;
            margin: 5px 0;
        }
        .botonSmallAdd:hover{
            font-size: 13px;
            color: #7C4599;
        }
        .botonSmallTrash{
            font-size: 12px;
            color: red;
            padding: 0;
            margin: 5px 0;
        }
        .botonSmallTrash:hover{
            font-size: 13px;
            color: red;
        }
        .botonSmallAgregado{
            font-size: 13px;
            color: green;
            border: solid 1px green;
        }
        .botonSmallAgregado:hover{
            font-size: 13px;
            color: green;
            border: solid 1px green;
            cursor: auto
        }
        .textareaDescripcion{
            margin:0!important;
            width: 100%;
            font-size: 11px;
            height: 210px;
        }
        /* ABM LIBROS */



        /*  LIBROS */
        article{
            min-height:230px;
            margin: 10px 0px;
            padding: 0!important;
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
        .selectCategoria{
            max-width: 250px;
        }
        /* LIBROS */ 







        .rowBotones{
            width: 100%;
            margin:auto;
        }
     

        
        .imgCard{
            padding: 0;
            display:flex;
            min-height: 250px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .descripcionCard{
            padding: 0;
            display:flex;
            min-height: 250px;
            flex-direction: column;
            justify-content: start;
            align-items: start;
        }
        .imgCard img{
            width: 100%;
            max-width: 150px;
            max-height: 250px;
            height: 100%;
            margin: 0 !important;
        }
        .tituloLibro{
            font-size: 1em;
            margin-top:5px;
            text-transform: uppercase;
            padding-left: 5px;
        }
        .descripcionLibro{
            font-size: 0.9em;
            padding-left: 5px;
        }
        .colImagen{
            display: flex;
            justify-content: center;
            flex-direction: column;
        }
        .colImagen img{
            height: 100%;
            width: 100%;
        }
        .card-title{
            font-size: 1.3em;
            width: 100%;
            text-align: center;
            display: flex;
            margin-bottom: 0;
            color: purple;
        }
        .card-text{
            font-size: 1em;
            width: 100%;
            text-align: justify;
            margin-bottom: 0;
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
            width: 30px;
            margin: auto;
            height: 30px;
            color: rgb(124, 69, 153);;
        }
        .removeCategoria{
            padding: 0;
            width: 30px;
            margin: auto;
            height: 30px;
            color: red;
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
                scroll: false,
                showCategorias: false,
                tituloToast: null,
                textoToast: null,
                buscandoLibros: false,
                buscandoCategorias: false,
                libros: [],
                categorias: [],
                libro: {
                    nombre: null,
                    imagen: null,
                    nombreImagen: null,
                    descripcion: null,
                    categoria: null
                },
                errorCategoria: null,
                errorImagen: null,
                errorNombre: null,
                errorDescripcion: null,
                errorNuevaCategoria: false,
                nuevaCategoria: null,  
                confirmCategorias: false,
                modal: false,
                modalLibros: false,
                pdfbase64: null,
                archivo: null,
                libroEliminable: {
                    nombre: null,
                    id: null
                },
                eliminandoLibro: false,
                modalEliminar: false,
                //
                loading: false,
                loadingLibro: false,
                confirmLibro: false,
                enviarCopia: false,
                usuarioAdmin: false,
                categoriaBusqueda: "0",
                librosPedidos: []
            },
            computed: {
                verArchivo() {
                    return this.pdfbase64;
                }
            },
            mounted () {
                let librosPedidos = JSON.parse(localStorage.getItem("librosPedidos"));
                if (librosPedidos) {
                    this.librosPedidos= librosPedidos;
                }
                this.usuarioAdmin = "<?php echo $rol; ?>";
                this.consultarLibros();
                this.consultarCategorias();
            },
            beforeUpdate(){
                window.onscroll = function (){
                    // Obtenemos la posicion del scroll en pantall
                    var scroll = document.documentElement.scrollTop || document.body.scrollTop;

                    // Realizamos alguna accion cuando el scroll este entre la posicion 300 y 400
                    if(scroll > 300 && scroll < 400){
                        console.log("Pasaste la posicion 300 del scroll");
                    }
                }
            },
            methods:{
                agregarLibroPedido(id, nombre) {
                    let libro = new Object();
                    libro.id = id;
                    libro.nombre = nombre;
                    this.librosPedidos.push(libro);
                    localStorage.setItem("librosPedidos", JSON.stringify(this.librosPedidos))
                },
                eliminarLibroPedido(id, nombre) {
                    this.librosPedidos = this.librosPedidos.filter(element => element.id != id)
                    localStorage.setItem("librosPedidos", JSON.stringify(this.librosPedidos))
                },
                irAHome () {
                    window.location.href = 'home.php';    
                },
                irArriba () {
                    window.scrollTo(0, 0);   
                },
                eliminarLibro (id, nombre) {
                    this.libroEliminable.id = id;
                    this.libroEliminable.nombre = nombre;
                    this.modalEliminar = true;
                },
                cancelarEliminarLibro () {
                    this.libroEliminable.nombre = null;
                    this.libroEliminable.id = null;
                    this.modalEliminar = false;
                },
                confirmarEliminarLibro() {
                    this.eliminandoLibro = true;
                    let formdata = new FormData();
                    formdata.append("idLibro", app.libroEliminable.id);

                    axios.post("funciones/acciones.php?accion=eliminarLibro", formdata)
                    .then(function(response){    
                        app.eliminandoLibro = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.modalEliminar = false;
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.consultarLibros(); 
                        }
                    });
                },
                changeCategoria (param) {
                    if (param == "crearCategoria") {
                        this.modal = true
                    } else {
                        this.modal = false
                    }
                },
                verDescripcion (id) {
                    document.getElementById("libro"+id).classList.add("hide")
                    document.getElementById("descripcion"+id).classList.remove("hide")
                },
                ocultarDescripcion (id) {
                    document.getElementById("libro"+id).classList.remove("hide")
                    document.getElementById("descripcion"+id).classList.add("hide")
                },
                subirImagen () {
                    const $seleccionArchivos = document.querySelector("#seleccionArchivos"),
                    $imagenPrevisualizacion = document.querySelector("#imagenPrevisualizacion");
                    const archivos = $seleccionArchivos.files;
                    // Si no hay archivos salimos de la función y quitamos la imagen
                    if (!archivos || !archivos.length) {
                        $imagenPrevisualizacion.src = "";
                        return;
                    }
                    // Ahora tomamos el primer archivo, el cual vamos a previsualizar
                    const primerArchivo = archivos[0];
                    // Lo convertimos a un objeto de tipo objectURL
                    const objectURL = URL.createObjectURL(primerArchivo);
                 
                    this.libro.imagen = objectURL
                    // Y a la fuente de la imagen le ponemos el objectURL
                    $imagenPrevisualizacion.src = objectURL;
                },
                retornarImagen(param){
                    return param.imagen
                },
                consultarCategorias () {
                    this.buscandoCategorias = true;
                    axios.post("funciones/acciones.php?accion=consultarCategorias")
                    //axios.post("http://localhost/proyectos/pedidosSiPueden/funciones/acciones.php?accion=consultarCategorias")
                    .then(function(response){
                        app.buscandoCategorias = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.categorias = response.data.categorias;
                        }
                    })
                },
                buscarPorCategoria (param) {
                    this.buscandoLibros = true;
                    let formdata = new FormData();
                    formdata.append("idCategoria", app.categoriaBusqueda);

                    axios.post("funciones/acciones.php?accion=buscarPorCategoria", formdata)
                    // axios.post("http://localhost/proyectos/pedidosSiPueden/funciones/acciones.php?accion=buscarPorCategoria", formdata)
                    .then(function(response){    
                        app.buscandoLibros = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.libros = response.data.libros;
                            app.libros.forEach(element => {
                                if (element.imagen !== null) {
                                    const blob = app.dataURItoBlob(element.imagen)
                                    const url = URL.createObjectURL(blob)
                                    element.imagen = url
                                    // element.imagen = "img/"+ element.imagen
                                }
                            })
                        }
                    });
                },
                consultarLibros() {
                    this.buscandoLibros = true;
                    axios.post("funciones/acciones.php?accion=consultarLibros")
                    //axios.post("http://localhost/proyectos/pedidosSiPueden/funciones/acciones.php?accion=consultarLibros")
                    .then(function(response){    
                        app.buscandoLibros = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.libros = response.data.libros;
                            app.libros.forEach(element => {
                                if (element.imagen !== null) {
                                    const blob = app.dataURItoBlob(element.imagen)
                                    const url = URL.createObjectURL(blob)
                                    element.imagen = url
                                    // element.imagen = "img/"+ element.imagen
                                }
                            })
                        }
                    });
                },
                dataURItoBlob (dataURI) {
                    const byteString = window.atob(dataURI)
                    const arrayBuffer = new ArrayBuffer(byteString.length)
                    const int8Array = new Uint8Array(arrayBuffer)
                    for (let i = 0; i < byteString.length; i++) {
                        int8Array[i] = byteString.charCodeAt(i)
                    }
                    const blob = new Blob([int8Array], {type: 'application/jpg'})
                    return blob
                },

                // START FUNCIONES CATEGORIA
                cancelarCategorias () {
                    this.selectAnadir = 0;
                    this.modal = false;
                    this.errorNuevaCategoria = false;
                    this.nuevaCategoria = null;
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
          
                    axios.post("funciones/acciones.php?accion=crearCategoria", formdata)
                    // axios.post("http://localhost/proyectos/pedidosSiPueden/funciones/acciones.php?accion=crearCategoria", formdata)
                    .then(function(response){
                        console.log(response.data);
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

                // START MODAL LIBRO
                showABMLibro (param) {
                    this.modalLibros = !param
                    if (param) {
                        this.cancelarModalLibro()
                    }
                },
                cancelarModalLibro () {
                    this.modalLibros = false;
                    this.selectAnadir = 0;
                    this.resetErroresNuevoLibro();
                    this.resetNuevoLibro();
                },
                processFile(event) {
                    console.log(event.target.files[0])
                    if (event != undefined) {
                        this.archivo = event;
                        let reader = new FileReader();
                        try {
                            reader.readAsDataURL(event.target.files[0]);
                            reader.onload = () => {
                                this.pdfbase64 = reader.result
                                .replace("data:", "")
                                .replace(/^.+,/, "");
                                console.log("termino");
                            }
                        } catch (e) {
                            this.mostrarToast("Error", "El archivo no se cargó correctamente");
                            return false;
                        }
                    }
                    this.libro.nombreImagen = event.target.files[0].name
                    this.subirImagen()
                },  
                crearLibro () {
                    let error = false;
                    this.resetErroresNuevoLibro();
                    if (this.libro.imagen == null) {
                        this.errorImagen = "Campo requerido";
                        error = true;
                    }
                    if (this.libro.categoria == null || this.libro.categoria == "crearCategoria") {
                        this.errorCategoria = "Campo requerido";
                        error = true;
                    }
                    if (this.libro.nombre == null || this.libro.nombre.trim() == '') {
                        this.errorNombre = "Campo requerido";
                        error = true;
                    } 
                    if (this.libro.descripcion == null || this.libro.descripcion.trim() == '') {
                        this.errorDescripcion = "Campo requerido";
                        error = true;
                    } 
                    if (!error) {
                        this.confirmLibro = true;
                    }
                },
                confirmarLibro () {
                    app.creandoLibro = true;
                    let formdata = new FormData();
                    formdata.append("categoria", app.libro.categoria);
                    formdata.append("imagen", app.libro.imagen);
                    formdata.append("nombre", app.libro.nombre);
                    formdata.append("descripcion", app.libro.descripcion);
                    axios.post("funciones/acciones.php?accion=crearLibro", formdata)
                    // axios.post("http://localhost/proyectos/pedidosSiPueden/funciones/acciones.php?accion=crearLibro", formdata)
                    .then(function(response){
                        console.log(response.data);
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.modalLibros = false;
                            app.confirmLibro = false;
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.consultarLibros();
                            app.resetNuevoLibro();
                        }
                        app.creandoLibro = false;
                    }).catch( error => {
                        app.creandoLibro = false;
                        app.mostrarToast("Error", "No se pudo crear el libro");
                    })
                },
                resetErroresNuevoLibro() {
                    this.errorImagen = null;
                    this.errorCategoria = null;
                    this.errorNombre = null;
                    this.errorDescripcion = null;
                },
                resetNuevoLibro() {
                    this.libro.imagen = null;
                    this.libro.categoria = null;
                    this.libro.nombre = null;
                    this.libro.descripcion = null;
                },
                // END FUNCIONES LIBRO
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
            },
            watch: {
                verArchivo () {
                    if (this.pdfbase64) {
                        this.libro.imagen = this.pdfbase64;
                    }

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
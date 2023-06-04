<?php
session_start();
$rol = "usuario";
if (!$_SESSION["login"] ) {
    header("Location: index.html");
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
$_SESSION["pedido"] = "biblioteca";
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
                    <span class="pointer mx-2" @click="irA('home')">Inicio</span> 
                    -  
                    <span class="mx-2 grey"> {{perfil == 'biblioteca' ? 'Biblioteca' : perfil == 'recursos' ?  'Recursos' : 'Planificaciones'}} </span>
                </div>
            </div>
            <!-- END BREADCRUMB -->         

            <!-- ACCIONES -->
            <div class="row rowBotones d-flex justify-content-between">     
                <div class="col-12 col-sm-6 col-md-4 px-0 mt-2 mt-md-0">
                    <select class="form-control selectCategoria" @change="page= 1, getObjetos()" v-model="categoriaBusqueda">
                        <option value="0" >Todas las categorias</option>
                        <option v-for="categoria in categorias" v-bind:value="categoria.id" >{{categoria.nombre}}</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-4 px-0 mt-2 mt-md-0" v-if="perfil != 'planificaciones'">
                    <div class="row rowBuscador d-flex justify-content-end justify-content-md-center">
                        <input class="form-control buscador" @keypress="changeBuscador(event)" :disabled="busquedaActiva" placeholder="Buscar" autocomplete="off" maxlength="60" id="buscador" v-model="buscador">
                        <button type="button" @click="buscarObjeto" class="mx-2 botonGeneral botonBuscar" v-if="buscador.trim().length >= 3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>              
                        </button>

                        <button type="button" @click="borrarBusqueda" class="btnEliminarBusqueda" v-if="busquedaActiva">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                            </svg>           
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-4 px-0 d-flex justify-content-end mt-2 mt-md-0" v-if="rol == 'admin' || rol == 'superAdmin'">
                    <button type="button" class="botonGeneral" @click="irA('nuevo')" >
                        {{perfil == 'biblioteca' ? 'NUEVO LIBRO' : perfil == 'recursos' ? 'NUEVO RECURSO' : 'NUEVA PLANIFICACIÓN' }}
                    </button>
                </div>
                
            </div>
            <!-- ACCIONES -->
           

            <div class="row mt-6">
                <div class="col-12">
                    <!-- START COMPONENTE LOADING BUSCANDO OBJETOS -->
                    <div class="contenedorLoading" v-if="buscandoObjetos">
                        <div class="loading">
                            <div class="spinner-border" role="status">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                    </div>
                    <!-- END COMPONENTE LOADING BUSCANDO OBJETOS -->
                
                    <!-- START COMPONENTE OBJETOS DISPONIBLES / SIN RESULTADOS -->
                    <div v-else>
                        <!-- START COMPONENTE OBJETOS DISPONIBLES Y PAGINACION -->
                        <div v-if="objetos.length != 0">
                            <!-- START COMPONENTE OBJETOS DISPONIBLES -->
                            <div class="row contenedorObjetos d-flex justify-content-around">

                                <article class="col-12" v-for="(objeto, index) in objetos">
                                    <div class="row rowCard">
                                        <div class="col-12 col-sm-3 p-0" :id="'objeto' + objeto.id"  v-if="perfil != 'planificaciones'">
                                            <div class="imgCard">
                                                <img  :src="retornarImagen(objeto)"/>    
                                            </div>
                                        </div>
                                        <div 
                                            class="col-12  px-3 mt-2 mt-md-0" 
                                            :class="perfil != 'planificaciones' ? 'col-sm-6 col-md-7' : 'col-sm-9 col-md-10'" 
                                            :id="'descripcion' + objeto.id"
                                        >
                                            <div class="descripcionCard">
                                                <div class="tituloOjeto">
                                                    {{objeto.nombre}}
                                                </div> 
                                                <div class="descripcionObjeto">
                                                    {{objeto.descripcion}}
                                                </div>
                                            </div>
                                        </div>
                                        <div 
                                            class="col-12 col-sm-3 col-md-2 acciones"  
                                            :id="'objeto' + objeto.id"
                                        >
                                            <div
                                                v-if="rol=='admin' || rol=='superAdmin'"
                                                class="botonesAdmin"
                                            >
                                                <button 
                                                    type="button" 
                                                    class="boton botonEliminar" 
                                                    @click="eliminar(objeto.id, objeto.nombre)" 
                                                    data-toggle="modal" 
                                                    data-target="#ModalCategoria"  
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                    </svg> ELIMINAR
                                                </button>

                                                <button 
                                                    type="button" 
                                                    class="boton botonEditar" 
                                                    disabled
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                    </svg> EDITAR
                                                </button>
                                            </div>

                                            <!-- v-if="objetosPedidos.filter(element =>element.id == objeto.id).length == 0 && perfil != 'planificaciones'" -->
                                            <button 
                                                type="button" 
                                                class="boton botonAgregar" 
                                                @click="agregarACarrito(objeto, index)" 
                                                v-if="!objeto.agregado && perfil != 'planificaciones'"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">
                                                    <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z"/>
                                                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                                </svg> AGREGAR
                                            </button> 
            
                                            <!-- v-if="objetosPedidos.filter(element =>element.id == objeto.id).length != 0 && perfil != 'planificaciones'" -->
                                            <button 
                                                type="button" 
                                                class="boton botonEliminar" 
                                                @click="eliminarDeCarrito(objeto.id, objeto.nombre, index)" 
                                                v-if="objeto.agregado && perfil != 'planificaciones'"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-dash" viewBox="0 0 16 16">
                                                    <path d="M6.5 7a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"/>
                                                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                                </svg> QUITAR
                                            </button> 
                                           
                                            <button 
                                                type="button" 
                                                @click="mostrarCarrito" 
                                                :disabled="verCarrito" 
                                                class="boton botonVerCarrito" 
                                                v-if="objetosPedidos.length != 0 && perfil != 'planificaciones'"
                                            >
                                                VER CARRITO ({{objetosPedidos.length}})
                                            </button> 

                                            <button 
                                                type="button" 
                                                class="boton botonAgregar" 
                                                @click="verPlanificacion(objeto.id)" 
                                                data-toggle="modal" 
                                                data-target="#ModalCategoria"
                                                v-if="perfil == 'planificaciones'"    
                                            >
                                                VER
                                            </button>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <span v-for="(categoria, index) in objeto.categoria.split('-')">
                                                <span class="chip" v-if="index > 0 && index < objeto.categoria.split('-').length - 1">
                                                    {{categorias.find(element => element.id == categoria).nombre}}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            </div> 
                            <!-- END COMPONENTE OBJETOS DISPONIBLES -->

                            <!-- START PAGINACION -->
                            <div class="row contenedorObjetos d-flex justify-content-around">
                                <div class="row mt-3 mb-5 paginacion">
                                    <div class="col-1 col-sm-4">
                                        <button @click="prev" class="btnPaginacion pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-left-fill" viewBox="0 0 16 16">
                                                <path d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="col-9 col-sm-4 d-flex justify-content-center">
                                        {{page * 6 - 5}} a {{page * 6 > cantidadObjetos ? cantidadObjetos : page * 6}} de {{cantidadObjetos == 1 ? "1 resultado" : cantidadObjetos >= 2 ? cantidadObjetos + " resultados" : ""}}
                                    </div>
                                    <div class="col-1 col-sm-4 d-flex justify-content-end">
                                        <button  class="btnPaginacion pointer" @click="next">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill" viewBox="0 0 16 16">
                                                <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- END PAGINACION -->
                        </div>
                        <!-- END COMPONENTE OBJETOS DISPONIBLES Y PAGINACION -->

                        <!-- START COMPONENTE SIN RESULTADOS -->
                        <div v-else>
                            <div class="contenedorTabla" v-if="objetos.length == 0">
                                <span class="sinResultados">
                                    NO SE ENCONTRÓ RESULTADOS PARA MOSTRAR
                                </span>
                            </div>       
                        </div>    
                        <!-- END COMPONENTE SIN RESULTADOS -->
                    </div>
                    <!-- END COMPONENTE OBJETOS DISPONIBLES / SIN RESULTADOS -->
                </div>

                <!-- MODAL ELIMINAR OBJETO -->
                <div v-if="modalEliminar">
                    <div id="myModal" class="modal">
                        <div class="modal-content p-0">
                            <div class="modal-header  d-flex justify-content-center">
                                <h5 class="modal-title" id="ModalLabel">
                                    ELIMINAR {{perfil == 'biblioteca' ? 'LIBRO' : perfil == 'recursos' ? 'RECURSO' : 'PLANIFICACIÓN'}}
                                </h5>
                            </div>

                            <div class="modal-body row d-flex justify-content-center">
                                <div class="col-sm-12 mt-3 d-flex justify-content-center">
                                    ¿Desea eliminar {{perfil == 'biblioteca' ? 'el libro' : perfil == 'recursos' ? 'el recurso' : 'la planificación'}}
                                </div>
                                <div class="col-sm-12 mt-3 d-flex justify-content-center">
                                    <b> {{objetoEliminable.nombre}}</b> ?    
                                </div>                             
                            </div>


                            <div class="modal-footer d-flex justify-content-between">
                                <button type="button" class="btn botonEliminar" @click="cancelarEliminar" >CANCELAR</button>
                                
                                <button type="button" @click="confirmarEliminar" class="botonGeneral" v-if="!eliminandoObjeto">
                                    CONFIRMAR
                                </button>

                                <button 
                                    class="botonGeneral"
                                    v-if="eliminandoObjeto" 
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

                <!-- START MODAL CARRITO -->
                <div v-if="verCarrito">
                    <div id="myModal" class="modal" :aria-labelledby="objetosPedidos.length < 2 ? 'mySmallModalLabel' : 'myLargeModalLabel'">
                        <div class="modal-content p-0">
                            <section>
                                <div class="modal-header  d-flex justify-content-center">
                                    <h6 class="modal-title my-0" id="ModalLabel">{{perfil == 'biblioteca' ? 'LIBROS' : 'RECURSOS'}} SELECCIONADOS</h6>
                                </div>

                                <div class="modal-body row d-flex justify-content-center">
                                    <div class="row d-flex justify-content-between">
                                        <div class="col-12 col-sm-5 articuloCarrito" v-for="objeto in objetosPedidos">
                                            <div >
                                                <img  class="imgCarrito" :src="retornarImagen(objeto)"/>    
                                            </div>
                                            <div class="nombreCarrito"> {{objeto.nombre}}</div>
                                        </div>
                                    </div>
                                </div>   

                                <div class="modal-footer d-flex justify-content-between">
                                    <button type="button" class="btn botonEliminar botonResponsive" @click="verCarrito = false">CERRAR</button>
                                    
                                    <button type="button" @click="prepararPedido()" class="btn botonAgregar botonResponsive" v-if="!loading">
                                        CONTINUAR
                                    </button>
                                </div>

                            </section>

                        </div>    
                    </div>
                </div>
                <!-- END MODAL CARRITO -->

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
                <!-- NOTIFICACION -->
              
            </div>

            <span class="ir-arriba" v-if="scroll" @click="irArriba">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
                </svg>
            </span>

        </div>
        
    </div>

    <style scoped> 
        .botonesAdmin{
            border-bottom: solid 1px grey;
            padding-bottom: 5px;
            margin-bottom: 5px;
        } 
       .descripcionCard{
            padding: 0;
            display:flex;
            height: auto;
            flex-direction: column;
            justify-content: start;
            align-items: start;
        }
        .chip {
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            padding: 5px 10px;
            margin-right: 5px;
            margin-bottom: 5px;
            background-color: #e0e0e0;
            font-size: 11px;
            border-radius: 10px;
        }
        .chip:before {
            content: '✓';
        }       
        .articuloCarrito{
            border: solid 1px grey; 
            margin: 1px;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .imgCarrito{
            padding: auto;
            display:flex;
            margin: auto;
            width: 90% !important;
            height: 100px;
        }
        .nombreCarrito{
            text-align: center;
            width: 100%;
            font-size: 12px;
        }
        .botonGeneral{
            width: auto;
            height: 34px;
            padding: 0 10px;
            font-size: 0.8em;
            color: rgb(124, 69, 153);
            border: solid 1px rgb(124, 69, 153);
            border-radius: 5px;
            background: white;
        }
        .botonGeneral:hover{
            background-color: rgb(124, 69, 153);
            color: white;
        }
        .botonBuscar{
            width: auto;
        }
        .btnEliminarBusqueda{
            width: auto;
            height: 38px;
            padding: 0 10px;
            font-size: 0.8em;
            border-radius: 5px;
            background: white;
            color: rgb(238, 100, 100);
            border: solid 1px rgb(238, 100, 100);;
        }
        .btnEliminarBusqueda:hover{
            background-color: rgb(238, 100, 100);;
            color: white;
            border: solid 1px rgb(238, 100, 100);
            box-shadow: none;
        }
        .paginacion{
            color: grey;
            font-size: 14px;
        }
        .btnPaginacion{
            border: none;
            background: white;
            color: #7C4599;
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
        .boton{
            width: 100%;
            height: 30px;
            margin: 5px 0;
            font-size: 0.6em;
            border-radius: 0px;
        }
        .btn{
            width: auto;
            height: 34px;
            font-size: 12px
        }
        .botonVerCarrito{
            color: grey;
            border: solid 1px grey;
        }
        .botonVerCarrito:hover{
            background-color: grey;
            color: white;
        }
        .botonEditar{
            color: grey;
            border: solid 1px grey;
        }
        .botonEditar:hover{
           box-shadow: none;
        }
        .botonEliminar{
            color: rgb(238, 100, 100);
            border: solid 1px rgb(238, 100, 100);
        }
        .botonEliminar:hover{
            color: white;
            background: rgb(238, 100, 100);
        }
        .botonAgregar{
            color:  rgb(124, 69, 153);;
            border: solid 1px  rgb(124, 69, 153);;
        }
        .botonAgregar:hover{
            background-color:  rgb(124, 69, 153);;
            color: white;
        }       
        article{
            height: auto;
            margin: 10px 0px;   
            padding: 0 !important;        
            background: #F9EEFF;
        }
        .rowCard{
            border-radius: 5px;
            border: solid 1px grey;
            padding: 10px 0;
            height:100%;
            margin:auto;
            position:relative;
        }
        .contenedorObjetos{
            width: 100%;
            margin:10px auto;
        }
        .selectCategoria{
            max-width: 250px;
        }
        .rowBotones{
            width: 100%;
            margin:auto;
        }
        .imgCard{
            padding: 0 12px;
            display:flex;
            height: auto;
            justify-content: center;
            align-items: center;
        } 
        .imgCard img{
            width: 100%;
            height: auto;
            max-height: 250px;
            margin: 0 !important;
        }
        .tituloOjeto{
            font-size: 1em;
            margin-top:5px;
            text-transform: uppercase;
            color:rgb(124, 69, 153);
        }
        .descripcionObjeto{
            font-size: 0.9em;
        }
        .descripcionObjeto{
            font-size: 0.8em;
            padding-left: 5px;
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
        .buscador{
            width: 60% !important;
        }
        .rowBuscador{
            width: 100%;
            margin: auto;
        }
        label{
            font-size: 12px;
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
                page: 1,
                cantidadObjetos: 0,
                verCarrito: false,
                pdf: null,
                scroll: false,
                tituloToast: null,
                textoToast: null,
                buscandoObjetos: false,
                buscandoCategorias: false,
                objetos: [],
                categorias: [],
                modal: false,
                pdfbase64: null,
                archivo: null,
                objetoEliminable: {
                    nombre: null,
                    id: null
                },
                eliminandoObjeto: false,
                modalEliminar: false,
                loading: false,
                rol: false,
                categoriaBusqueda: "0",
                objetosPedidos: [],
                buscador: "",
                busquedaActiva: false,
                perfil: null
            },
            mounted () {
                this.consultarCantidad();
                let pedido = JSON.parse(localStorage.getItem("pedido"));
                this.perfil = localStorage.getItem("perfil")
                this.rol = "<?php echo $rol; ?>";
                this.cargarPagina();
                this.consultarCategorias();
            },
            beforeUpdate(){
                window.onscroll = function (){
                    // Obtenemos la posicion del scroll en pantall
                    var scroll = document.documentElement.scrollTop || document.body.scrollTop;
                }
            },
            methods:{
                async cargarPagina() {
                    try {
                        // Realizar la consulta a la base de datos
                        const resultado = await this.getObjetos();
                        // Llamar a la otra función una vez que la consulta se haya completado
                        this.cargarPedido();
                    } catch (error) {
                        app.mostrarToast("Error", "Hubo un error al recuperar la información. Actualice la página");
                    }
                },
                cargarPedido() {
                    let pedido = JSON.parse(localStorage.getItem("pedido"));
                    if (pedido) {
                        if (pedido.tipo == this.perfil) {
                            this.objetosPedidos = [];
                            pedido.articulos.forEach(element => {
                                this.objetosPedidos.push(this.objetos.find(e => e.id == element.id))
                            });
                        }
                    }
                },
                changeBuscador (param) {
                    if (param.key == "Enter" && this.buscador.length >= 3) {
                        this.buscarObjeto()
                    }
                },
                mostrarCarrito() {
                    this.verCarrito = true;
                },
                agregarACarrito(objeto, index) {
                    this.objetosPedidos.push(objeto);
                    this.objetos = this.objetos.map((objeto, i) => {
                        if (i === index) {
                            return { ...objeto, agregado: !objeto.agregado };
                        }
                            return objeto;
                    });
                },
                eliminarDeCarrito(id, nombre, index) {
                    this.objetosPedidos = this.objetosPedidos.filter(element => element.id != id)
                    this.objetos = this.objetos.map((objeto, i) => {
                        if (i === index) {
                            return { ...objeto, agregado: !objeto.agregado };
                        }
                            return objeto;
                    });
                },
                prepararPedido () {
                    let pedido = {
                        tipo: this.perfil,
                        articulos: this.objetosPedidos
                    }
                    localStorage.setItem("pedido", JSON.stringify(pedido))
                    window.location.href = 'envio.php';    
                },
                irA (destino) {
                    switch (destino) {
                        case "home":
                            window.location.href = 'home.php';    
                            break; 
                        case "nuevo":
                            window.location.href = 'nuevo.php';    
                            break;
                        default:
                            break;
                    }
                },
                irArriba () {
                    window.scrollTo(0, 0);   
                },
                eliminar (id, nombre) {
                    this.objetoEliminable.id = id;
                    this.objetoEliminable.nombre = nombre;
                    this.modalEliminar = true;
                },
                cancelarEliminar () {
                    this.objetoEliminable.nombre = null;
                    this.objetoEliminable.id = null;
                    this.modalEliminar = false;
                },
                confirmarEliminar() {
                    this.eliminandoObjeto = true;
                    let formdata = new FormData();
                    formdata.append("id", app.objetoEliminable.id);

                    axios.post("funciones/acciones.php?accion=eliminarObjeto", formdata)
                    .then(function(response){    
                        app.eliminandoObjeto = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.modalEliminar = false;
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.consultarCantidad();
                            app.page = 1;
                            app.getObjetos(); 
                        }
                    });
                },
                verPlanificacion(id) {
                    let formdata = new FormData();
                    formdata.append("idPlanificacion", id);
                
                    axios.post("funciones/acciones.php?accion=verPlanificacion", formdata)
                    .then(function(response){    
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            if (response.data.archivos != false) {
                                try {
                                    const blob = app.dataURItoBlobPlanificacion(response.data.archivos[0].archivo)
                                    const url = URL.createObjectURL(blob)
                                    window.open(url, '_blank');
                                } catch (error) {
                                    app.mostrarToast("Error", "No se pudo visualizar el archivo. Intente nuevamente");
                                }
                            }
                        }
                    }).catch( error => {
                        app.mostrarToast("Error", "No se pudo visualizar el archivo. Intente nuevamente");
                    });
                },
                changeCategoria (param) {
                    if (param == "crearCategoria") {
                        this.modal = true
                    } else {
                        this.modal = false
                    }
                },
                retornarImagen(param){
                    return param.archivo
                },
                consultarCategorias () {
                    this.buscandoCategorias = true;
                    let formdata = new FormData();
                    if (this.perfil == 'biblioteca') {
                        formdata.append("recurso", "libros");
                    }
                    if (this.perfil == 'recursos') {
                        formdata.append("recurso", "recurso");
                    }
                    if (this.perfil == 'planificaciones') {
                        formdata.append("recurso", "planificaciones");
                    }
                    axios.post("funciones/acciones.php?accion=getCategorias", formdata)
                    .then(function(response){
                        app.buscandoCategorias = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.categorias = response.data.categorias;
                        }
                    })
                },
                consultarCantidad () {
                    let categoria = this.categoriaBusqueda;
                    let formdata = new FormData();
                    formdata.append("categoria", this.categoriaBusqueda);
                    formdata.append("buscador", this.buscador);
                    if (this.perfil == 'biblioteca') {
                        formdata.append("tipo", "libro");
                    } 
                    if (this.perfil == 'recursos') {
                        formdata.append("tipo", "recurso");
                    }
                    if (this.perfil == 'planificaciones') {
                        formdata.append("tipo", "planificaciones");
                    }

                    axios.post("funciones/acciones.php?accion=contarObjetos", formdata)
                    .then(function(response){    
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            if (response.data.cantidad != false) {
                                app.cantidadObjetos = response.data.cantidad
                            } else {
                                app.cantidadObjetos = 0;
                            }
                        }
                    });
                },
                getObjetos() {
                    this.buscandoObjetos = true;
                    let formdata = new FormData();
                    formdata.append("idCategoria", this.categoriaBusqueda);
                    formdata.append("buscador", this.buscador);
                    
                    if (this.perfil == 'biblioteca') {
                        formdata.append("tipo", "libro");
                    } 
                    if (this.perfil == 'recursos') {
                        formdata.append("tipo", "recurso");
                    }
                    if (this.perfil == 'planificaciones') {
                        formdata.append("tipo", "planificaciones");
                    }

                    if (this.page == 1) {
                        formdata.append("inicio", 0);
                    } else {
                        formdata.append("inicio", ((app.page -1) * 6));
                    }
                    this.consultarCantidad()

                    return axios.post("funciones/acciones.php?accion=getObjetos", formdata)
                    .then(function(response){   
                        console.log(response.data); 
                        app.buscandoObjetos = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            if (response.data.archivos != false) {
                                app.objetos = response.data.archivos;
                                if (app.perfil != 'planificaciones') {
                                    app.objetos.forEach(element => {
                                        element.agregado = false;
                                        if (element.archivo !== null) {
                                            const blob = app.dataURItoBlob(element.archivo)
                                            const url = URL.createObjectURL(blob)
                                            element.archivo = url
                                        }
                                    })
                                }
                            } else {
                                app.objetos = []
                            }
                        }
                    });
                },

                /////
                buscarObjeto() {
                    this.page = 1;
                    this.busquedaActiva = true;
                    this.getObjetos();
                },
                borrarBusqueda () {
                    this.busquedaActiva = false;
                    this.buscador = "";
                    this.getObjetos();
                },
                prev() {
                    if(this.page > 1) {
                        this.page = this.page - 1;
                        this.getObjetos();
                    }
                },
                next() {
                    if (Math.ceil(this.cantidadObjetos/6) > this.page) {
                        this.page = this.page + 1;
                        this.getObjetos();
                    }
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
                dataURItoBlobPlanificacion (dataURI) {
                    const byteString = window.atob(dataURI)
                    const arrayBuffer = new ArrayBuffer(byteString.length)
                    const int8Array = new Uint8Array(arrayBuffer)
                    for (let i = 0; i < byteString.length; i++) {
                        int8Array[i] = byteString.charCodeAt(i)
                    }
                    const blob = new Blob([int8Array], {type: 'application/pdf'})
                    return blob
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
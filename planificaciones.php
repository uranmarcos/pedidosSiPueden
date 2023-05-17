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
                    <span class="pointer mr-2" @click="irAHome()">Inicio</span>  -  <span class="ml-2 grey"> Planificaciones </span>
                </div>
            </div>
            <!-- END BREADCRUMB -->
           

            <!-- START OPCIONES ADMIN -->
            <div class="row rowBotones d-flex justify-content-between mb-3" v-if="usuarioAdmin == 'admin'">  
                <button type="button" class="btn boton" @click="showABMPlanificacion(modalPlanificaciones)" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg> Planificación
                </button>

                <button type="button" class="btn boton" @click="modal=true" data-toggle="modal" data-target="#ModalCategoria">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg> Categoria
                </button> 
            </div>
            <!-- END OPCIONES ADMIN -->



            <!-- START ABM PLANIFICACIONES -->
            <div class="contenedorABM" v-if="modalPlanificaciones">    
                <div class="titleABM">
                    NUEVA PLANIFICACIÓN
                </div>

                <!-- accept="application/pdf,application/msword*"
                                @change="processFile($event)" -->
                <div class="row rowBotones d-flex justify-content-center">
                    <div class="col-sm-12 col-md-6 mt-3">
                        <div class="pr-3">
                            <label for="nombre">Archivo (*) <span class="errorLabel" v-if="errorDocumento">{{errorDocumento}}</span></label>                 
                            <input 
                                class="form-control" 
                                type="file" 
                                capture
                                name="archivo"
                                ref="archivo"
                                @change="processFile($event)"
                                accept=".pdf, .jpg"
                                id="seleccionArchivos" 
                                value="planificacion.nombreArchivo"
                            >
                        </div>
                        <div class="mt-2">
                            <label for="nombre">Categoria (*) 
                            <span class="errorLabel" v-if="errorCategoria">{{errorCategoria}}</span></label>
                            <button type="button" class="btn botonSmall" @click="modal=true" data-toggle="modal" data-target="#ModalCategoria">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg> AGREGAR CATEGORIA
                            </button> 
                            <div class="row my-3">
                                <div class="col-6 categoria" v-for="categoria in categorias">
                                    <input v-model="categoria.checked" type="checkbox" value="categoria.id">
                                    {{categoria.nombre}}
                                </div>
                            </div>                            
                        </div>
                        <div class="mt-2">
                            <label for="nombre">Nombre (*) <span class="errorLabel" v-if="errorNombre">{{errorNombre}}</span></label>
                            <input class="form-control" autocomplete="off" maxlength="60" id="nombre" v-model="planificacion.nombre">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 mt-3">
                        <div>
                            <label for="nombre">Descripción (*) <span class="errorLabel" v-if="errorDescripcion">{{errorDescripcion}}</span></label>
                            <textarea class="form-control textareaDescripcion" maxlength="400" v-model="planificacion.descripcion"></textarea>
                        </div>
                    </div>
                </div>                   

                <div class="footerABM" v-if="!confirmPlanificacion">
                    <button type="button" class="btn botonABM" @click="cancelarModalPlanificacion">Cancelar</button>
                    <button type="button" @click="crearPlanificacion" class="btn botonABM">
                        Crear
                    </button>
                </div>
                <div class="footerABM" v-if="confirmPlanificacion">
                    <button type="button" class="btn boton" :disabled="creandoPlanificacion" @click="confirmPlanificacion = false">Cancelar</button>
                    <div class="row rowBotones f-dlex justify-content-center my-3">
                        ¿Confirma la nueva planificación?
                    </div>
                    
                    <button type="button" @click="confirmarPlanificacion" class="btn boton" v-if="!creandoPlanificacion">
                        Confirmar
                    </button>
                    <button 
                        class="btn boton"
                        v-if="creandoPlanificacion" 
                    >
                        <div class="loading">
                            <div class="spinner-border" role="status">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                    </button>
                </div>
                
            </div>
            <!-- END ABM PLANIFICACIONES -->

            <div class="row rowBotones d-flex justify-content-between">     
                <select class="form-control selectCategoria" @change="page= 1, getPlanificaciones()" v-model="categoriaBusqueda">
                    <option value="0" >Todas las categorias</option>
                    <option v-for="categoria in categorias" v-bind:value="categoria.id" >{{categoria.nombre}}</option>
                </select>
            </div>
           

            <div class="row mt-6">
                <div class="col-12">
                    <!-- START COMPONENTE LOADING BUSCANDO PLANIFICACIONES -->
                    <div class="contenedorLoading" v-if="buscandoPlanificaciones">
                        <div class="loading">
                            <div class="spinner-border" role="status">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                    </div>
                    <!-- END COMPONENTE LOADING BUSCANDO PLANIFICACIONES -->

                    <div v-if="planificaciones.length != 0" class="row contenedorPlanficaciones d-flex justify-content-around">
                        <article class="col-12" v-for="planificacion  in planificaciones">
                            <div class="row rowCard">
                                <div class="col-12 col-sm-10 p-0" :id="'descripcion' + planificacion.id" >
                                    <div class="descripcionCard">
                                        <div class="tituloPlanificacion">
                                            {{planificacion.nombre}}
                                        </div> 
                                        <div class="descripcionPlanificacion">
                                            {{planificacion.descripcion}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2 acciones" :id="'planificacion' + planificacion.id" >
                                    <button type="button" class="btn botonSmallTrash" @click="eliminarPlanificacion(planificacion.id, planificacion.nombre)" data-toggle="modal" data-target="#ModalCategoria" v-if="usuarioAdmin == 'admin'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                        </svg>
                                    </button>

                                    <button type="button" class="btn botonSmallEye" @click="verPlanificacion(planificacion.id)" data-toggle="modal" data-target="#ModalCategoria">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </article>
                        <div class="row mt-3 mb-5 paginacion">
                            <div class="col-4">
                                <button @click="prev" class="btnPaginacion pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-left-fill" viewBox="0 0 16 16">
                                        <path d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="col-4 d-flex justify-content-center">
                            {{page * 5 - 4}} a {{page * 5 > cantidadRecursos ? cantidadRecursos : page * 5}} de {{cantidadRecursos == 1 ? "1 resultado" : cantidadRecursos >= 2 ? cantidadRecursos + " resultados" : ""}}
                            <!-- Página {{page}} de {{Math.ceil(cantidadRecursos/5)}} /    {{cantidadRecursos == 1 ? "1 resultado" : cantidadRecursos >= 2 ? cantidadRecursos + " resultados" : ""}} -->
                            </div>
                            <div class="col-4 d-flex justify-content-end">
                                <button  class="btnPaginacion pointer" @click="next">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill" viewBox="0 0 16 16">
                                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div> 
                    <div class="contenedorTabla" v-else>
                        
                        <span class="sinResultados">
                            NO SE ENCONTRÓ RESULTADOS PARA MOSTRAR
                        </span>
                    </div>       
                    <!-- END TABLA PLANIFICACIONES -->
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
                                    <span class="errorLabel" v-if="errorNuevaCategoria">Complete el campo para crear la categoria</span>
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
                                    ¿Confirma la creación de la categoria?
                                </div>

                                <button type="button" class="btn boton" :disabled="creandoCategoria" @click="confirmCategorias = false">Cancelar</button>
                                
                                <button type="button" @click="crearCategoria" class="btn boton" v-if="!creandoCategoria">
                                    Confirmar
                                </button>

                                <button 
                                    class="btn boton"
                                    v-if="creandoCategoria" 
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

                <!-- MODAL ELIMINAR PLANIFICACION -->
                <div v-if="modalEliminar">
                    <div id="myModal" class="modal">
                        <div class="modal-content p-0">
                            <div class="modal-header  d-flex justify-content-center">
                                <h5 class="modal-title" id="ModalLabel">ELIMINAR PLANIFICACIÓN</h5>
                            </div>

                            <div class="modal-body row d-flex justify-content-center">
                                <div class="col-sm-12 mt-3 d-flex justify-content-center">
                                    ¿Desea eliminar la planificacion: </br>
                                     <b> {{ planificacionEliminable.nombre}}</b> ?
                                </div>                                
                            </div>


                            <div class="modal-footer d-flex justify-content-between" v-if="!confirmCategorias">
                                <button type="button" class="btn boton" @click="cancelarEliminarPlanificacion" >Cancelar</button>
                                
                                <button type="button" @click="confirmarEliminarPlanificacion" class="btn boton" v-if="!eliminandoPlanificacion">
                                    Confirmar
                                </button>

                                <button 
                                    class="btn boton"
                                    v-if="eliminandoPlanificacion" 
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
        .paginacion{
            color: grey;
            font-size: 14px;
        }
        .btnPaginacion{
            border: none;
            background: white;
            color: #7C4599;
        }     
        .acciones{
            display: flex;
            align-items: end;
            flex-direction: column;
        }
        .categoria{
            font-size: 0.8em;
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
        .verCategorias{
            width: 95%;
            margin-top: 10px;
        }
        /* ABM PLANIFICACIONES */
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
        .botonSmallTrash{
            width: 40px;
            height: 30px;
            border: solid 1px rgb(238, 100, 100);
            font-size: 12px;
            color: rgb(238, 100, 100);
            padding: 0;
            margin: 5px 0;
        }
        .botonSmallTrash:hover{
            font-size: 13px;
            background-color: rgb(238, 100, 100);
            color: white;
        }
        .textareaDescripcion{
            margin:0!important;
            width: 100%;
            font-size: 11px;
            height: 210px;
        }
        /* ABM PLANIFICACIONES */



        /*  PLANIFICACIONES */
        article{
            min-height:30px;
            margin: 10px 0px;
            padding: 0!important;
        }
        .rowCard{
            border-radius: 5px;
            border: solid 1px grey;
            width: 100%;
            padding: 10px;
            height:100%;
            margin:auto;
        }
        .contenedorPlanficaciones{
            width: 100%;
            margin:10px auto;
        }
        .selectCategoria{
            max-width: 250px;
        }
        /* PLANIFICACIONES */ 







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
            min-height: 40px;
            flex-direction: column;
            justify-content: start;
            align-items: start;
        }
        .tituloPlanificacion{
            font-size: 1em;
            margin-top:5px;
            text-transform: uppercase;
            color:rgb(124, 69, 153);
        }
        .descripcionPlanificacion{
            font-size: 0.9em;
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
            border: solid 1px rgb(124, 69, 153);
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
        @media (max-width: 575px) {
            .acciones{
                display: flex;
                justify-content: center;
                flex-direction: row;
            }
            .botonSmallTrash{
                width: 60px;
                margin: 0 5px;
            }
            .botonSmallEye{
                width: 60px;
                margin: 0 5px;
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
                modalPlanificaciones: false,
                errorDocumento: null,
                confirmPlanificacion: false,
                buscandoPlanificaciones: false,
                planificaciones: [],
                creandoCategoria: false,
                creandoPlanificacion: false,
                planificacion: {
                    nombre: null,
                    archivo: null,
                    nombreArchivo: null,
                    descripcion: null,
                    categoria: null
                },
                //
                scroll: false,
                showCategorias: false,
                tituloToast: null,
                textoToast: null,
                buscandoCategorias: false,
                categorias: [],
                
                errorCategoria: null,
                errorNombre: null,
                errorDescripcion: null,
                errorNuevaCategoria: false,
                nuevaCategoria: null,  
                confirmCategorias: false,
                modal: false,
                pdfbase64: null,
                archivo: null,
                planificacionEliminable: {
                    nombre: null,
                    id: null
                },
                eliminandoPlanificacion: false,
                modalEliminar: false,
                //
                loading: false,
                enviarCopia: false,
                usuarioAdmin: false,
                categoriaBusqueda: "0"
            },
            mounted () {
                this.usuarioAdmin = "<?php echo $rol; ?>";
                this.consultarCantidad();
                this.getPlanificaciones();
                this.getCategorias();
            },
            beforeUpdate(){
                window.onscroll = function (){
                    // Obtenemos la posicion del scroll en pantall
                    var scroll = document.documentElement.scrollTop || document.body.scrollTop;
                }
            },
            methods:{
                consultarCantidad () {
                    let categoria = this.categoriaBusqueda;
                    let formdata = new FormData();
                    formdata.append("categoria", this.categoriaBusqueda);

                    axios.post("funciones/acciones.php?accion=contarPlanificaciones", formdata)
                    .then(function(response){    
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            if (response.data.cantidad != false) {
                                app.cantidadRecursos = response.data.cantidad
                            } else {
                                app.cantidadRecursos = 0;
                            }
                        }
                    });
                },
                prev() {
                    if(this.page > 1) {
                        this.page = this.page - 1;
                        this.getPlanificaciones();
                    }
                },
                next() {
                    if (Math.ceil(this.cantidadRecursos/5) > this.page) {
                        this.page = this.page + 1;
                        this.getPlanificaciones();
                    }
                },
                getPlanificaciones() {
                    this.buscandoPlanificaciones = true;
                    let formdata = new FormData();
                    formdata.append("recurso", "planificaciones");
                    formdata.append("idCategoria", this.categoriaBusqueda);
                    if (this.page == 1) {
                        formdata.append("inicio", 0);
                    } else {
                        formdata.append("inicio", ((app.page -1) * 5));
                    }
                    this.consultarCantidad()


                    formdata.append("recurso", "planificaciones");
                    axios.post("funciones/acciones.php?accion=getPlanificaciones", formdata)
                    .then(function(response){ 
                        console.log(response.data);   
                        app.buscandoPlanificaciones = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            if (response.data.archivos != false) {
                                app.planificaciones = response.data.archivos;
                            } else {
                                app.planificaciones = []
                            }
                        }
                    });
                },
                verPlanificacion(id) {
                    let formdata = new FormData();
                    formdata.append("idPlanificacion", id);
                
                    axios.post("funciones/acciones.php?accion=verPlanificacion", formdata)
                    .then(function(response){    
                        // app.buscandoPlanificaciones = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            if (response.data.archivos != false) {
                                try {
                                    const blob = app.dataURItoBlob(response.data.archivos[0].archivo)
                                    const url = URL.createObjectURL(blob)
                                    window.open(url, '_blank');
                                } catch (error) {
                                    app.creandoCategoria = false;
                                    app.mostrarToast("Error", "No se pudo visualizar el archivo. Intente nuevamente");
                                }
                            }
                        }
                    }).catch( error => {
                        app.creandoCategoria = false;
                        app.mostrarToast("Error", "No se pudo visualizar el archivo. Intente nuevamente");
                    });
                },
                getCategorias () {
                    this.buscandoCategorias = true;
                    let formdata = new FormData();
                    formdata.append("recurso", "planificaciones");
                    axios.post("funciones/acciones.php?accion=getCategorias", formdata)
                    .then(function(response){
                        app.buscandoCategorias = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            if (response.data.categorias) {
                                app.categorias = response.data.categorias;
                            }
                        }
                    })
                },
                //
                crearCategoria () {
                    app.creandoCategoria = true;
                    let formdata = new FormData();
                    formdata.append("categoria", app.nuevaCategoria);
                    formdata.append("tipo", "planificaciones");
          
                    axios.post("funciones/acciones.php?accion=postCategoria", formdata)
                    .then(function(response){
                        console.log(response.data);
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.modal = false;
                            app.confirmCategorias = false;
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.nuevaCategoria = null;
                            app.getCategorias();
                        }
                        app.creandoCategoria = false;
                    }).catch( error => {
                        app.creandoCategoria = false;
                        app.mostrarToast("Error", "No se pudo crear la categoria");
                    })
                },
                //
                irAHome () {
                    window.location.href = 'home.php';    
                },
                irArriba () {
                    window.scrollTo(0, 0);   
                },
                //
                //
                //
                //
                //
                //
                // FUNCIONES A REUTILIZAR
                eliminarPlanificacion (id, nombre) {
                    this.planificacionEliminable.id = id;
                    this.planificacionEliminable.nombre = nombre;
                    this.modalEliminar = true;
                },
                cancelarEliminarPlanificacion () {
                    this.planificacionEliminable.nombre = null;
                    this.planificacionEliminable.id = null;
                    this.modalEliminar = false;
                },
                confirmarEliminarPlanificacion() {
                    this.eliminandoPlanificacion = true;
                    let formdata = new FormData();
                    formdata.append("idPlanificacion", app.planificacionEliminable.id);

                    axios.post("funciones/acciones.php?accion=eliminarPlanificacion", formdata)
                    .then(function(response){    
                        app.eliminandoPlanificacion = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.modalEliminar = false;
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.getPlanificaciones(); 
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
                    const blob = new Blob([int8Array], {type: 'application/pdf'})
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
                
                // END FUNCIONES CATEGORIA

                // START MODAL PLANIFICACION
                showABMPlanificacion (param) {
                    this.modalPlanificaciones = !param
                    if (param) {
                        this.cancelarModalPlanificacion()
                    }
                },
                cancelarModalPlanificacion () {
                    this.modalPlanificaciones = false;
                    this.selectAnadir = 0;
                    this.resetErroresNuevaPlanificacion();
                    this.resetNuevaPlanificacion();
                },
                processFile(event) {
                    if (event.target.files[0].size > 999000) {
                        this.errorDocumento = "Archivo muy grande"
                        this.planificacion.archivo = null;
                        return;
                    }
                    if (event != undefined) {
                        this.archivo = event;
                        let reader = new FileReader();
                        try {
                            reader.readAsDataURL(event.target.files[0]);
                            reader.onload = () => {
                                this.planificacion.archivo = reader.result
                                .replace("data:", "")
                                .replace(/^.+,/, "");
                            }
                        } catch (e) {
                            this.mostrarToast("Error", "El archivo no se cargó correctamente");
                            return false;
                        }
                    }
                    this.planificacion.nombreArchivo = event.target.files[0].name
                },  
                crearPlanificacion () {
                    let error = false;
                    this.resetErroresNuevaPlanificacion();
                    if (this.planificacion.archivo == null) {
                        this.errorDocumento = "Campo requerido";
                        error = true;
                    }
                    if (this.categorias.filter(element => element.checked).length == 0) {
                        this.errorCategoria = "Campo requerido";
                        error = true;
                    }
                    if (this.planificacion.nombre == null || this.planificacion.nombre.trim() == '') {
                        this.errorNombre = "Campo requerido";
                        error = true;
                    } 
                    if (this.planificacion.descripcion == null || this.planificacion.descripcion.trim() == '') {
                        this.errorDescripcion = "Campo requerido";
                        error = true;
                    } 
                    if (!error) {
                        this.confirmPlanificacion = true;
                    }
                },
                confirmarPlanificacion () {
                    app.creandoPlanificacion = true;
                    let formdata = new FormData();
                    let categorias = "-";
                    app.categorias.forEach(element => {
                        if (element.checked) {
                            categorias = categorias + element.id + "-"
                        }    
                    });

                    let descripcion = app.planificacion.descripcion.replaceAll("'", '"')
                    formdata.append("tipo", "planificaciones");
                    formdata.append("categoria", categorias);
                    formdata.append("archivo", app.planificacion.archivo);
                    formdata.append("nombre", app.planificacion.nombre);
                    // formdata.append("descripcion", app.planificacion.descripcion);
                    formdata.append("descripcion", descripcion);

                    axios.post("funciones/acciones.php?accion=crearRecurso", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.modalPlanificaciones = false;
                            app.confirmPlanificacion = false;
                            app.categorias.forEach(element => {
                                element.checked = false;
                            });
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.getPlanificaciones();
                            app.resetNuevaPlanificacion();
                        }
                        app.creandoPlanificacion = false;
                    }).catch( error => {
                        app.creandoPlanificacion = false;
                        app.mostrarToast("Error", "No se pudo guardar la panificación");
                    })
                },
                resetErroresNuevaPlanificacion() {
                    this.errorDocumento = null;
                    this.errorCategoria = null;
                    this.errorNombre = null;
                    this.errorDescripcion = null;
                },
                resetNuevaPlanificacion() {
                    this.planificacion.archivo = null;
                    this.planificacion.categoria = null;
                    this.planificacion.nombre = null;
                    this.planificacion.descripcion = null;
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
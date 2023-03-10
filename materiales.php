<?php

if (!$GLOBALS["login"] ) {
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
  
 
</head>
<body>
    <div id="app">
        <?php require("shared/header.html")?>
        
        <div class="container">
            <div class="row mt-6">
                <div class="col-12">
                    <div class="card" v-if="!pedido">
                        <span class="subtituloCard">DATOS PARA EL ENVIO</span>
                        <div class="px-3 mt-3 row">
                            <div class="col-sm-12 col-md-6">
                                <label for="nombre">Nombre Sí Pueden (*) <span class="errorLabel" v-if="errorNombre">{{errorNombre}}</span></label>
                                <input class="form-control" maxlength="30" id="nombre" v-model="envio.nombre">
                            </div>
                            <div class="col-sm-12 col-md-6 mt-3 mt-md-0">
                                <label for="direccion">Dirección de envio (*)<span class="errorLabel" v-if="errorDireccion">{{errorDireccion}}</span></label>
                                <input class="form-control" maxlength="50" id="direccion" v-model="envio.direccion">
                            </div>
                            <div class="col-sm-12 col-md-6 mt-3">
                                <label for="ciudad">Ciudad (*) <span class="errorLabel" v-if="errorCiudad">{{errorCiudad}}</span></label>
                                <input class="form-control" maxlength="30" id="ciudad" v-model="envio.ciudad">
                            </div>
                            <div class="col-sm-12 col-md-6 mt-3">
                                <label for="provincia">Provincia (*) <span class="errorLabel" v-if="errorProvincia">{{errorProvincia}}</span></label>
                                <input class="form-control" maxlength="30" id="provincia" v-model="envio.provincia">
                            </div>
                            <div class="col-sm-12 col-md-6 mt-3">
                                <label for="ciudad">Código Postal (*) <span class="errorLabel" v-if="errorCodigoPostal">{{errorCodigoPostal}}</span></label>
                                <input class="form-control" maxlength="8" id="codigoPostal" v-model="envio.codigoPostal">
                            </div>
                            <div class="col-sm-12 col-md-6 mt-3">
                                <label for="provincia">Teléfono (*) <span class="errorLabel" v-if="errorTelefono">{{errorTelefono}}</span></label>
                                <div class="row">
                                    <div class="col-3">
                                        <input class="form-control" maxlength="4" id="telefono" v-model="envio.caracteristica">
                                    </div> 
                                    <div class="col-1">
                                        -
                                    </div> 
                                    <div class="col-8">
                                        <input class="col-sm-9 form-control" maxlength="9" id="telefono" v-model="envio.telefono">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-3 mt-3 row rowBotonesEnvio">
                            <div class="col-6 p-0">
                                <input type="checkbox" v-model="recordarDatos">
                                <label>Recordar</label>
                            </div>
                            <div class="col-6 pr-0 d-flex justify-content-end">
                                <button type="button" @click="continuar()" class="btn boton">
                                    Continuar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card" v-if="pedido">
                        <span class="subtituloCard">LISTADO DE MATERIALES</span>
                        <div class="row rowListado contenedorPrincipal" >             
                            <!-- START LISTADO CATEGORIAS NORMAL -->
                            <section class="col-12 col-md-3 px-0 mr-1 categorias">
                                <div class="row contenedorBoton" v-for="item in categorias" >
                                    <button :class="categoria == item.id ? 'btnCategoriaSeleccionado' : 'btnCategoria'" @click="changeCategoria(item)">
                                        <span :class="categoria == item.id ? 'textoBtnRemarcado' : ''">
                                            {{item.descripcion}}
                                        </span>
                                    </button>
                                </div>
                            </section>
                            <!-- END LISTADO CATEGORIAS NORMAL -->

                            <!-- START LISTADO CATEGORIAS RESPONSIVE -->
                            <section class="col-12 px-0 mr-1 categoriasResponsive">
                                <p>
                                    <button :class="categoria != null ? 'btnCategoriaSeleccionado' : 'btnCategoria'" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                        CATEGORIAS
                                    </button>
                                </p>
                                <div class="collapse" id="collapseExample"> 
                                    <div class="card card-body">
                                        <div class="row contenedorBoton" v-for="item in categorias" >
                                            <button :class="categoria == item.id ? 'btnCategoriaSeleccionado' : 'btnCategoria'" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" @click="changeCategoria(item)">{{item.descripcion}}</button>
                                        </div>                                        
                                    </div>
                                </div>
                            </section>
                            <!-- END LISTADO CATEGORIAS RESPONSIVE -->

                            <section class="col-12 col-md-5" v-if="categoria">
                                <article  v-if="categoria != null" class="pb-3">
                                    <h6> {{tituloCategoria}} </h6>
                                    <div v-for="articulo in listadoPedido">
                                        <div v-if="articulo.categoria == categoria">
                                            <div class="row rowArticulo" v-if="articulo.medible">
                                                <div class="col-10 divInputCantidad">
                                                    <input 
                                                        @input="changeCantidad(articulo.nombre, articulo.cantidad)"
                                                        type="number" 
                                                        class="inputCantidad" 
                                                        autocomplete="off" 
                                                        min="0" 
                                                        maxlength="4" 
                                                        max="9999" 
                                                        :placeholder="articulo.descripcion + '  '"
                                                        v-model="articulo.cantidad"
                                                    >
                                                    <span class="labelCantidad" v-if="articulo.cantidad != null"> {{ articulo.descripcion}}</span>
                                                </div>
                                                <button class="botonAccion botonDelete col-1" @click="articulo.cantidad = null">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eraser-fill" viewBox="0 0 16 16">
                                                        <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828l6.879-6.879zm.66 11.34L3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293l.16-.16z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div v-else>
                                                <div class="row rowArticulo" v-if="articulo.categoria != 'otros'">
                                                    <div class="col-10 divInputCantidad">
                                                        <label class="labelNoMedible">{{articulo.descripcion}}</label>
                                                    </div>
                                                    <div class="col-1 checkboxNoMedible">
                                                        <input type="checkbox" v-model="articulo.cantidad">
                                                    </div>
                                                </div>
                                                <div class="row rowArticulo" v-else>
                                                    <textarea class="otros" v-model="articulo.cantidad"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </section>

                            <section class="col-12 sectionPedido" :class="categoria ? 'col-md-3' : 'col-md-9'">
                                <h6>TU PEDIDO:</h6>
                                <div class="mb-3">
                                    <ul  v-for="articulo in listadoPedido">
                                        <li v-if="articulo.cantidad != null && articulo.cantidad != 0" class="itemListado">
                                            {{articulo.nombre}} {{articulo.medible || articulo.categoria == 'otros' ? ': ' + articulo.cantidad : ''}}
                                        </li>
                                    </ul>
                                </div>
                            </section>
                        </div>
                     
                        <div class="mt-3 row rowBotones p-3">
                            <button type="button" @click="pedido = false" class="btn boton">
                                Volver
                            </button>
                            <button type="button" :disabled="habilitarBtnEnviar()" class="btn boton" data-toggle="modal" data-target="#Modal">
                                Enviar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
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
                            

                            <div class="modal-footer d-flex justify-content-between" v-if="!errorEnvio && !pedidoEnviado">
                                <button type="button" class="btn boton" :disabled="loading" data-dismiss="modal">Cancelar</button>
                                
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
                    <!-- NOTIFICACION -->
                </div>
              
            </div>
        </div>
        
    </div>

    <style scoped>
        .categorias{
            display: none;
        }
        .categoriasResponsive{
            display: block;
        }
        @media (min-width: 768px) {
            .contenedorPrincipal{
                width: 100%;
                padding:0 5px;
                height: auto;
                margin: auto;
                display: flex;
                justify-content: space-between;
            }
            .categorias{
                display: block;
            }
            .categoriasResponsive{
                display: none;
            }
        }        
    </style>
    <script>
        var app = new Vue({
            el: "#app",
            components: {
                
            },
            data: {
                loading: false,
                recordarDatos: false,
                pedido: false,
                envio: {
                    nombre: null,
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
                listadoPedido: [
                    {medible: true, categoria: "afiches", nombre: "Afiche amarillo", descripcion: "Amarillo", cantidad: null},
                    {medible: true, categoria: "afiches", nombre: "Afiche azul", descripcion: "Azul", cantidad: null},
                    {medible: true, categoria: "afiches", nombre: "Afiche blanco", descripcion: "Blanco", cantidad: null},
                    {medible: true, categoria: "afiches", nombre: "Afiche celeste", descripcion: "Celeste", cantidad: null},
                    {medible: true, categoria: "afiches", nombre: "Afiche naranja", descripcion: "Naranja", cantidad: null},
                    {medible: true, categoria: "afiches", nombre: "Afiche rojo", descripcion: "Rojo", cantidad: null},
                    {medible: true, categoria: "afiches", nombre: "Afiche rosa", descripcion: "Rosa", cantidad: null},
                    {medible: true, categoria: "afiches", nombre: "Afiche verde", descripcion: "Verde", cantidad: null},
                    {medible: true, categoria: "afiches", nombre: "Afiche violeta", descripcion: "Violeta", cantidad: null},
                    {medible: true, categoria: "cartulinas", nombre: "Cartulina amarilla", descripcion: "Amarilla", cantidad: null},
                    {medible: true, categoria: "cartulinas", nombre: "Cartulina azul", descripcion: "Azul", cantidad: null},
                    {medible: true, categoria: "cartulinas", nombre: "Cartulina blanca", descripcion: "Blanca", cantidad: null},
                    {medible: true, categoria: "cartulinas", nombre: "Cartulina celeste", descripcion: "Celeste", cantidad: null},
                    {medible: true, categoria: "cartulinas", nombre: "Cartulina naranja", descripcion: "Naranja", cantidad: null},
                    {medible: true, categoria: "cartulinas", nombre: "Cartulina negra", descripcion: "Negra", cantidad: null},
                    {medible: true, categoria: "cartulinas", nombre: "Cartulina roja", descripcion: "Roja", cantidad: null},
                    {medible: true, categoria: "cartulinas", nombre: "Cartulina rosa", descripcion: "Rosa", cantidad: null},
                    {medible: true, categoria: "cartulinas", nombre: "Cartulina verde", descripcion: "Verde", cantidad: null},
                    {medible: true, categoria: "cartulinas", nombre: "Cartulina violeta", descripcion: "Violeta", cantidad: null},
                    {medible: true, categoria: "fibronesPizarra", nombre:"Fibrón para pizarra azul", descripcion: "Azul", cantidad: null},
                    {medible: true, categoria: "fibronesPizarra", nombre:"Fibrón para pizarra negro", descripcion: "Negro", cantidad: null},
                    {medible: true, categoria: "fibronesPizarra", nombre:"Fibrón para pizarra rojo", descripcion: "Rojo", cantidad: null},
                    {medible: true, categoria: "fibronesPizarra", nombre:"Fibrón para pizarra verde", descripcion: "Verde", cantidad: null},
                    {medible: true, categoria: "fibronesPermanentes", nombre:"Fibrón permanente azul", descripcion: "Azul", cantidad: null},
                    {medible: true, categoria: "fibronesPermanentes", nombre:"Fibrón permanente negro", descripcion: "Negro", cantidad: null},
                    {medible: true, categoria: "fibronesPermanentes", nombre:"Fibrón permanente rojo", descripcion: "Rojo", cantidad: null},
                    {medible: true, categoria: "fibronesPermanentes", nombre:"Fibrón permanente verde", descripcion: "Verde", cantidad: null}, 
                    {medible: true, categoria: "gomaEva", nombre: "Goma eva amarilla", descripcion: "Amarilla", cantidad: null},
                    {medible: true, categoria: "gomaEva", nombre: "Goma eva azul", descripcion: "Azul", cantidad: null},
                    {medible: true, categoria: "gomaEva", nombre: "Goma eva blanca", descripcion: "Blanca", cantidad: null},
                    {medible: true, categoria: "gomaEva", nombre: "Goma eva celeste", descripcion: "Celeste", cantidad: null},
                    {medible: true, categoria: "gomaEva", nombre: "Goma eva naranja", descripcion: "Naranja", cantidad: null},
                    {medible: true, categoria: "gomaEva", nombre: "Goma eva negra", descripcion: "Negra", cantidad: null},
                    {medible: true, categoria: "gomaEva", nombre: "Goma eva roja", descripcion: "Roja", cantidad: null},
                    {medible: true, categoria: "gomaEva", nombre: "Goma eva rosa", descripcion: "Rosa", cantidad: null},
                    {medible: true, categoria: "gomaEva", nombre: "Goma eva verde", descripcion: "Verde", cantidad: null},
                    {medible: true, categoria: "gomaEva", nombre: "Goma eva violeta", descripcion: "Violeta", cantidad: null},
                    {medible: true, categoria: "lapiceras", nombre:"Lapicera azul", descripcion: "Azul", cantidad: null},
                    {medible: true, categoria: "lapiceras", nombre:"Lapicera negra", descripcion: "Negra", cantidad: null},
                    {medible: true, categoria: "lapiceras", nombre:"Lapicera roja", descripcion: "Roja", cantidad: null},
                    {medible: true, categoria: "lapiceras", nombre:"Lapicera verde", descripcion: "Verde", cantidad: null},
                    {medible: true, categoria: "papelCrepe", nombre:"Papel crepe amarillo", descripcion: "Amarillo", cantidad: null},
                    {medible: true, categoria: "papelCrepe", nombre:"Papel crepe azul", descripcion: "Azul", cantidad: null},
                    {medible: true, categoria: "papelCrepe", nombre:"Papel crepe blanco", descripcion: "Blanco", cantidad: null},
                    {medible: true, categoria: "papelCrepe", nombre:"Papel crepe celeste", descripcion: "Celeste", cantidad: null},
                    {medible: true, categoria: "papelCrepe", nombre:"Papel crepe naranja", descripcion: "Naranja", cantidad: null},
                    {medible: true, categoria: "papelCrepe", nombre:"Papel crepe rojo", descripcion: "Rojo", cantidad: null},
                    {medible: true, categoria: "papelCrepe", nombre:"Papel crepe rosa", descripcion: "Rosa", cantidad: null},
                    {medible: true, categoria: "papelCrepe", nombre:"Papel crepe verde", descripcion: "Verde", cantidad: null},
                    {medible: true, categoria: "papelCrepe", nombre:"Papel crepe violeta", descripcion: "violeta", cantidad: null},
                    {medible: true, categoria: "temperas", nombre:"Tempera amarilla", descripcion: "Amarilla", cantidad: null},
                    {medible: true, categoria: "temperas", nombre:"Tempera azul", descripcion: "Azul", cantidad: null},
                    {medible: true, categoria: "temperas", nombre:"Tempera blanca", descripcion: "Blanca", cantidad: null},
                    {medible: true, categoria: "temperas", nombre:"Tempera celeste", descripcion: "Celeste", cantidad: null},
                    {medible: true, categoria: "temperas", nombre:"Tempera marron", descripcion: "Marrón", cantidad: null},
                    {medible: true, categoria: "temperas", nombre:"Tempera negra", descripcion: "Negra", cantidad: null},
                    {medible: true, categoria: "temperas", nombre:"Tempera roja", descripcion: "Roja", cantidad: null},
                    {medible: true, categoria: "temperas", nombre:"Tempera rosa", descripcion: "Rosa", cantidad: null},
                    {medible: true, categoria: "temperas", nombre:"Tempera verde", descripcion: "Verde", cantidad: null},
                    {medible: true, categoria: "temperas", nombre:"Tempera violeta", descripcion: "Violeta", cantidad: null},
                    {medible: false, categoria: "extras", nombre:"Botones", descripcion: "Botones", cantidad: null},
                    {medible: false, categoria: "extras", nombre:"Brillantina", descripcion: "Brillantina", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Caja x 6  Lápices  de colores", descripcion: "Caja x 6  Lápices  de colores", cantidad: null}, 
                    {medible: false, categoria: "extras", nombre:"Canutillos/mostacillas", descripcion: "Canutillos/mostacillas", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Cinta papel", descripcion: "Cinta papel", cantidad: null}, 
                    {medible: true, categoria: "extras", nombre:"Cinta embalar", descripcion: "Cinta embalar", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Cinta scotch", descripcion: "Cinta scotch", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Crayones de colores", descripcion: "Crayones de colores", cantidad: null},
                    {medible: false, categoria: "extras", nombre:"Elástico", descripcion: "Elástico", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Fibras de colores", descripcion: "Fibras de colores", cantidad: null},
                    {medible: false, categoria: "extras", nombre:"Globos", descripcion: "Globos", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Gomas de borrar", descripcion: "Gomas de borrar", cantidad: null},
                    {medible: false, categoria: "extras", nombre:"Hilo de algodón", descripcion: "Hilo de algodón", cantidad: null}, 
                    {medible: false, categoria: "extras", nombre:"Hilo Sisal", descripcion: "Hilo Sisal", cantidad: null},
                    {medible: false, categoria: "extras", nombre:"Lana", descripcion: "Lana", cantidad: null},
                    {medible: false, categoria: "extras", nombre:"Lentejuelas", descripcion: "Lentejuelas", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Palitos de helado", descripcion: "Palitos de helado", cantidad: null},
                    {medible: false, categoria: "extras", nombre:"Papel glasé", descripcion: "Papel glasé", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Pegamento Unipox", descripcion: "Pegamento Unipox", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Pinceles", descripcion: "Pinceles", cantidad: null}, 
                    {medible: true, categoria: "extras", nombre:"Pistolita silicona", descripcion: "Pistolita silicona", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Pistolita silicona repuesto", descripcion: "Pistolita silicona repuesto", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Plásticolas", descripcion: "Plásticolas", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Plasticolas de colores", descripcion: "Plasticolas de colores", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Plastilina", descripcion: "Plastilina", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Resmas A4", descripcion: "Resmas A4", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Sacapuntas", descripcion: "Sacapuntas", cantidad: null},
                    {medible: false, categoria: "extras", nombre:"Tanza", descripcion: "Tanza", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Tijeras adultos", descripcion: "Tijeras adultos", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Tijeras infantiles", descripcion: "Tijeras infantiles", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Tizas blancas", descripcion: "Tizas blancas", cantidad: null},
                    {medible: true, categoria: "extras", nombre:"Tizas de colores", descripcion: "Tizas de colores", cantidad: null},
                    {medible: false, categoria: "otros", nombre:"Otros", descripcion: "Otros", cantidad: null}
                ],
                categoria: null,
                tituloCategoria: null,
                categorias: [
                    {id: "afiches", descripcion: "AFICHES"}, 
                    {id: "cartulinas", descripcion: "CARTULINAS"},
                    {id: "fibronesPizarra", descripcion: "FIBRONES PARA PIZARRA"},
                    {id: "fibronesPermanentes", descripcion: "FIBRONES PERMANENTES"},
                    {id: "gomaEva", descripcion: "GOMA EVA"},
                    {id: "lapiceras", descripcion: "LAPICERAS"}, 
                    {id: "papelCrepe", descripcion: "PAPEL CREPE"},
                    {id: "temperas", descripcion: "TEMPERAS"},
                    {id: "extras", descripcion: "EXTRAS"},
                    {id: "otros", descripcion: "OTROS"}
                ],
                titulo: null,
                textoToast: null,
                mailCopia: null,
                enviarCopia: false,
                errorMail: false,
                errorEnvio: false,
                pedidoEnviado: false,
            },
            mounted () {
                let envio = JSON.parse(localStorage.getItem("datosEnvio"));
                this.envio.nombre = envio.nombre;
                this.envio.direccion = envio.direccion;
                this.envio.ciudad = envio.ciudad;
                this.envio.provincia = envio.provincia;
                this.envio.codigoPostal = envio.codigoPostal;
                this.envio.caracteristica = envio.caracteristica;
                this.envio.telefono = envio.telefono;
                this.recordarDatos = true;
            },
            methods:{
                habilitarBtnEnviar () {
                    return this.listadoPedido.find(element => element.cantidad != null) == undefined;
                },
                changeCategoria (param) {
                    this.categoria = param.id;
                    this.tituloCategoria = this.categorias.filter(element => element.id == param.id)[0].descripcion;
                },
                changeCantidad (nombre, cantidad) {
                    if (cantidad.length > 4) {
                        let cantidadModificada = cantidad.slice(0,4);
                        this.listadoPedido.filter(element => element.nombre == nombre)[0].cantidad = cantidadModificada;
                    }
                },
                continuar () {
                    this.pedido = true;
                    this.resetErrores();
                    if (this.envio.nombre != null && this.envio.nombre.trim() != '' &&
                        this.envio.direccion != null && this.envio.direccion.trim() != '' &&
                        this.envio.ciudad != null && this.envio.ciudad.trim() != '' &&
                        this.envio.provincia != null && this.envio.provincia.trim() != '' &&
                        this.envio.codigoPostal != null && this.envio.codigoPostal.trim() != '' &&
                        this.envio.caracteristica != null && this.envio.caracteristica.trim() != '' &&
                        this.envio.telefono != null && this.envio.telefono.trim() != '')
                        {
                            this.pedido = true;
                            if (this.recordarDatos) {
                                localStorage.setItem("datosEnvio", JSON.stringify(this.envio))
                            } else {
                                localStorage.removeItem("datosEnvio")
                            }
                        } else {
                            if (this.envio.nombre == null || this.envio.nombre.trim() == '') {
                                this.errorNombre = "Campo requerido";
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
                    let fecha = hoy.getDate() + "/" + (hoy.getMonth() + 1) + "/" + hoy.getYear();

                    formdata.append("nombreSiPueden", this.envio.nombre);
                    formdata.append("direccionEnvio", this.envio.direccion);
                    formdata.append("ciudad", this.envio.ciudad);
                    formdata.append("provincia", this.envio.provincia);
                    formdata.append("codigoPostal", this.envio.codigoPostal);
                    formdata.append("telefono", this.envio.caracteristica + " - " + this.envio.telefono );
                    formdata.append("fecha", fecha);
                    formdata.append("mail", "marcos_uran@hotmail.com");
                    formdata.append("mailCopia", this.mailCopia);
                    formdata.append("pedido", []);
                
                    axios.post("http://localhost/proyectos/pedidosSiPueden/funciones/acciones.php?accion=enviarPedido", formdata)
                        // axios.post("conexion/login.php?accion=login", formdata)
                        .then(function(response){
                            console.log(response.data);
                            if (response.data.error) {
                                app.errorEnvio= true;
                                app.pedidoEnviado= false;
                                // app.mostrarToast("Error", response.data.mensaje);
                            } else {
                                app.errorEnvio= false;
                                app.pedidoEnviado= true;
                                // app.mostrarToast("Éxito", response.data.mensaje);
                                // window.location.href = 'http://localhost/proyectos/pedidosSiPueden/home.php'; 
                                
                            }
                            app.loading = false;
                        }).catch( error => {
                            app.errorEnvio= true;
                            app.pedidoEnviado= false;
                            app.loading = false;
                            // app.mostrarToast("Error", "response.data.mensaje");
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
                }

            }
        })
    </script>
</body>
</html>
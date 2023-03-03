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
                    INICIO - SEDES
                </span>

                <button type="button" @click="mostrarABM=true, editable = false" class="btn boton" v-if="!mostrarABM">
                    Nueva sede
                </button>
            </div>
        
            <!-- START COMPONENTE ABM SEDES -->
            <div class="cardABM" v-if="mostrarABM">
                <h5 class="px-3 py-3 m-0">{{editable ? "Editar sede" : "Crear sede"}}</h5>
                <div class="px-3 row">
                    <div class="col-sm-12 col-md-4">
                        <label for="selectProvincia">Provincia</label>
                        <select class="form-control" name="selectProvincia" id="selectProvincia" v-model="provincia" @change="selectProvincia($event)" v-if="!editable">
                            <option v-for="provincia in provincias" v-on:value="{{provincia.nombre}}" >{{provincia.nombre.toUpperCase()}}</option>
                        </select>
                        <input class="form-control" v-model="provincia" disabled v-else>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="selectLocalidad">Localidad</label>
                        <select class="form-control" name="selectLocalidad" id="selectLocalidad" :disabled="localidades.length == 0" v-model="localidad"  @change="change('selectLocalidad')"  v-if="!editable">
                            <option v-for="localidad in localidades" v-on:value="{{localidad.nombre}}" >{{localidad.nombre}}</option>
                        </select>
                        <input class="form-control"  v-model="localidad" disabled v-else>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="selectCasas">Casas</label>
                        <select class="form-control" name="selectCasas" id="selectCasas" v-model="casas" @change="change('selectCasas')">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="my-3 d-flex justify-content-around">
                        <button type="button" @click="cancelarABM()" class="btn boton" >Cancelar</button>
                        <button type="button" @click="validar()" class="btn botonConfirm">Confirmar</button>
                    </div>
                </div>
            </div>
            <!-- END COMPONENTE ABM SEDES -->
            
            <!-- START COMPONENTE LOADING BUSCANDO SEDES -->
            <div class="contenedorLoading" v-if="buscandoSedes">
                <div class="loading">
                    <div class="spinner-border" role="status">
                        <span class="sr-only"></span>
                    </div>
                </div>
            </div>
            <!-- END COMPONENTE LOADING BUSCANDO SEDES -->

            <!-- START TABLA SEDES -->
            <div class="contenedorTabla" v-else>
                <table class="table table-hover">
                    <thead class="tituloColumna">
                        <th>
                            ID
                        </th>
                        <th>
                            Provincia
                        </th>
                        <th>
                            Localidad
                        </th>
                        <th>
                            Casas
                        </th>
                        <th style="width: 150px">
                            ACCION
                        </th>
                    </thead>
                    <tbody v-if="sedes.length != 0">
                        <tr v-for="sede in sedes">
                            <td>
                                {{sede.id}}
                            </td>
                            <td>
                                {{sede.provincia}}
                            </td>
                            <td>
                                {{sede.localidad}}
                            </td>
                            <td>
                                {{sede.casas}}
                            </td>
                            <td>
                                <button class="botonAccion botonEdit" @click="editar(sede)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                    </svg>
                                </button>
                                <button class="botonAccion botonDelete" @click="eliminar(sede)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eraser-fill" viewBox="0 0 16 16">
                                        <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828l6.879-6.879zm.66 11.34L3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293l.16-.16z"/>
                                    </svg>
                                </button>
                                <!-- <button class="botonAccion botonReset" @click="eliminarUsuario = true, elegir(usuario)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
                                        <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2zM2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                    </svg>
                                </button> -->
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="sedes.length == 0">
                    <span class="sinResultados">
                       NO SE ENCONTRÓ RESULTADOS PARA MOSTRAR
                    </span>
                </div>
            </div>
            <!-- END TABLA SEDES -->

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
                            <b class="d-flex justify-content-center">¿Desea {{accionModal}} la sede?</b>
                        </div>

                        <div class="row d-flex justify-content-center my-3">
                            Provincia: {{app.provincia}}
                            <br>
                            Localidad: {{app.localidad}}
                            <br>
                            Casas: {{app.casas}}
                        </div>
                        
                        <div class="row d-flex justify-content-around">
                            <div class="col-sm-12 col-md-6 d-flex justify-content-center">
                                <button type="button" @click="modal=false" class="btn boton" >Cancelar</button>
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

                                <!-- BOTONES CONFIRMACION ELIMINACION -->
                                <button type="button" @click="confirmarEliminacion()" class="btn botonConfirm" v-if="accionModal == 'eliminar' && !eliminando">Eliminar</button>
                                <button type="button" class="btn botonConfirm" v-if="accionModal == 'eliminar' && eliminando">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only"></span>
                                    </div>
                                </button>
                                <!-- BOTONES CONFIRMACION ELIMINACION -->
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
        .errorInput {
            /* width: 100%;
            text-align: center;
            margin: 10px auto !important;
            color: red;
            border: none; */
            border: solid 1px red;
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
                idSede:null,
                editable: false,
                buscandoSedes: false,
                provincia: null,
                localidad: null,
                casas: null,
                confirm: false,
                mostrarABM: false,
                eliminando: false,
                editando: false,
                creando: false,
                provincias: [
                    { id: 'Buenos Aires', nombre: 'Buenos Aires'},
                    { id: 'Catamarca', nombre: 'Catamarca'},
                    { id: 'Chaco', nombre: 'Chaco'},
                    { id: 'Chubut', nombre: 'Chubut'},
                    { id: 'Córdoba', nombre: 'Córdoba'},
                    { id: 'Corrientes', nombre: 'Corrientes'},
                    { id: 'Entre Rios', nombre: 'Entre Rios'},
                    { id: 'Formosa', nombre: 'Formosa'},
                    { id: 'Jujuy', nombre: 'Jujuy'},
                    { id: 'La Pampa', nombre: 'La Pampa'},
                    { id: 'La Rioja', nombre: 'La Rioja'},
                    { id: 'Mendoza', nombre: 'Mendoza'},
                    { id: 'Misiones', nombre: 'Misiones'},
                    { id: 'Neuquen', nombre: 'Neuquen'},
                    { id: 'Rio Negro', nombre: 'Rio Negro'},
                    { id: 'Salta', nombre: 'Salta'},
                    { id: 'San Juan', nombre: 'San Juan'},
                    { id: 'San Luis', nombre: 'San Luis'},
                    { id: 'Santa Cruz', nombre: 'Santa Cruz'},
                    { id: 'Santa Fe', nombre: 'Santa Fe'},
                    { id: 'Santiago del Estero', nombre: 'Santiago del Estero'},
                    { id: 'Tierra del Fuego', nombre: 'Tierra del Fuego'},
                    { id: 'Tucuman', nombre: 'Tucuman'}
                ],
                localidades: [],
                sedes: [],
                rol: null
            },
            mounted: function() {
                this.consultarSedes();
                this.rol = "<?php echo $_SESSION['rol'] ?>";
                if (this.rol == 'admin') {
                    document.getElementById("navSedes").classList.add("active");
                }
            },
            methods:{
                editar (sede) {
                    app.editable = true;
                    app.mostrarABM = true;
                    app.provincia = sede.provincia;
                    app.localidad = sede.localidad;
                    app.idSede = sede.id;
                    app.casas = sede.casas;
                },
                eliminar (sede) {
                    app.mostrarABM = false;
                    app.modal = true;
                    app.accionModal = "eliminar";
                    app.provincia = sede.provincia;
                    app.localidad = sede.localidad;
                    app.idSede = sede.id;
                    app.casas = sede.casas;
                },
                selectProvincia(event) {
                    document.getElementById("selectProvincia").classList.remove("errorInput");
                    let provincia = event.target.value.toLowerCase();
                    this.consultarLocalidades(provincia);
                },
                cancelarABM(){
                    app.mostrarABM = false;
                    app.provincia = null;
                    app.localidad = null;
                    app.casas = null;
                },
                change (id) {
                    document.getElementById(id).classList.remove("errorInput"); 
                },
                validar() {
                    if (!this.editable) {
                        document.getElementById("selectProvincia").classList.remove("errorInput");
                        document.getElementById("selectLocalidad").classList.remove("errorInput");
                        if (app.provincia == null) {
                            document.getElementById("selectProvincia").classList.add("errorInput");
                        } else {
                            if (app.localidad == null) {
                                document.getElementById("selectLocalidad").classList.add("errorInput");
                            }
                        }
                    }
                    document.getElementById("selectCasas").classList.remove("errorInput");
                    if (app.casas == null) {
                        document.getElementById("selectCasas").classList.add("errorInput");
                    }
                    if (app.provincia != null && app.localidad != null && app.casas != null) {
                        this.modal = true;
                        if (this.editable) {
                            this.accionModal = "modificar";
                        } else {
                            this.accionModal = "crear";
                        }
                    }
                },
                confirmarCreacion () {
                    app.creando = true;
                    let formdata = new FormData();
                    formdata.append("provincia", app.provincia);
                    formdata.append("localidad", app.localidad);
                    formdata.append("casas", app.casas);
                    axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=insertarSede", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            if (response.data.mensaje == "La sede ya existe") {
                                app.modal = false;    
                            }
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.modal = false;
                            app.mostrarABM = false;
                            app.provincia = null;
                            app.localidad = null;
                            app.casas = null;
                            app.consultarSedes();
                        }
                        app.creando = false;
                    }).catch( error => {
                        app.creando = false;
                        app.mostrarToast("Error", response.data.mensaje);
                    })
                },
                confirmarEdicion () {
                    app.editando = true;
                    let formdata = new FormData();
                    formdata.append("id", app.idSede);
                    formdata.append("casas", app.casas);
                    axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=editarSede", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.modal = false;
                            app.mostrarABM = false;
                            app.idSede= null;
                            app.provincia = null;
                            app.localidad = null;
                            app.casas = null;
                            app.consultarSedes();
                        }
                        app.editando = false;
                    }).catch( error => {
                        app.editando = false;
                        app.mostrarToast("Error", response.data.mensaje);
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
                confirmarEliminacion () {
                    app.eliminando = true;
                    let formdata = new FormData();
                    formdata.append("id", app.idSede);
                    axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=eliminarSede", formdata)
                    // .then(function(response){
                    //     app.modal = false;
                    //     app.eliminando = false;
                    //     app.idSede= null;
                    //     app.provincia = null;
                    //     app.localidad = null;
                    //     app.casas = null;
                    //     app.consultarSedes();
                    // })
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.modal = false;
                            app.idSede= null;
                            app.provincia = null;
                            app.localidad = null;
                            app.casas = null;
                            app.consultarSedes();
                        }
                        app.eliminando = false;
                    }).catch( error => {
                        app.eliminando = false;
                        app.mostrarToast("Error", response.data.mensaje);
                    })
                },
                consultarSedes() {
                    this.buscandoSedes = true;
                    axios.get("http://localhost/proyectos/pedidos2/conexion/api.php?accion=consultarSedes")
                    .then(function(response){
                        app.buscandoSedes = false;
                        // app.sedes = response.data.sedes;

                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.sedes = response.data.sedes;
                        }
                    })
                    
                },
                consultarLocalidades(provincia) {
                    axios.get(`https://apis.datos.gob.ar/georef/api/localidades?provincia=${provincia}&campos=nombre`)
                    .then(function(response){
                        app.localidades = response.data.localidades;
                    })
                },
                // consultar() {
                //     axios.get("http://localhost/proyectos/pedidos2/conexion/api.php?accion=mostrar")
                //     .then(function(response){
                //         console.log(response.data);
                //     })
                // }
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
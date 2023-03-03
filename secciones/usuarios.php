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
                    INICIO - USUARIOS
                </span>

                <button type="button" @click="mostrarABM=true, editable = false" class="btn boton" v-if="!mostrarABM">
                    Nuevo Usuario
                </button>
            </div>
        
            <!-- START COMPONENTE ABM usuarios -->
            <div class="cardABM" v-if="mostrarABM">
                <h5 class="px-3 py-3 m-0">{{editable ? "Editar usuario" : "Crear usuario"}}</h5>
                <div class="px-3 row">
                    <div class="col-sm-12 col-md-4">
                        <label for="primerNombre">Primer Nombre <span class="errorLabel" v-if="errorPrimerNombre">{{errorPrimerNombre}}</span></label>
                        <input class="form-control" maxlength="30" @keyup="validarCampo('primerNombre')" id="primerNombre" v-model="primerNombre">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="segundoNombre">Segundo Nombre <span class="errorLabel" v-if="errorSegundoNombre">{{errorSegundoNombre}}</span></label>
                        <input class="form-control" maxlength="30" @keyup="validarCampo('segundoNombre')" id="segundoNombre" v-model="segundoNombre">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="primerApellido">Primer Apellido <span class="errorLabel" v-if="errorPrimerApellido">{{errorPrimerApellido}}</span></label>
                        <input class="form-control" maxlength="30" @keyup="validarCampo('primerApellido')" id="primerApellido" v-model="primerApellido">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="segundoApellido">Segundo Apellido <span class="errorLabel" v-if="errorSegundoApellido">{{errorSegundoApellido}}</span></label>
                        <input class="form-control" maxlength="30" @keyup="validarCampo('segundoApellido')" id="segundoApellido" v-model="segundoApellido">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="dni">DNI <span class="errorLabel" v-if="errorDni">{{errorDni}}</span></label>
                        <input class="form-control" maxlength="8" @keyup="validarCampo('dni')" id="dni" v-model="dni">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="sede">Sede <span class="errorLabel" v-if="errorSede">{{errorSede}}</span></label>
                        <select class="form-control" name="selectSede" @change="validarCampo('sede')" id="selectSede" v-model="sede">
                            <option v-for="sede in sedes" v-bind:value="sede.id" >{{sede.provincia}} - {{sede.localidad}}</option>
                        </select>
                    </div>
                    <div class="col-sm-12">
                        <label for="mail">Mail <span class="errorLabel" v-if="errorMail">{{errorMail}}</span></label>
                        <input class="form-control" @keyup="validarCampo('mail')" id="mail" v-model="mail">
                    </div>
                    <div class="my-3 d-flex justify-content-around">
                        <button type="button" @click="cancelarABM()" class="btn boton" >Cancelar</button>
                        <button type="button" @click="validarForm()" class="btn botonConfirm">Confirmar</button>
                    </div>
                </div>
            </div>
            <!-- END COMPONENTE ABM USUARIOS -->
            
            <!-- START COMPONENTE LOADING BUSCANDO USUARIOS -->
            <div class="contenedorLoading" v-if="buscandoUsuarios">
                <div class="loading">
                    <div class="spinner-border" role="status">
                        <span class="sr-only"></span>
                    </div>
                </div>
            </div>
            <!-- END COMPONENTE LOADING BUSCANDO USUARIOS -->

            <!-- START TABLA USUARIOS -->
            <div class="contenedorTabla" v-else>
                <table class="table table-hover">
                    <thead class="tituloColumna">
                        <th>
                            ID
                        </th>
                        <th>
                            Nombre
                        </th>
                        <th>
                            Apellido
                        </th>
                        <th>
                            DNI
                        </th>
                        <th>
                            mail
                        </th>
                        <th>
                            Sede
                        </th>
                        <th style="width: 150px">
                            ACCION
                        </th>
                    </thead>
                    <tbody v-if="usuarios.length != 0">
                        <tr v-for="usuario in usuarios">
                            <td>
                                {{usuario.id}}
                            </td>
                            <td>
                                {{usuario.nombre}} {{usuario.segundoNombre}}
                            </td>
                            <td>
                                {{usuario.apellido}} {{usuario.segundoApellido}}
                            </td>
                            <td>
                                {{usuario.dni}}
                            </td>
                            <td>
                                {{usuario.mail}}
                            </td>
                            <td>
                                <span v-if="sedes.length == 0"> - </span>
                                <span v-else>
                                    {{sedes.filter(element => element.id == usuario.sede)[0]["provincia"]}} - {{sedes.filter(element => element.id == usuario.sede)[0]["localidad"]}}
                                </span>
                            </td>
                            <td>
                                <button class="botonAccion botonEdit" @click="editar(usuario)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                    </svg>
                                </button>
                                <button class="botonAccion botonDelete" @click="eliminar(usuario)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eraser-fill" viewBox="0 0 16 16">
                                        <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828l6.879-6.879zm.66 11.34L3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293l.16-.16z"/>
                                    </svg>
                                </button>
                                <button class="botonAccion botonReset" @click="reset(usuario)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
                                        <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2zM2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="usuarios.length == 0">
                    <span class="sinResultados">
                       NO SE ENCONTRÓ RESULTADOS PARA MOSTRAR
                    </span>
                </div>
            </div>
            <!-- END TABLA USUARIOS -->

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
                            <b class="d-flex justify-content-center">¿Desea {{accionModal}} el usuario?</b>
                        </div>

                        <div class="row d-flex justify-content-center my-3">
                            Nombre: {{app.primerNombre}} {{app.segundoNombre}}
                            <br>
                            Apellido: {{app.primerApellido}} {{app.segundoApellido}}
                            <br>
                            <div v-if="accionModal != 'resetear la contraseña'">
                                Dni: {{app.dni}}
                                <br>
                                Sede: {{sedes.filter(element => element.id == app.sede)[0]["provincia"]}} - {{sedes.filter(element => element.id == app.sede)[0]["localidad"]}}
                                <br>
                                Mail: {{app.mail}}
                            </div>
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

                                <!-- BOTONES CONFIRMACION ELIMINACION -->
                                <button type="button" @click="confirmarEliminacion()" class="btn botonConfirm" v-if="accionModal == 'eliminar' && !eliminando">Eliminar</button>
                                <button type="button" class="btn botonConfirm" v-if="accionModal == 'eliminar' && eliminando">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only"></span>
                                    </div>
                                </button>
                                <!-- BOTONES CONFIRMACION ELIMINACION -->

                                <!-- BOTONES CONFIRMACION ELIMINACION -->
                                <button type="button" @click="confirmarReseteo()" class="btn botonConfirm" v-if="accionModal == 'resetear la contraseña' && !reseteando">Eliminar</button>
                                <button type="button" class="btn botonConfirm" v-if="accionModal == 'resetear la contraseña' && reseteando">
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
                errorPrimerNombre: "",
                errorSegundoNombre: "",
                errorPrimerApellido: "",
                errorSegundoApellido: "",
                errorDni: "",
                errorSede: "",
                errorMail: "",
                tituloToast: "",
                textoToast: "",
                modal: false,
                idUsuario:null,
                editable: false,
                buscandoUsuarios: false,
                primerNombre: null,
                segundoNombre: null,
                primerApellido: null,
                segundoApellido: null,
                dni: null,
                sede: null,
                mail: null,
                mostrarABM: false,
                //
                confirm: false,
                eliminando: false,
                reseteando: false,
                editando: false,
                creando: false,
                usuarios: [],
                sedes: [],
                rol: null
            },
            mounted: function() {
                this.consultarUsuarios();
                this.consultarSedes();
                this.rol = "<?php echo $_SESSION['rol'] ?>";
                if (this.rol == 'admin') {
                    document.getElementById("navUsuarios").classList.add("active");
                }
            },
            methods:{
                editar (usuario) {
                    app.editable = true;
                    app.mostrarABM = true;
                    app.idUsuario = usuario.id;
                    app.primerNombre = usuario.nombre;
                    app.segundoNombre = usuario.segundoNombre;
                    app.primerApellido = usuario.apellido;
                    app.segundoApellido = usuario.segundoApellido;
                    app.sede = usuario.sede;
                    app.dni = usuario.dni;
                    app.mail = usuario.mail;
                },
                eliminar (usuario) {
                    app.mostrarABM = false;
                    app.modal = true;
                    app.idUsuario = usuario.id;
                    app.accionModal = "eliminar";
                    app.primerNombre = usuario.nombre;
                    app.segundoNombre = usuario.segundoNombre;
                    app.primerApellido = usuario.apellido;
                    app.segundoApellido = usuario.segundoApellido;
                    app.dni = usuario.dni;
                    app.sede = usuario.sede;
                    app.mail = usuario.mail;
                },
                reset (usuario) {
                    app.mostrarABM = false;
                    app.modal = true;
                    app.idUsuario = usuario.id;
                    app.accionModal = "resetear la contraseña";
                    app.primerNombre = usuario.nombre;
                    app.segundoNombre = usuario.segundoNombre;
                    app.primerApellido = usuario.apellido;
                    app.segundoApellido = usuario.segundoApellido;
                },
                cancelarABM(){
                    this.limpiarErrores()
                    app.mostrarABM = false;
                    app.idUsuario = null;
                    app.primerNombre = null;
                    app.segundoNombre = null;
                    app.primerApellido = null;
                    app.segundoApellido = null;
                    app.dni = null;
                    app.sede = null;
                    app.mail = null;
                },
                cancelarModal(){
                    app.modal = false;
                },
                limpiarErrores () {
                    document.getElementById("primerNombre").classList.remove("errorInput");
                    this.errorPrimerNombre = "";
                    document.getElementById("segundoNombre").classList.remove("errorInput");
                    this.errorSegundoNombre = "";
                    document.getElementById("primerApellido").classList.remove("errorInput");
                    this.errorPrimerApellido = "";
                    document.getElementById("segundoApellido").classList.remove("errorInput");
                    this.errorSegundoApellido = "";
                    document.getElementById("dni").classList.remove("errorInput");
                    this.errorDni = "";
                    document.getElementById("selectSede").classList.remove("errorInput");
                    this.errorSede = "";
                    document.getElementById("mail").classList.remove("errorInput");
                    this.errorMail = "";
                },
                validarCampo (campo) {
                    switch (campo) {
                        // VALIDACION PRIMER NOMBRE
                        case 'primerNombre':
                            if (this.primerNombre == null) {
                                document.getElementById("primerNombre").classList.add("errorInput");
                                this.errorPrimerNombre = "Campo requerido";
                            } else if (this.primerNombre.trim() == '') {
                                document.getElementById("primerNombre").classList.add("errorInput");
                                this.errorPrimerNombre = "Campo requerido";
                            } else if (this.primerNombre.trim().length < 3) {
                                document.getElementById("primerNombre").classList.add("errorInput");
                                this.errorPrimerNombre = "Mínimo 3 letras";
                            } else if (!/^[a-zA-Z\ áéíóúÁÉÍÓÚñÑ\s]*$/.test(this.primerNombre)) {
                                document.getElementById("primerNombre").classList.add("errorInput");
                                this.errorPrimerNombre = "Caracteres inválidos";
                            } else {
                                document.getElementById("primerNombre").classList.remove("errorInput");
                                this.errorPrimerNombre = "";
                            }
                        break;
                        
                        // VALIDACION SEGUNDO NOMBRE
                        case 'segundoNombre':
                            if (this.segundoNombre != null) {
                                if (this.segundoNombre.trim() != '') {
                                    if (this.segundoNombre.trim().length < 3) {
                                        document.getElementById("segundoNombre").classList.add("errorInput");
                                        this.errorSegundoNombre = "Mínimo 3 letras";
                                    } else if (!/^[a-zA-Z\ áéíóúÁÉÍÓÚñÑ\s]*$/.test(this.segundoNombre)) {
                                        document.getElementById("segundoNombre").classList.add("errorInput");
                                        this.errorSegundoNombre = "Caracteres inválidos";
                                    }  else {
                                        document.getElementById("segundoNombre").classList.remove("errorInput");
                                        this.errorSegundoNombre = "";
                                    }
                                }
                            }
                        break;

                        // VALIDACION PRIMER APELLIDO
                        case 'primerApellido':
                            if (this.primerApellido == null) {
                                document.getElementById("primerApellido").classList.add("errorInput");
                                this.errorPrimerApellido = "Campo requerido";
                            } else if (this.primerApellido.trim() == '') {
                                document.getElementById("primerApellido").classList.add("errorInput");
                                this.errorPrimerApellido = "Campo requerido";
                            } else if (this.primerApellido.trim().length < 3) {
                                document.getElementById("primerApellido").classList.add("errorInput");
                                this.errorPrimerApellido = "Mínimo 3 letras";
                            } else if (!/^[a-zA-Z\ áéíóúÁÉÍÓÚñÑ\s]*$/.test(this.primerApellido)) {
                                document.getElementById("primerApellido").classList.add("errorInput");
                                this.errorPrimerApellido = "Caracteres inválidos";
                            } else {
                                document.getElementById("primerApellido").classList.remove("errorInput");
                                this.errorPrimerApellido = "";
                            }                         
                        break;
                        
                        // VALIDACION SEGUNDO NOMBRE
                        case 'segundoApellido':
                            if (this.segundoApellido != null) {
                                if (this.segundoApellido.trim() != '') {
                                    if (this.segundoApellido.trim().length < 3) {
                                        document.getElementById("segundoApellido").classList.add("errorInput");
                                        this.errorSegundoApellido = "Mínimo 3 letras";
                                    } else if (!/^[a-zA-Z\ áéíóúÁÉÍÓÚñÑ\s]*$/.test(this.segundoApellido)) {
                                        document.getElementById("segundoApellido").classList.add("errorInput");
                                        this.errorSegundoApellido = "Caracteres inválidos";
                                    }  else {
                                        document.getElementById("segundoApellido").classList.remove("errorInput");
                                        this.errorSegundoApellido = "";
                                    }    
                                }
                            }
                        break;

                        // VALIDACION DNI
                        case 'dni':
                            if (this.dni == null) {
                                document.getElementById("dni").classList.add("errorInput");
                                this.errorDni = "Campo requerido";
                            } else if (this.dni.trim() == '') {
                                document.getElementById("dni").classList.add("errorInput");
                                this.errorDni = "Campo requerido";
                            } else if (this.dni.trim().length != 8) {
                                document.getElementById("dni").classList.add("errorInput");
                                this.errorDni = "8 dígitos";
                            } else if (!/^[0-9,$]*$/.test(this.dni)) {
                                document.getElementById("dni").classList.add("errorInput");
                                this.errorDni = "Campo numérico";
                            } else {
                                document.getElementById("dni").classList.remove("errorInput");
                                this.errorDni = "";
                            }  
                        break;

                        // VALIDACION MAIL
                        case 'mail':
                            if (this.mail == null) {
                                document.getElementById("mail").classList.add("errorInput");
                                this.errorMail = "Campo requerido";
                            } else if (this.mail.trim() == '') {
                                document.getElementById("mail").classList.add("errorInput");
                                this.errorMail = "Campo requerido";
                            } else if (!this.validarMail(this.mail)) {
                                document.getElementById("mail").classList.add("errorInput");
                                this.errorMail = "Formato de mail inválido";
                            } else {
                                document.getElementById("mail").classList.remove("errorInput");
                                this.errorMail = "";
                            }  
                        break;

                        // VALIDACION SEGUNDO NOMBRE
                        case 'sede':
                            if (this.sede == null) {
                                document.getElementById("selectSede").classList.add("errorInput");
                                this.errorSede = "Campo requerido";
                            } else {
                                document.getElementById("selectSede").classList.remove("errorInput");
                                this.errorSede = "";
                            }  
                        break;
                    
                        default:
                        break;
                    }
                },
                validarMail (mail) {
                    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
                        return (true)
                    }
                    return (false)
                },
                validarForm () {
                    this.validarCampo("primerNombre");
                    this.validarCampo("segundoNombre");
                    this.validarCampo("primerApellido");
                    this.validarCampo("segundoApellido");
                    this.validarCampo("dni");
                    this.validarCampo("sede");
                    this.validarCampo("mail");  
                    
                    if (
                        this.errorPrimerNombre ||
                        this.errorSegundoNombre ||
                        this.errorPrimerApellido ||
                        this.errorSegundoApellido ||
                        this.errorMail ||
                        this.errorDni ||
                        this.errorSede
                    ) {
                        return;
                    }
                    this.modal = true;
                    if (this.editable) {
                        this.accionModal = "modificar";
                    } else {
                        this.accionModal = "crear";
                    }
                },
                confirmarCreacion () {
                    app.creando = true;
                    let formdata = new FormData();
                    formdata.append("primerNombre", app.primerNombre);
                    formdata.append("segundoNombre", app.segundoNombre);
                    formdata.append("primerApellido", app.primerApellido);
                    formdata.append("segundoApellido", app.segundoApellido);
                    formdata.append("dni", app.dni);
                    formdata.append("sede", app.sede);
                    formdata.append("mail", app.mail);
                    axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=insertarUsuario", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            if (response.data.mensaje == "El mail ya se encuentra registrado" ||
                                response.data.mensaje == "El dni ya se encuentra registrado"
                            ) {
                                app.modal = false;    
                            }
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.modal = false;
                            app.mostrarABM = false;
                            app.primerNombre = null;
                            app.segundoNombre = null;
                            app.primerApellido = null;
                            app.segundoApellido = null;
                            app.dni = null;
                            app.sede = null;
                            app.mail = null;
                            app.consultarUsuarios();
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
                    formdata.append("id", app.idUsuario);
                    formdata.append("nombre", app.primerNombre);
                    formdata.append("segundoNombre", app.segundoNombre);
                    formdata.append("apellido", app.primerApellido);
                    formdata.append("segundoApellido", app.segundoApellido);
                    formdata.append("dni", app.dni);
                    formdata.append("sede", app.sede);
                    formdata.append("mail", app.mail);
                    axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=editarUsuario", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.modal = false;
                            app.mostrarABM = false;
                            app.idUsuario= null;
                            app.primerNombre = null;
                            app.segundoNombre = null;
                            app.primerApellido = null;
                            app.segundoApellido = null;
                            app.dni = null;
                            app.mail = null;
                            app.sede = null;
                            app.consultarUsuarios();
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
                    formdata.append("id", app.idUsuario);
                    axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=eliminarUsuario", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.modal = false;
                            app.idUsuario= null;
                            app.primerNombre = null;
                            app.segundoNombre = null;
                            app.primerApellido = null;
                            app.segundoApellido = null;
                            app.dni = null;
                            app.mail = null;
                            app.sede = null;
                            app.consultarUsuarios();
                        }
                        app.eliminando = false;
                    }).catch( error => {
                        app.eliminando = false;
                        app.mostrarToast("Error", response.data.mensaje);
                    })
                },
                consultarUsuarios() {
                    this.buscandoUsuarios = true;
                    axios.get("http://localhost/proyectos/pedidos2/conexion/api.php?accion=consultarUsuarios")
                    .then(function(response){
                        app.buscandoUsuarios = false;

                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.usuarios = response.data.usuarios;
                        }
                    })
                    
                },
                consultarSedes() {
                    axios.get("http://localhost/proyectos/pedidos2/conexion/api.php?accion=consultarSedes")
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.sedes = response.data.sedes;
                        }
                    })
                    
                },
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
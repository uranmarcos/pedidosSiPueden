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
                    INICIO - ARTICULOS
                </span>

                <button type="button" @click="mostrarABM=true, editable = false" class="btn boton" v-if="!mostrarABM">
                    Nuevo Articulo
                </button>
            </div>
        
            <!-- START COMPONENTE ABM ARTICULOS -->
            <div class="cardABM" v-if="mostrarABM">
                <h5 class="px-3 py-3 m-0">{{editable ? "Editar articulo" : "Crear articulo"}}</h5>
                <div class="px-3 row">
                    <div class="col-sm-12 col-md-4">
                        <label for="descripcion">Descripcion <span class="errorLabel" v-if="errorDescripcion">{{errorDescripcion}}</span></label>
                        <input class="form-control" maxlength="30" @keyup="validarCampo('descripcion')" id="descripcion" v-model="descripcion">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="categoria">Categoria <span class="errorLabel" v-if="errorCategoria">{{errorCategoria}}</span></label>
                        <select class="form-control" name="selectCategoria" @change="validarCampo('categoria')" id="selectCategoria" v-model="categoria">
                            <option v-for="categoria in categorias" v-bind:value="categoria.id" >{{categoria.descripcion}}</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="medida">Medida <span class="errorLabel" v-if="errorMedida">{{errorMedida}}</span></label>
                        <select class="form-control" name="selectMedida" @change="validarCampo('medida')" id="selectMedida" v-model="medida">
                            <option v-for="medida in medidas" v-bind:value="medida.id" >{{medida.descripcion}}</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4" v-if="editable">
                        <label for="medida">Vigente</label>
                        <select class="form-control" name="selectMedida" @change="validarCampo('medida')" v-model="vigente">
                            <option value="0" >No</option>
                            <option value="1" >Sí</option>
                        </select>
                    </div>
                    <div class="my-3 d-flex justify-content-around">
                        <button type="button" @click="cancelarABM()" class="btn boton" >Cancelar</button>
                        <button type="button" @click="validarForm()" class="btn botonConfirm">Confirmar</button>
                    </div>
                </div>
            </div>
            <!-- END COMPONENTE ABM ARTICULOS -->
            
            <!-- START COMPONENTE LOADING BUSCANDO ARTICULOS -->
            <div class="contenedorLoading" v-if="buscandoArticulos">
                <div class="loading">
                    <div class="spinner-border" role="status">
                        <span class="sr-only"></span>
                    </div>
                </div>
            </div>
            <!-- END COMPONENTE LOADING BUSCANDO ARTICULOS -->

            <!-- START TABLA ARTICULOS -->
            <div class="contenedorTabla" v-else>
                <table class="table table-hover">
                    <thead class="tituloColumna">
                        <th>
                            ID
                        </th>
                        <th>
                            Descripción
                        </th>
                        <th>
                            Categoria
                        </th>
                        <th>
                            Medida
                        </th>
                        <th>
                            Vigente
                        </th>
                        <th style="width: 150px">
                            ACCION
                        </th>
                    </thead>
                    <tbody v-if="articulos.length != 0">
                        <tr v-for="articulo in articulos">
                            <td>
                                {{articulo.id}}
                            </td>
                            <td>
                                {{articulo.descripcion}}
                            </td>
                            <td>
                                {{articulo.categoria}}
                            </td>
                            <td>
                                {{medidas.filter(element => element.id == articulo.medida)[0]["descripcion"]}}
                            </td>
                            <td>
                                {{articulo.vigente == 0 ? "No" : "Sí"}}
                            </td>
                            <td>
                                <button class="botonAccion botonEdit" @click="editar(articulo)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="articulos.length == 0">
                    <span class="sinResultados">
                       NO SE ENCONTRÓ RESULTADOS PARA MOSTRAR
                    </span>
                </div>
            </div>
            <!-- END TABLA ARTICULOS -->

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
                            <b class="d-flex justify-content-center">¿Desea {{accionModal}} el articulo?</b>
                        </div>

                        <div class="row d-flex justify-content-center my-3">
                            Descripcion: {{app.descripcion}} 
                            <br>
                            Categoria: {{categorias.filter(element => element.id == app.categoria)[0]["descripcion"]}}
                            <br>
                            Medida: {{medidas.filter(element => element.id == app.medida)[0]["descripcion"]}}
                            <br>
                            Vigente: {{app.vigente == 0 ? "No" : "Sí"}}
                            <br>
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
                errorDescripcion: "",
                errorMedida: "",
                errorCategoria: "",
                tituloToast: "",
                textoToast: "",
                modal: false,
                idArticulo:null,
                editable: false,
                buscandoArticulos: false,
                descripcion: null,
                medida: null,
                categoria: null,
                vigente: null,
                mostrarABM: false,
                //
                confirm: false,
                editando: false,
                creando: false,
                articulos: [],
                categorias: [
                    {
                        id: "Alimentos",
                        descripcion: "Alimentos"
                    },
                    {
                        id: "Desayuno",
                        descripcion: "Desayuno / Merienda"
                    },
                    {
                        id: "Limpieza",
                        descripcion: "Limpieza"
                    },
                    {
                        id: "Uso Personal",
                        descripcion: "Uso Personal"
                    }
                ],
                medidas: [
                    {
                        id: "kg",
                        descripcion: "Kilogramos"
                    },
                    {
                        id: "sq",
                        descripcion: "Saquitos"
                    },
                    {
                        id: "lt",
                        descripcion: "Latas"
                    },
                    {
                        id: "un",
                        descripcion: "Unidades"
                    },
                    {
                        id: "so",
                        descripcion: "Sobres"
                    },
                    {
                        id: "ca",
                        descripcion: "Cajas"
                    },
                    {
                        id: "ro",
                        descripcion: "Rollos"
                    }
                ],
                rol: null
            },
            mounted: function() {
                this.consultarArticulos();
                this.rol = "<?php echo $_SESSION['rol'] ?>";
                if (this.rol == 'admin') {
                    document.getElementById("navArticulos").classList.add("active");
                }
            },
            methods:{
                editar (articulo) {
                    app.editable = true;
                    app.mostrarABM = true;
                    app.idArticulo = articulo.id;
                    app.descripcion = articulo.descripcion;
                    app.categoria = articulo.categoria;
                    app.medida = articulo.medida;
                    app.vigente = articulo.vigente;
                },
                cancelarABM(){
                    this.limpiarErrores()
                    app.mostrarABM = false;
                    app.idArticulo = null;
                    app.descripcion = null;
                    app.medida = null;
                    app.categoria = null;
                    app.vigente = null;
                },
                cancelarModal(){
                    app.modal = false;
                },
                limpiarErrores () {
                    document.getElementById("descripcion").classList.remove("errorInput");
                    this.errorDescripcion = "";
                    document.getElementById("selectCategoria").classList.remove("errorInput");
                    this.errorCategoria = "";
                    document.getElementById("selectMedida").classList.remove("errorInput");
                    this.errorMedida = "";
                },
                validarCampo (campo) {
                    switch (campo) {
                        // VALIDACION DESCRIPCION
                        case 'descripcion':
                            if (this.descripcion == null) {
                                document.getElementById("descripcion").classList.add("errorInput");
                                this.errorDescripcion = "Campo requerido";
                            } else if (this.descripcion.trim() == '') {
                                document.getElementById("descripcion").classList.add("errorInput");
                                this.errorDescripcion = "Campo requerido";
                            } else if (this.descripcion.trim().length < 3) {
                                document.getElementById("descripcion").classList.add("errorInput");
                                this.errorDescripcion = "Mínimo 3 letras";
                            } else if (!/^[a-zA-Z\ áéíóúÁÉÍÓÚñÑ\s]*$/.test(this.descripcion)) {
                                document.getElementById("descripcion").classList.add("errorInput");
                                this.errorDescripcion = "Caracteres inválidos";
                            } else {
                                document.getElementById("descripcion").classList.remove("errorInput");
                                this.errorDescripcion = "";
                            }
                        break;

                        // VALIDACION CATEGORIA
                        case 'categoria':
                            if (this.categoria == null) {
                                document.getElementById("selectCategoria").classList.add("errorInput");
                                this.errorCategoria = "Campo requerido";
                            } else {
                                document.getElementById("selectCategoria").classList.remove("errorInput");
                                this.errorCategoria = "";
                            }  
                        break;

                        // VALIDACION MEDIDA
                        case 'medida':
                            if (this.medida == null) {
                                document.getElementById("selectMedida").classList.add("errorInput");
                                this.errorMedida = "Campo requerido";
                            } else {
                                document.getElementById("selectMedida").classList.remove("errorInput");
                                this.errorMedida = "";
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
                    this.validarCampo("descripcion");
                    this.validarCampo("medida");
                    this.validarCampo("categoria");
                    
                    if (
                        this.errorDescripcion ||
                        this.errorMedida ||
                        this.errorCategoria
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
                    formdata.append("descripcion", app.descripcion);
                    formdata.append("medida", app.medida);
                    formdata.append("categoria", app.categoria);
                    axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=insertarArticulo", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.modal = false;
                            app.mostrarABM = false;
                            app.descripcion = null;
                            app.categoria = null;
                            app.medida = null;
                            app.consultarArticulos();
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
                    formdata.append("id", app.idArticulo);
                    formdata.append("categoria", app.categoria);
                    formdata.append("descripcion", app.descripcion);
                    formdata.append("medida", app.medida);
                    formdata.append("vigente", app.vigente);
                    
                    axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=editarArticulo", formdata)
                    .then(function(response){
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.mostrarToast("Éxito", response.data.mensaje);
                            app.modal = false;
                            app.mostrarABM = false;
                            app.idArticulo= null;
                            app.descripcion = null;
                            app.categoria = null;
                            app.medida = null;
                            app.vigente = null;
                            app.consultarArticulos();
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
                consultarArticulos() {
                    this.buscandoArticulos = true;
                    axios.get("http://localhost/proyectos/pedidos2/conexion/api.php?accion=consultarArticulos")
                    .then(function(response){
                        app.buscandoArticulos = false;
                        if (response.data.error) {
                            app.mostrarToast("Error", response.data.mensaje);
                        } else {
                            app.articulos = response.data.articulos;
                        }
                    })
                    
                }
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
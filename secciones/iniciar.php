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
    <link href="../css/modal.css" rel="stylesheet">
    <link href="../css/opciones.css" rel="stylesheet">
    

</head>
<body>
    
    <div id="app">

        <?php require("../shared/header.html")?>
        
        <div class="container">
            
            <div class="breadcrumb">
                <span>
                    INICIO - GENERAR PEDIDO
                </span>
    
                <button type="button" @click="irAPedidos()" class="btn boton">
                    Mis pedidos
                </button>
            </div>
            
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
                        <th class="hide">
                            ID
                        </th>
                        <th >
                            Articulo
                        </th>
                        <th>
                            Cantidad
                        </th>
                        <th>
                            Categoria
                        </th>
                    </thead>
                    <tbody v-if="articulos.length != 0">
                        <tr v-for="articulo in articulos">
                            <td class="hide">
                                {{articulo.id}}
                            </td>
                            <td class="columnaArticulo">
                                <div class="descripcionArticulo">
                                    {{articulo.descripcion.toUpperCase()}}
                                    
                                </div>
                                <div class="descripcionMedida">
                                    {{medidas.filter(element => element.id == articulo.medida)[0]["descripcion"]}}
                                </div>
                            </td>
                            <td class="columnaCantidad">
                                <input 
                                    class="form-control" 
                                    @keyup="change(articulo)" 
                                    type="number" 
                                    min="0"
                                    max="999"
                                    v-model="articulo.value"
                                >
                            </td>
                            <td class="columnaCategoria">
                                {{articulo.categoria}}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="row rowOtros">
                    <div class="col-4">
                        OTROS
                    </div>
                    <div class="col-8 contenedorTextarea">
                       <textarea v-model="otros" ></textarea>
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        <button type="button" @click="validarForm()" class="btn boton" :disabled="pedido.length == 0 && otros.trim() == ''">
                            Generar pedido
                        </button>
                    </div>
                </div>
                <div v-if="articulos.length == 0">
                    <span class="sinResultados">
                       NO SE ENCONTRÓ RESULTADOS PARA MOSTRAR
                    </span>
                </div>
            </div>
            <!-- END TABLA PEDIDOS -->

            <div v-if="modal">
                <!-- The Modal -->
                <div id="myModal" class="modal">

                <!-- Modal content -->
                <div class="modal-content">
                    <div class="">
                        <div class="row d-flex justify-content-center tituloModal">    
                            <h5 class="d-flex justify-content-center">CONFIRMACIÓN</h5>
                        </div>

                        <div v-if="!generando">
                            <div class="row d-flex justify-content-center my-3">
                                <b class="d-flex justify-content-center">¿Desea enviar el pedido?</b>
                            </div>

                                                
                            <div class="row">
                                <div class="col-sm-12 d-flex justify-content-center">
                                    <button type="button" @click="detalle=true" class="btn boton btnRevisar" v-if="!detalle">REVISAR PEDIDO</button>
                                    <button type="button" @click="detalle=false" class="btn boton btnRevisar" v-else>OCULTAR PEDIDO</button>
                                </div>

                                <div v-if="detalle" class="col-sm-12 d-flex justify-content-center ">
                                    <div class="row detalle">
                                        <div v-for="(producto, index) in pedido" class="col-sm-6" v-if="(index + 1) <= (pedido.length / 2)">
                                            {{articulos.filter(element=> element.id == producto.id)[0].descripcion}} : {{producto.cantidad}} {{articulos.filter(element=> element.id == producto.id)[0].medida}}
                                        </div>
                                        <div class="col-sm-6" v-else>
                                            {{articulos.filter(element=> element.id == producto.id)[0].descripcion}} : {{producto.cantidad}} {{articulos.filter(element=> element.id == producto.id)[0].medida}}
                                        </div>
                                        <div class="col-12" v-if="otros.trim() != ''">
                                            Otros: {{otros}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row d-flex justify-content-around mt-3">

                                <div class="col-sm-12 col-md-5 d-flex justify-content-center mt-sm-3 mt-md-0" >
                                    <button type="button" @click="cancelarModal()" class="btn boton" >Cancelar</button>
                                </div>

                                <div class="col-sm-12 col-md-5 d-flex justify-content-center mt-sm-3 mt-md-0" >
                                    <button type="button" @click="confirmarPedido()" class="btn botonConfirm">Confirmar</button>
                                </div>
    
                            </div>
                        </div>

                        <div v-if="generando">
                            <div class="row d-flex justify-content-center my-3">
                                <b class="d-flex justify-content-center">Enviando el pedido...</b>
                            </div>
                            <div class="col-sm-12 d-flex justify-content-center mt-sm-3 mt-md-0">
                                <button type="button" class="btn botonConfirm">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only"></span>
                                    </div>
                                </button>
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
        .hide{
            display: none;
        }
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


      

           
        .descripcionMedida{
            font-size: 12px;
            height: 12px;
            text-align: left;
            margin-left: 10px;
            line-height:12px;
        }
        .descripcionArticulo{
            font-size: 16px;
            margin-top: 10px;
            font-weight: bolder;
            margin-left: 10px;
            text-align: left;
            height:20px;
            line-height:20px;
        }
        .rowOtros{
            font-size: 16px;
            margin-top: 10px;
            font-weight: bolder;
            margin-left: 10px;
            text-align: left;
            height:150px;
            color: black;
            line-height:20px;
            width: 100%;
            margin:auto;
        }
        .contenedorTextarea{
            padding-left: 50px;
        }
        textarea{
            width: 100%;
            min-height: 50px;
        }
        input.form-control{
            margin-top: 7px
        }
        td{
            height: 60px !important;
        }
        .columnaArticulo{
            width: 40%;
        }
        .columnaCantidad{
            width: 30%;
        }
        .columnaCategoria{
            width: 30%;
        }
        .btnRevisar{
            width: 100%;
        }
        .detalle{
            width: 100%;
            margin: 20px 0 0;
            padding: 5px;
            border-radius: 5px;
            font-size: 14px;
            border: solid 1px black;
        }
        textarea{
            padding: 10px;
            font-size: 14px;
            border-radius: 5px;
            border: solid 1px #ced4da;
        }
        textarea:focus{
            outline: none;
        }
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
                idPedido:null,
                buscandoArticulos: false,
                //
                confirm: false,
                generando: false,
                articulos: [],
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
                pedido: [],
                detalle: false,
                otros: ""
            },
            mounted: function() {
                this.consultarArticulos();
            },
            methods:{
                irAPedidos () {
                    window.location.href = 'http://localhost/proyectos/pedidos2/secciones/pedidos.php';   
                },
                validarForm () {
                    if (this.pedido.length != 0 || this.otros.trim() != '') {
                        app.modal = true;
                    }
                },
                change (articulo) {
                    let producto = {
                        id: articulo.id,
                        cantidad: articulo.value
                    }
                    
                    let art = this.pedido.find(element => element.id == producto.id);
                    let posicion = this.pedido.indexOf(art);
                   
                    if (posicion >= 0) {
                        if (producto.cantidad != '') {
                            this.pedido[posicion] = producto
                        } else {
                            this.pedido.splice(posicion, 1)
                        }
                    } else {
                        this.pedido.push(producto);
                    }
                },
                cancelarModal(){
                    app.modal = false;
                },
                confirmarPedido () {
                    app.generando = true;

                    if (this.otros.trim() != '') {
                        let producto = {
                            id: 'otros',
                            cantidad: this.otros
                        }
                        this.pedido.push(producto);
                    }
                    
                    const pedid = JSON.stringify(this.pedido);
                    let formdata = new FormData();
                    formdata.append("sede", 13);
                    formdata.append("usuario",1);
                    formdata.append("pedido", this.pedido);

                    axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=generarPedido", formdata)
                        .then(function(response){
                            console.log(response.data);
                            if (response.data.error) {
                                app.mostrarToast("Error", response.data.mensaje);
                            } else {
                                console.log("se genero");
                                app.mostrarToast("Éxito", response.data.mensaje);
                                app.modal = false;
                                app.pedido = [];
                                app.otros = '';
                                setTimeout(() => {
                                    window.location.href = 'http://localhost/proyectos/pedidos2/secciones/pedidos.php'; 
                                }, 5000);
                            }
                            app.generando = false;
                        }).catch( error => {
                            app.generando = false;
                            app.mostrarToast("Error", response.data.mensaje);
                        })
                },
                // confirmarCreacion () {
                //     app.creando = true;
                //     let formdata = new FormData();
                //     formdata.append("descripcion", app.descripcion);
                //     formdata.append("medida", app.medida);
                //     formdata.append("categoria", app.categoria);
                //     axios.post("http://localhost/proyectos/pedidos2/conexion/api.php?accion=insertarArticulo", formdata)
                //     .then(function(response){
                //         if (response.data.error) {
                //             app.mostrarToast("Error", response.data.mensaje);
                //         } else {
                //             app.mostrarToast("Éxito", response.data.mensaje);
                //             app.modal = false;
                //             app.mostrarABM = false;
                //             app.descripcion = null;
                //             app.categoria = null;
                //             app.medida = null;
                //             app.consultarArticulos();
                //         }
                //         app.creando = false;
                //     }).catch( error => {
                //         app.creando = false;
                //         app.mostrarToast("Error", response.data.mensaje);
                //     })
                // },
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
<?php
session_start();
if (!$_SESSION["login"] ) {
    header("Location: index.html");
}
if(time() - $_SESSION['login_time'] >= 1000){
    session_destroy(); // destroy session.
    header("Location: index.html");
    die(); 
} else {        
   $_SESSION['login_time'] = time();
}
$_SESSION["pedido"] = "meriendas";
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
</head>
<body>
    <div id="app">
        <?php require("shared/header.html")?>
        
        <div class="container">
            <!-- START BREADCRUMB -->
            <div class="col-12 p-0">
                <div class="breadcrumb">
                    <span class="pointer mx-2" @click="irA('home')">Inicio</span>  -  <span class="mx-2 grey"> Desayunos / Meriendas </span>
                </div>
            </div>
            <!-- END BREADCRUMB -->           

            <div class="row mt-6">
                <div class="col-12">
                    <div class="contenedor py-3">    
                        <span class="subtituloCard">ARTICULOS DISPONIBLES</span>
                        <div class="row contenedorArticulos d-flex justify-content-around">
                            <article class="col-10 col-md-5 articulo" :class="articulo.checked ? 'remarcado' : ''" @click="articulo.checked = !articulo.checked" v-for="articulo  in articulos">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16" v-if="articulo.checked">
                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                                </svg>
                                <div class="textoArticulo">{{articulo.nombre}}</div>
                            </article>
                            <div class="col-10 col-md-5 d-flex justify-content-center align-items-center">
                                <button type="button" @click="avanzar()" :disabled="habilitarAvanzar()" class="botonGeneral">
                                    CONTINUAR
                                </button>
                            </div>
                        </div>                
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style scoped>  
        /* ABM LIBROS */
        .contenedor{
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
            border: solid 1px #7C4599;
            border-radius: 5px;
        }


        /*  color: #7C4599;  LIBROS */
        .articulo{
            height:50px;
            border: solid 1px grey;
            border-radius: 10px;
            margin: 10px 0px;
            display:flex;
            color: grey;
            font-weight: bolder;
            align-items: center;
            justify-content: center;
            padding: 0!important;
        }
        .articulo:hover{
            cursor: pointer;
        }
        .remarcado{
            background-color: #7C4599;
            color: white;
            border: solid 1px #7C4599;
        }
        .textoArticulo{
            font-size: 1em;
            margin-top:5px;
            text-transform: uppercase;
            text-align: center;
            padding-left: 5px;
        }
     
        .contenedorArticulos{
            width: 100%;
            margin:10px auto;
        }
    </style>
    <script>
        var app = new Vue({
            el: "#app",
            components: {                
            },
            data: {
                articulos: [
                    {
                        nombre: "Alfajores",
                        checked: false
                    },
                    {
                        nombre: "Cacao",
                        checked: false
                    },
                    {
                        nombre: "Galletitas",
                        checked: false
                    },
                    {
                        nombre: "Leche",
                        checked: false
                    },
                    {
                        nombre: "Mate cocido",
                        checked: false
                    },
                    {
                        nombre: "Té",
                        checked: false
                    },
                    {
                        nombre: "Turrones",
                        checked: false
                    }
                ],
                
            },
            mounted() {
                let pedido = JSON.parse(localStorage.getItem("pedido"));
                if (pedido) {
                    if (pedido.tipo == "merienda") {
                        pedido.articulos.forEach(element => {
                            this.articulos.find(e => e.nombre == element.nombre).checked = true;
                        });
                    }
                }
            },
            methods:{
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
                habilitarAvanzar () {
                    let marcados = this.articulos.filter(element => element.checked);
                    if (marcados.length != 0) {
                        return false;
                    } 
                    return true;
                },
                avanzar () {
                    let pedido = {
                        tipo: "merienda",
                        articulos: this.articulos.filter(element => element.checked)
                    }
                    
                    localStorage.setItem("pedido", JSON.stringify(pedido))
                    window.location.href = 'envio.php';    
                },
                irAHome () {
                    window.location.href = 'home.php';    
                },
            }
        })
    </script>
</body>
</html>
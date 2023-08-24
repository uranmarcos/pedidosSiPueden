<?php
session_start();
if (!$_SESSION["login"] ) {
    header("Location: index.html");
}

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

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
        
        <div class="container containerMenu">
            <div class="row mt-6">
                <div class="col-md-4 col-sm-12 my-2 my-md-5 d-flex justify-content-center">
                    <div class="opciones" @click="irA('materiales')">
                        Pedido de <br>materiales
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 my-2 my-md-5 d-flex justify-content-center">
                    <div class="opciones" @click="irA('biblioteca')">
                    
                        Biblioteca
        
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 my-2 my-md-5 d-flex justify-content-center">
                    <div class="opciones" @click="irA('merienda')">
                    
                        Desayuno /
                        <br>Merienda
        
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 my-2 my-md-5 d-flex justify-content-center">
                    <div class="opciones"  @click="irA('planificaciones')">
                        Banco de planificaciones
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 my-2 my-md-5 d-flex justify-content-center"  @click="irA('recursos')">
                    <div class="opciones">
                        Otros recursos
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 my-2 my-md-5 d-flex justify-content-center"  @click="irA('videos')">
                    <div class="opciones">
                        Videos
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 my-2 my-md-5 d-flex justify-content-center"  @click="irA('pedidos')" v-if="rol =='admin' || rol =='superAdmin'">
                    <div class="opciones">
                        Pedidos <br>realizados
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style scoped>
        .disabled{
            background-color: lightgrey;
        }
        .avisoDisabled{
            width:100% !important;
            display: flex;
            justify-content: center;
            text-align: center;
            font-size: 10px
        }
        .containerMenu{
            min-height: 85vh;
            margin: auto;
            display: flexbox;
            align-items: center;
            color: rgb(94, 93, 93);
        }
        .opciones{
            flex-direction: column;
            border: solid 1px purple;
            border-radius: 10px;
            color: purple;
            text-transform: uppercase;
            text-align: center;
            width: 200px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .opciones:hover{
            cursor: pointer;
        }
            
    </style>
    <script>
        var app = new Vue({
            el: "#app",
            components: {
                
            },
            data: {
                rol: ""
            },
            mounted() {
                this.rol = "<?php echo $_SESSION["rol"]; ?>";
            },
            methods:{
                irA(param) {
                    switch (param) {
                        case "materiales":
                            localStorage.setItem("perfil", "materiales")     
                            window.location.href = 'materiales.php';   
                            break;
                    
                        case "biblioteca":
                            localStorage.setItem("perfil", "biblioteca") 
                            window.location.href = 'banco.php';        
                            break;
                        
                        case "recursos":
                            localStorage.setItem("perfil", "recursos") 
                            window.location.href = 'banco.php';         
                            break;

                        case "planificaciones":
                            localStorage.setItem("perfil", "planificaciones") 
                            window.location.href = 'banco.php';        
                            break;

                        case "videos":
                            localStorage.setItem("perfil", "videos") 
                            window.location.href = 'banco.php';        
                            break;


                        case "merienda":
                            localStorage.setItem("perfil", "meriendas") 
                            window.location.href = 'meriendas.php';         
                            break;

                        case "pedidos":
                            window.location.href = 'pedidos.php';         
                            break;

                        default:
                            break;
                    }
                }
            }
        })
    </script>
</body>
</html>
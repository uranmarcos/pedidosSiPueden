<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI PEDIDOS</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.21/vue.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.2.1/axios.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="css/tabla.css" rel="stylesheet">
    <link href="css/opciones.css" rel="stylesheet">
    <link href="css/modal.css" rel="stylesheet">
  
 
</head>
<body>
    <div id="app">
        <?php require("shared/header.html")?>
        
        <div class="container">
            <div class="row mt-6">
              <div class="col-md-6 col-sm-10">
                <div class="opciones" @click="irA('usuarios')">
                    Usuarios
                </div>
              </div>
              <div class="col-md-6 col-sm-10"  @click="irA('articulos')">
                <div class="opciones">
                    Articulos
                </div>
              </div>
              <div class="col-md-6 col-sm-10"  @click="irA('sedes')">
                <div class="opciones">
                    Sedes
                </div>
              </div>
            </div>
          </div>
    </div>

    <style scoped>
        .opciones{
            border: solid 1px purple;
            border-radius: 10px;
            color: purple;
            text-transform: uppercase;
            width: 200px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
            
    </style>
    <script>
        var app = new Vue({
            el: "#app",
            components: {
                
            },
            data: {
                items: [
                    { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
                    { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
                    { age: 89, first_name: 'Geneva', last_name: 'Wilson' },
                    { age: 38, first_name: 'Jami', last_name: 'Carney' }
                ]
            },
            methods:{
                irA(param) {
                    console.log(param)
                    switch (param) {
                        case "usuarios":
                            window.location.href = 'http://localhost/proyectos/pedidos2/secciones/usuarios.php';         
                            break;
                    
                        case "articulos":
                            window.location.href = 'http://localhost/proyectos/pedidos2/secciones/articulos.php';         
                            break;

                        case "sedes":
                            window.location.href = 'http://localhost/proyectos/pedidos2/secciones/sedes.php';         
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
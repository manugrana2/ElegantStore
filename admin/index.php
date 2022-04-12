<?php
   session_start(); //Start the session 
   include '../includes/functions.php';
   include '../server/databaseClass.php';
   $database = new databaseClass();
  
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Collapsible sidebar using Bootstrap 4</title>

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="/assets/styles/admin.css">

    <!-- Font Awesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/fontawesome.min.js" integrity="sha512-5qbIAL4qJ/FSsWfIq5Pd0qbqoZpk5NcUVeAAREV2Li4EKzyJDEGlADHhHOSSCw0tHP7z3Q4hNHJXa81P92borQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>

    <div class="wrapper">
    <?php
if(isLogged()){

?>
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Administración</h3>
                <strong>Sho.php</strong>
            </div>

            <ul class="list-unstyled components">
                <li class="active"  id="newArticle">
                    <a href="#newArticle">
                        <i class="fas fa-cart-plus"></i>
                        Añadir Artículo
                    </a>
                </li>
                <li class="active"  id="editArticle">
                    <a href="#editArticle">
                        Editar Artículos
                    </a>
                </li>
                <li class="active"  id="orders">
                    <a href="#orders">
                    <i class="fas fa-shipping-fast"></i>
                        Pedidos y Ordenes
                    </a>
                </li>
                <li>
                    <a href="#editStore">
                    <i class="fas fa-wrench"></i>
                        Ajustes de la tienda
                    </a>
                    <a href="#userSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-copy"></i>
                        Usuarios
                    </a>
                    <ul class="collapse list-unstyled" id="userSubmenu">
                        <li>
                            <a href="#viewAllUsers">Ver todos</a>
                        </li>
                        <li>
                            <a href="#newUser">Nuevo</a>
                        </li>
                        <li>
                            <a href="#changePassword">Cambiar mi Contraseña</a>
                        </li>
                    </ul>
                </li>
                <li class="active">
                    <a href="#homeSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-edit"></i>
                        Blog
                    </a>
                    <ul class="collapse list-unstyled" id="homeSubmenu">
                        <li>
                            <a href="#">Nueva Entrada</a>
                        </li>
                        <li>
                            <a href="#">Todas</a>
                        </li>
                        <li>
                            <a href="#">Ajustes</a>
                        </li>
                    </ul>
                </li>
            </ul>

        </nav>

        <!-- Page Content  -->
        <div id="content">

            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                        <span>Menu</span>
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                        <li class="nav-item active">
                                <a class="nav-link" href="#">Cerrar Sesion</a>
                            </li>
                           
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="body">  
                <div class="welcome">
                    <div class="line"></div>

                    <h2>Bienvenido al portal de administración de su tienda</h2>
                    <p>Desde aquí puede añadir y editar productos, ver, agregar y editar usuarios, añadir y editar las entradas de su blog y configurar los aspectos de su tienda</p>
                    <p>Para comenzar, seleccione en el menú izquierdo lo que quiere hacer</p>
                </div>
                <div class="newArticleView hide">
                    <h2>Aquí puedes añadir nuevos artículos</h2>
                    <form id="newArticle__form" class="" enctype='multipart/form-data'>
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Nombre</label>
                            <input class="form-control" type="text" maxlength="62" name='product_name' id="nombre" placeholder="Nombre del Artículo" aria-label="Nombre del Artículo" required>
                        </div>
                        <div class="form-group">
                            <label for="detalles">Detalles del Producto</label>
                            <textarea class="form-control" id="detalles" rows="3" maxlength="520" name='product_details' required></textarea>
                         </div>
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Precio</label>
                            <input class="form-control" type="text" maxlength="6" style="width:150px" name='product_price' id="precio" placeholder="Precio" aria-label="Precio" required>
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoria</label>
                            <select class="form-control" id="categoria" name='product_category' required>
                            <option>Accesorios</option>
                            <option>Hoddies</option>
                            <option>Blusas</option>
                            <option>Bolsos</option>
                            <option>Buzos</option>
                            <option>Carteras</option>
                            <option>Chaquetas</option>
                            <option>Medias</option>
                            <option>Pantalones</option>
                            <option>Relojes</option>
                            <option>Vestidos</option>
                            <option>Zapatos</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Tallas y Disponibilidad:</label>
                            <div class="sizes">
                                <div class="form-row size">
                                   <div class="col-md-2 mb-3">
                                        <input type="text" class="form-control talla" placeholder="Talla">
                                    </div>
                                    <div class="col-md-4 mb-3 same">
                                        <input type="text" class="form-control disponible" placeholder="Cantidad">
                                        
                                    </div>
                                    <div class="col-md-3 mb-3">
                                    <button type="button" class="btn btn-secondary new-size">Añadir Talla</button>                               
                                    </div>
                                </div>                                
                            </div>
                        </div>
                        <!--<div class="form-group">
                            <label for="exampleFormControlSelect1">Colores  (Separados por comas):</label>
                            <div class="alert alert-warning" role="alert">
                            Añada primero una talla
                            </div>
                        </div>-->
                        <div class="form-group">
                         <input type="file" class="form-control" id="product_images" name='product_image' multiple/>
                        <center> <a href="javascript:selectImage()" class="img-selector"> <i class="fas fa-image"></i> Añadir Imagenes</a> </center>
                        </div>
                        
                         <button type="submit" class="btn btn-primary" style="float:right">Publicar</button>

                    </form>
                    <div class="preview-images">
                    <div class="row">
                        <div id="small-img" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 center">
                            <ul class="product-preview-images">
                           
                            </ul>
                        </div>
                    </div>
                </div>

                </div>
                <div class="editArticlesView hide">
                    <h2>Editar los articulos  guardados</h2>
                </div>
                <div class="ordersView hide">
                    <p>Aquí puedes ver tu lista de órdenes</p>
                </div>
                <div class="editStoreView hide">
                    <p>Aquí puedes editar la configuración de tu tienda</p>
                </div>
                <div class="viewAllUsersView hide">
                    <p>Aquí puedes ver y editar a los usuarios de la tienda</p>
                </div>
                <div class="newUserView hide">
                    <p>Aquí puedes añadir nuevos usuarios a tu tienda</p>
                </div>
                <div class="changePasswordView hide">
                    <p>Aquí puedes cambiar tu contraseña</p>
                </div>

            </div>
        </div>
        <?php 
}else{
    echo 'Inicia sesion';
}
?>
    </div>

    <!-- jQuery CDN -  version (=wit AJAX) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
 <script src="/assets/javascripts/admin.js"></script>
</body>

</html>
<?php

    require_once '../includes/functions.php';

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$re = '/([\w|-]+)/m';
$str = $_SERVER['REQUEST_URI'];

preg_match_all($re, $str, $link, PREG_SET_ORDER, 0);
$permalink = $link[0][0];

if (!empty($permalink)) {
    
    $product_id =  $database->getRow("SELECT * FROM permalinks WHERE `permalink` = ? AND `user` = ?",[$permalink,'products']);
    if($product_id){
        $product_id = $product_id['userid'];
        $product =  $database->getRow("SELECT * FROM products WHERE `product_id` = ?",[$product_id]);
        if($product){
            $product_id_next = $product['product_id'] + 1;
            $product_id_prev = $product['product_id'] - 1;

            $next_product = $database->getRow("SELECT permalink FROM products WHERE product_id = '$product_id_next'");

            $prev_product = $database->getRow("SELECT permalink FROM products WHERE product_id = '$product_id_prev'");
        }else{
             header("Location: 404.php");
        }
    }else{
        header("Location: 404.php");
    }
} else {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MajoShop! - Comprar <?php echo $product['product_name']?></title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">

    <!-- Import fontawesome libraries to get access to its css fonts -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
        integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <!-- Import bootstrap CSS dependency -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <!-- Import custom CSS -->
    <link rel="stylesheet" href="assets/styles/main.css">
</head>

<body>

    <!-- Navigation bar -->
    <?php include '../includes/navigation-bar.php';?>

    <!-- Hero / header section -->
    <header>
        <div class="container header__container">
            <!--  Imagenes del Producto -->
            <div class="row product-info">
                <div class="col-md-7 mb-3 product-images">
                    <div id="slider" class="slider">
                        <div class="wrapper">
                            <div id="items" class="items">
                            <!-- Obtener imagenes del producto -->
                            <?php
                               $userid = $product['product_id'];
                               $product_images = getProductImages($userid);
                               if($product_images){
                                   foreach($product_images as $image){
                                     echo '
                                     <span class="slide">
                                     <img src="/uploads/images/'.$image['name'].'" alt="'.$product['product_name'].' image">
                                     </span>
                                     ';
                                   }
                               }
                            ?>
                            </div>
                        </div>
                        <a id="prev" class="control prev"></a>
                        <a id="next" class="control next"></a>
                    </div>
                </div>
                <!-- Información del producto -->
                <div class="col-md-5 mb-3 product-details center">
                    <div class="product_name">
                        <h2 class="product-name"><?php echo $product['product_name']?></h2> 
                    </div>
                    <div class="product_description">
                        <?php echo $product['product_description']?>
                    </div> 
                    <div class="sizes info-sizes <?php echo $product['product_id'] ?> ">
                    <span style="font-family:Averta;color:#332D2D;size:12px;">Tallas disponibles: </span>
                    <?php
                    $tallas = json_decode($product['tallas']);
                    foreach($tallas as $talla => $disponible){
                        if($disponible>0){
                            echo '<span class="talla-circle unselected" data-product-id="'.$product['product_id'].'" data-product-size="'.$talla.'">'.$talla.'</span>';
                        }

                    }
                    ?>
                    </div>                 
                </div>
            </div>  
            <div class="header__more">
                <a href="#main" class="btn btn-icon-primary">
                    <i class="fa fa-plus"></i>
                </a>
                <small class="mx-2 header__explore__text">Ver todo</small>
            </div>
        </div>

    </header>

    <!-- List of shoes -->
    <main id="main">
        <section class="collection">
            <div class="container">
                <h2 class="collection__title">
                    <span class="font-bold title__line__left">Colección </span> <span
                        class="font-bold text-primary">Destacada</span>
                </h2>

                <div class="row mt-4">

                    <!-- display all the shoes from the database here -->
                    <?php
                    
                        // get all available shoes and their information from the database
                        $get_products = $database->getRows("SELECT * FROM products ORDER BY `product_id` ASC");
                        
                        // Loop through the data and display them one by one with a foreloop 
                        for ($i=0; $i <count($get_products) ; $i++) { 
                            if($i==4){
                                break;
                            }

                            // check if shoe is in cart
                            $style='';
                            $quitar='none';
                            $add='inline';
                            if (isset($_SESSION['userId'])) {
                                 $check_cart = $database->getRow("SELECT * FROM john_cart WHERE product_id = ? AND user_id = ?",[$get_products[$i]['product_id'],$_SESSION['userId']]);
                                if ($check_cart) {
                                    $style = 'block';
                                    $quitar='block';
                                    $add='none';
                                }else{
                                    $style = 'none';
                                }
                            }else{
                                $style = 'none';
                            }
                          
                           echo'
                                <div class="col-md-3 mb-4 d-flex">
                                    <div class="card shoe__card" data-product-id="'.$get_products[$i]['product_id'].'">
                                        <img class="shoe__image" src="/uploads/images/thumbs/'.$get_products[$i]['product_image_1'].'" alt="">
                                        <div class="shoe__card__overlay">
                                            <div class="added">
                                                <small class="p-2 text-primary font-bold text-center added__to__cart added-for-'.$get_products[$i]['product_id'].'" style="display:'.$style.';">✔️ Agregado al carrito</small>
                                                <small class="p-2 text-primary font-bold text-center rm-cart remove__from__cart-'.$get_products[$i]['product_id'].'" style="display:'.$quitar.';" data-product-id="'.$get_products[$i]['product_id'].'">X Quitar</small>
                                            </div>
                                            <span class="overlay__icons" style="margin-left:55px">
                                                <button class="btn btn-icon btn-card-icon" type="button" data-product-id="'.$get_products[$i]['product_id'].'" data-product-name="'.$get_products[$i]['product_name'].'" data-product-image="'.$get_products[$i]['product_image_1'].'">
                                                    <img width="80%" src="https://cdn-icons-png.flaticon.com/512/134/134937.png"
                                                        alt="" style="float:left;margin-right:12px" ><span style="margin-top:10px">Comprar</span>
                                                </button>
                                            </span>
                                            <span class="overlay__info">
                                                <h6 class="overlay__title">'.$get_products[$i]['product_name'].'</h6>
                                                <h6 class="overlay__price">$ <span class="shoe__card__item__price">'.number_format($get_products[$i]['product_price'],0,',','.').'</span>
                                                </h6>
                                                <div class="tallas '.$get_products[$i]['product_id'].'" >
                                                <span style="font-family:Averta;color:#332D2D;size:12px;">Tallas: </span>';
                                                $tallas = json_decode($get_products[$i]['tallas']);
                                            foreach($tallas as $talla => $disponible){
                                               if($disponible>0){
                                                   echo '<span class="talla-circle unselected" data-product-id="'.$get_products[$i]['product_id'].'" data-product-size="'.$talla.'">'.$talla.'</span>';
                                                }

                                            }
                                  echo  '       </div>
                                                </div>
                            
                                            </span>
                                    </div>
                                </div>
                           ';
                        }
                    ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Include the login and register modals -->
    <?php 
        include '../includes/login-modal.php';
        include '../includes/register-modal.php';
        include '../includes/whatsapp-modal.php';
        include '../includes/scripts.php';
    ?>
    <script src="assets/javascripts/article.js"></script>
</body>

</html>
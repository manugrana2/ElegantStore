<?php
    $firstname = ''; // Initialize firstname variable
    $loggedin = false;
    $cart_active = '';
    if (isset($_SESSION['userId'])) {
        $loggedin=true;
        $firstname = $_SESSION['firstname']; // pass the user firstname fromt he database to the firstname variable
    }

   if ($loggedin ==true) {
        $sub_total=-0.6;
        $hidden = 'hidden';
       // Check if Cart is empty
        $check_cart = $database->getRows("SELECT * FROM john_cart WHERE `user_id` = ?",[$_SESSION['userId']]);
        if ($check_cart) {
            $cart_active = 'active';
            $hidden = '';
            // for every cart item, use the cart item ID to get the shoe details from the sheos table
            for ($i=0; $i <count($check_cart) ; $i++) { 
                $get_shoes = $database->getRows('SELECT * FROM products WHERE `product_id`=?',[$check_cart[$i]['product_id']]);
                if ($get_shoes) {
                    // echo json_encode($get_shoes);
                    for ($x=0; $x <count($get_shoes) ; $x++) { 
                        // Add to subtotal
                        $sub_total = $sub_total+$get_shoes[$x]['product_price']*$check_cart[$i]['product_quantity'];
                    }
                }
            }
        }else{
            $sub_total=0.0;
        }
        
   }

?>
<nav class="">
    <div class="container nav__container">
        <a class="navbar-brand" href="./">
            <h1 class="logo__text">
                <span class="font-black h1 logo__text">MajoShop</span><span
                    class="text-primary font-black h1 logo__text">.net</span>
            </h1>
        </a>
        <?php
            setlocale(LC_MONETARY, 'es_CO');
            if ($loggedin==true) {
                echo 
                    '<small class="nav__username dropdown-toggle" data-toggle="dropdown" >
                        Hola, <span>'.$firstname.'</span>
                    </small>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="./api/logout.php">Cerrar Sesión</a>
                    </div>
                    <span class="nav__cart '.$cart_active.'">
                        <a href="./cart.php" class="btn btn-icon">
                            <span style="float:left;margin-top:-18px;margin-left:-15px" class="cart-subtotal '.$hidden.'">$'.number_format($sub_total, 0, ',', '.').'</span><img style="float:left;position:absolute;width:40px" class="cart__icon" src="https://cdn-icons-png.flaticon.com/512/263/263142.png" alt="">
                        </a>
                    </span>';
            }else{
                echo '
                   <div class="btn-group ml-auto" role="group" aria-label="Basic example">
                   <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#login__modal">Iniciar Sesión</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#register__modal">Registrarse</button>-->
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                             data-target="#login__modal">Iniciar Sesión</button>
                            
                    </div>
                ';
            }
        ?>
    </div>
</nav>
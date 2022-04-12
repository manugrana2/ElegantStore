<?php
   include '../server/databaseClass.php';
   $database = new databaseClass();
   header( 'Content-Type: text/json' );
   session_start();
   $hasCart=false;
   $sub_total=-0.6;
   if (isset($_SESSION['userId'])) {
       // Check if Cart is empty
        $check_cart = $database->getRows("SELECT * FROM john_cart WHERE `user_id` = ?",[$_SESSION['userId']]);
        if ($check_cart) {
            $hasCart=True;
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

   $data['result']=$hasCart;
   $data['value']="$".number_format($sub_total, 0, ',', '.');
   echo(json_encode($data))

?>
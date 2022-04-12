<?php
    include '../server/databaseClass.php';
    $database = new databaseClass();
    
    session_start();
    //collect the item data that was clicked
    $cart_id = trim($_POST['cart_id']);
    $user_id = $_SESSION['userId'];

    // check if shoe is already in cart for user
    $get_shoe = $database->deleteRow("DELETE FROM john_cart WHERE `user_id` = ? AND `cart_id` = ?",[$user_id,$cart_id]);

    if ($get_shoe) {
        $get_cart = $database->getRows("SELECT * FROM john_cart WHERE `user_id` = ?",[$_SESSION['userId']]);
        $grand_total = 0;

        if ($get_cart) {
            for ($i=0; $i <count($get_cart) ; $i++) { 
                
                // for total subprice get price of each shoe
                $get_shoe_price = $database->getRow("SELECT * FROM products WHERE product_id =?",[$get_cart[$i]['product_id']]);

                // calculate item sub total
                $item_total = $get_cart[$i]['product_quantity'] * $get_shoe_price['product_price'];
                $grand_total = $grand_total + $item_total;
            }
        }

        $data['sub_total'] = number_format($grand_total,2,'.',',');
        $data['grand_total'] = number_format($grand_total,2,'.',',');
        $data['status']=200;
        $data['message']='Item removed cart';
        echo json_encode($data);
    }else{
        header('location:../');
    }
?>
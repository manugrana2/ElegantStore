<?php
    session_start();

    include '../server/databaseClass.php';
    $database = new databaseClass();

    $product_id = $_POST['product_id'];
    $cart_id = $_POST['cart_id'];
    $item_quantity = $_POST['item_quantity'];

    // get the shoe price
    $get_shoe_price = $database->getRow("SELECT product_price FROM products WHERE product_id = ?",[$product_id]);
    
    // update cart quantity
    $update_cart = $database->updateRow("UPDATE john_cart SET product_quantity = ? WHERE cart_id = ?",[$item_quantity,$cart_id]);
    // get the updated quantity from the database
    $get_updated_quantity = $database->getRow("SELECT product_quantity FROM john_cart WHERE cart_id = ?",[$cart_id]);

    // Multiply the quanity by the price 
    $new_total = number_format($get_updated_quantity['product_quantity'] * $get_shoe_price['product_price'], 2, '.', ',');
    $data['new_total'] = $new_total;

    // Now lets's get grand total price
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
    echo json_encode($data);
?>
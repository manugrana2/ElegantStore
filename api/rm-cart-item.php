<?php
    include '../server/databaseClass.php';
    $database = new databaseClass();
    
    session_start();
    //collect the item data that was clicked
    $product_id = trim($_POST['product_id']);
    $user_id = $_SESSION['userId'];

    // check if shoe is already in cart for user
    $get_shoe = $database->deleteRow("DELETE FROM john_cart WHERE `user_id` = ? AND `product_id` = ?",[$user_id,$product_id]);

    if ($get_shoe) {
        $data['status']=200;
        $data['message']='Item removed cart';
        echo json_encode($data);
    }else{
        header('location:../');
    }
?>
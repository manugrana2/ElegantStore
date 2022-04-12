<?php
    include '../server/databaseClass.php';
    $database = new databaseClass();
    
    session_start();
    $data=[];
    $data['status']=500;
    $data['message']=[];
    if(isset($_SESSION['userId'])){
        $user_id = $_SESSION['userId'];
        //collect the item data that was clicked
        $tallas = json_decode($_POST['tallas']);
        $product_id = filter_var( $_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
        // check if shoe is already in cart for user
        if(property_exists($tallas,$product_id)){
            foreach($tallas->$product_id as $talla){
                $talla =  filter_var( $talla, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
                $get_shoe = $database->getRow("SELECT * FROM john_cart WHERE `user_id` = ? AND `product_id` = ? AND `talla` = ?",[$user_id,$product_id,$talla]);
                if ($get_shoe) {
                    $data['status']=400;
                    $data['message']='Item already added to cart';
                    echo json_encode($data);
                }else{
                    $add_shoe_to_cart = $database->insertRow("INSERT INTO john_cart (`user_id`,`product_id`, `talla`, `product_quantity`) VALUE(?,?,?,1)",[$user_id,$product_id,$talla]);
                    if ($add_shoe_to_cart) {
                        $data['status']=200;
                        $data['message'][]='Item added to cart';
                    }else {
                        $data['status']=500;
                        $data['message']='Could not add item to cart';
                    }
                }
            }
        }    
    }
    echo json_encode($data);
?>
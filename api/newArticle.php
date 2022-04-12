<?php
//var_dump($_FILES['images']);
?>

<?php
    include '../server/databaseClass.php';
    $database = new databaseClass();
    
    session_start();
    require_once '../includes/functions.php';
$target_dir = "../uploads/images/";
$uploadOk = 1;
$article = [];
$article['uploadErrors'] = [];
$article['uploaded'] = [];
$article['errors'] = [];
$article['sizes'] = [];
$article['host'] = 'http://localhost/';
$error = $article['uploadErrors'];
$images = [];
// Check if user is logged
if(isset($_SESSION['userId'])){
    $user_id = $_SESSION['userId'];
    
    // Check if image file is a actual image or fake image
    if(isset($_FILES["images"])) {
    for($i=0; $i < count($_FILES["images"]["name"]); $i++){
            #var_dump($_FILES["images"]);
            $target_file = $target_dir . basename($_FILES["images"]["name"][$i]);
            $check = getimagesize($_FILES["images"]["tmp_name"][$i]);
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            if($check !== false) {
                $uploadOk = 1;
            } else {
                $error[] = "File is not".$_FILES["images"]["name"][$i]." an image.";
                $uploadOk = 0;
            }


            // Check if file already exists
            if (file_exists($target_file)) {
            $images[][$i] =  basename($_FILES["images"]["name"][$i]);
            $article['uploaded'][] = basename($_FILES["images"]["name"][$i]);
            $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["images"]["size"][$i] > 8000000) {
            $error[][$i] = "Sorry, your file ".$_FILES["images"]["name"][$i]." is too large.";
            $uploadOk = 0;
            }

            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" && $imageFileType != "webp" ) {
            $error[][$i] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed in ".$_FILES["images"]["name"][$i];
            $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 1) {
                // if everything is ok, try to upload file
                if (move_uploaded_file($_FILES["images"]["tmp_name"][$i], $target_file)) {
                    $images[] =  basename($_FILES["images"]["name"][$i]);
                    $article['uploaded'][] = basename($_FILES["images"]["name"][$i]);
                    list($origWidth, $origHeight, $type) = getimagesize($target_file);
                    $width_ratio=500/$origWidth;
                    $height_ratio=800/$origHeight;
                    $width_ratio >= 1?  $new_width=480-(500%$origWidth): $new_width=500;
                    $height_ratio >= 1?  $new_height=780-(800%$origHeight): $new_height=800;
                    $resized = resizeImage($target_file, $target_dir.'thumbs/'.basename($_FILES["images"]["name"][$i]), $new_width,$new_height);
                    if(!file_exists($target_dir.'thumbs/'.basename($_FILES["images"]["name"][$i]))){
                        copy($target_file, $target_dir.'thumbs/'.basename($_FILES["images"]["name"][$i]));
                    }
                    $article['thumbs'][]=$target_dir.'thumbs/'.basename($_FILES["images"]["name"][$i]);
                } else {
                    $error[][$i] =  "Sorry, there was an error uploading your file ".basename($_FILES["images"]["name"][$i]);
                }
            } 
        }
    }
    if(isset($_POST["product_name"]) and $_POST["product_name"]!='' and isset($_POST["product_details"]) and $_POST["product_details"]!='' and isset($_POST["product_category"]) and $_POST["product_category"]!='' and isset($_POST["product_price"]) and $_POST["product_price"]!=''){
        $nroImgs = count($article['uploaded']);
        if($nroImgs>0){
        if(isset($_POST["sizes"]) and json_decode($_POST["sizes"])){
            $tallas =  json_decode($_POST["sizes"]);
            foreach($tallas as $talla => $disponible){
                if(is_numeric( $disponible)){
                    $article['sizes'][strip_tags((string)$talla)] = (int)$disponible;
                }
            }
            if(count($article['sizes'])>0){
                $name= filter_var($_POST["product_name"], FILTER_SANITIZE_SPECIAL_CHARS);
                $descripcion = filter_var($_POST["product_details"], FILTER_SANITIZE_SPECIAL_CHARS);
                $category = filter_var($_POST["product_category"], FILTER_SANITIZE_SPECIAL_CHARS);
                $tallas = json_encode($article['sizes']);
                $product_image_1 = $article['uploaded'][0];
                $price = filter_var($_POST["product_price"], FILTER_SANITIZE_SPECIAL_CHARS);;
                //Check if a article with the same name already exist
                $get_article = $database->getRow("SELECT * FROM products WHERE `product_name` = ?",[$name]);
                $permalink = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name)));
                $permalink = strtolower(trim(preg_replace('/[-]+/', '-', $permalink)));
                if($get_article){
                    $article['errors'][]='Article exist';
                }else{
                    $add_article = $database->insertRow("INSERT INTO products (`user_id`,`product_name`, `product_description`, `product_price`, `product_image_1`, `category`, `tallas`,`permalink`) VALUE(?,?,?,?,?,?,?,?)",[$user_id,$name,$descripcion,$price,$product_image_1,$category,$tallas,$permalink]);
                    if ($add_article) {
                        $userid =  $database->getRow("SELECT * FROM products WHERE `permalink` = ?",[$permalink]);
                        $userid = $userid['product_id'];
                        //añadir permalink del artículo
                        #Verificar que otro articulo no esté ocupando ese permalink
                        $permalink_info = $database->getRows("SELECT * FROM permalinks WHERE `permalink` = ? AND  `user` = ?",[$permalink,'products']);
                        
                        if($permalink_info){
                            $permalinks = count($permalink_info);    
                            if(count($permalinks)>0){
                                $n = $permalinks+1;
                                $permalink = $permalink.$n;
                            }
                        }
                        $permalink=$permalink.'-pid'.$userid;
                        $add_permalink = $database->insertRow("INSERT INTO permalinks (`user`,`permalink`, `userid`) VALUE(?,?,?)",['products',$permalink,$userid]);
                        $article['product_name'] = $name;
                        $article['product_description'] = $descripcion;
                        $article['product_category'] = $category;
                        $article['status']=200;
                        $article['permalink']= $permalink;
                        //añadir imagenes al producto
                        if($nroImgs>1 and is_numeric($userid)){
                            $product_has_img = $database->getRow("SELECT * FROM media WHERE `user` = ? AND `name` = ? AND `userid` = ?",['products',$name,$userid]);
                            if(!$product_has_img ){
                                for($i=0; $i < $nroImgs;$i++){
                                    $name = $article['uploaded'][$i];
                                    $add_product_imgs = $database->insertRow("INSERT INTO media (`user`,`name`, `userid`, `type`) VALUE(?,?,?,?)",['products',$name,$userid,'image']);

                                }
                            }
                        }
                    }else {
                        $article['status']=500;
                    }
                }
                
            }else{
                $article['errors'][]='Añada por lo menos una talla';
            }

        }
        }else{
            $article['errors'][]='Añada por lo menos una imagen válida';
        }
    
    }else{
        $article['errors'][] = 'Completa todos los campos';
    }
}else{
    $article['errors'][]='Logged Out';
}
echo json_encode($article);
?>
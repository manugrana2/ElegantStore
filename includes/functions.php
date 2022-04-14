<?php
session_start();
require_once '../server/settings.php';
include '../server/databaseClass.php';
$database = new databaseClass();
define('db', $database);


function isLogged(){
    if (isset($_SESSION['userId'])) {
        // If user is logged in send true
        return true;
    }else{
        // If user is logged out send false
        return false;
    }
}

/**
 * Resize image - preserve ratio of width and height.
 * @param string $sourceImage path to source JPEG/PNG image
 * @param string $targetImage path to final JPEG/PNG image file
 * @param int $maxWidth maximum width of final image (value 0 - width is optional)
 * @param int $maxHeight maximum height of final image (value 0 - height is optional)
 * @param int $quality quality of final image (0-100)
 * @return bool
 */
function resizeImage($sourceImage, $targetImage, $maxWidth, $maxHeight, $quality = 100)
{
    $isValid = @getimagesize($sourceImage);

    if (!$isValid)
    {
        return false;
    }

    // Get dimensions and type of source image.
    list($origWidth, $origHeight, $type) = getimagesize($sourceImage);

    if ($maxWidth == 0)
    {
        $maxWidth  = $origWidth;
    }

    if ($maxHeight == 0)
    {
        $maxHeight = $origHeight;
    }

    // Calculate ratio of desired maximum sizes and original sizes.
    $widthRatio = $maxWidth / $origWidth;
    $heightRatio = $maxHeight / $origHeight;

    // Ratio used for calculating new image dimensions.
    $ratio = min($widthRatio, $heightRatio);

    // Calculate new image dimensions.
    $newWidth  = (int)$origWidth  * $ratio;
    $newHeight = (int)$origHeight * $ratio;

    // Create final image with new dimensions.
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Obtain image from given source file.
    switch(strtolower(image_type_to_mime_type($type)))
    {
        case 'image/jpeg' or 'image/jpg':                      
            $image = @imagecreatefromjpeg($sourceImage);            
            if (!$image)
            {
                return false;
            }

            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight); 

            if(imagejpeg($newImage,$targetImage,$quality))
            {
                // Free up the memory.
                imagedestroy($image);
                imagedestroy($newImage);
                return true;
            }            
        break;
        
        case 'image/png':
            $image = @imagecreatefrompng($sourceImage);

            if (!$image)
            {
                return false;
            }

            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            if(imagepng($newImage,$targetImage, floor($quality / 10)))
            {
                // Free up the memory.
                imagedestroy($image);
                imagedestroy($newImage);
                return true;
            }
        break;
        case 'image/webp':
            $image = @imagecreatefromwebp($sourceImage);

            if (!$image)
            {   
                return false;
            }

            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            if(imagepng($newImage,$targetImage, floor($quality / 10)))
            {
                // Free up the memory.
                imagedestroy($image);
                imagedestroy($newImage);
                return true;
            }
        break;
                
		default:
			return false;
       }
}

function getProductPermalink($id){
    $permalink = db->getRow("SELECT * FROM permalinks WHERE `userid` = ? AND  `user` = ?",[$id,'products']);
    if($permalink){
        return($permalink['permalink']);
    }else{
        return false;
    }
}

function getProductImages($id){
    $product_has_img = db->getRows("SELECT * FROM media WHERE `user` = ? AND `userid` = ?",['products',$id]);
    if($product_has_img){
        return $product_has_img;
    }else{
        return false;
    }
}

function getProduct($id){
    $product =  db->getRow("SELECT * FROM products WHERE `product_id` = ?",[$id]);
    if($product){
        $permalink = getProductPermalink($id);
        if($permalink){
            $product['permalink'] = $permalink; 
        }
        return $product;
    }else{
        return false;
    }

}

function getArticle($product_id=false){
    if($product_id!=false){
        $product =  db->getRow("SELECT * FROM products WHERE `product_id` = ?",[$product_id]);
        if($product){
            return $product;
        }else{
            return false;
        }
    }
}
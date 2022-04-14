<?php
require_once '../includes/functions.php';
require_once '../server/settings.php';

function filterArticleAttr($article){
    // Remove non needed attributes
    
    $article = array_filter($article, function($v, $k) {
    return $k == 'active' and ( $v == 0 or $v ==1 );
    }, ARRAY_FILTER_USE_BOTH);
    return $article;

}
$result = [];
$result['status']=500;
if(isLogged()){
    if(isset($_GET['id'])){
        $id = (int)$_GET['id'];

        if(is_integer($id)){
            if(getArticle($id)){
                if(isset($_GET['editArticle']) and json_decode($_GET['editArticle'])){
                    $article = (array)json_decode($_GET['editArticle']);
                    $article =filterArticleAttr($article);
                    if($article){
                        // For each filtered article attribute
                        //product_quantity = ?
                        $attributes = '';
                        $values = [];
                        $i=0;
                        foreach($article as $attr => $value){
                            if($i==0){
                                $attributes .= "$attr = ?";
                            }else{
                                $attributes .= ", $attr = ?";
                            }
                            $values[] = $value;
                            $i++;

                        }
                        $values[] = $id;
                        $update_article = $database->updateRow("UPDATE products SET $attributes WHERE product_id = ?", $values);
                        if($update_article){
                            $result['status']=200;
                        }else{
                            $result['status']=500;
                        }

                    }
                    
                }
            }
        }
    }elseif(isset($_GET['del'])){
        $id = (int)$_GET['del'];

        if(is_integer($id)){
            $delete_article = $database->deleteRow("DELETE FROM products WHERE product_id = ?",[$id]);
            if($delete_article){
                $result['deleted']= true;
                $result['status']=200;
            }
            else{
                $result['deleted']= false;
            }
        }
    }
}

echo (json_encode($result));
<?php
//Incluimos la conexión a la base de datos.
include '../server/databaseClass.php';
$database = new databaseClass();

session_start();
require_once '../includes/functions.php';
require_once '../server/settings.php';
 
//$registros nos entrega la cantidad de registros a mostrar.
$registros = 10;
 
//$contador como su nombre lo indica el contador.
$contador = 1;
 
$articles = [];

/**
 * Se inicia la paginación, si el valor de $pagina es 0 le asigna el valor 1 e $inicio entra con valor 0.
 * si no es la pagina 1 entonces $inicio sera igual al numero de pagina menos 1 multiplicado por la cantidad de registro
 */
if (!isset($_GET['page'])) {
    $inicio = 0;
    $pagina = 1;
} else {
    $pagina = $_GET['page'];
    $inicio = ($_GET['page'] - 1) * $registros;
}
?>
            <?php
            /**
             * Se inicia la consulta donde seleccionamos todos los campos de la tabla personas, pero donde isActive = 1, esto lo hacemos para el manejo
             * de registros si estan activos o no.
             */
            $resultados = $database->getRows("SELECT * FROM products WHERE 	active = 1 ");
 
            //Contamos la cantidad de filas entregadas por la consulta, de esta forma sabemos cuantos registros fueron retornados por la consulta.
            $total_registros = count($resultados);
 
            //Generamos otra consulta la cual creara en si la paginacion, ordenando y crendo un limite en las consultas.
            $resultados = $database->getRows("SELECT * FROM products WHERE active = '1' ORDER BY `products`.`date` ASC LIMIT $inicio, $registros");
 
            //Con ceil redondearemos el resultado total de las paginas 4.53213 = 5
            $total_paginas = ceil($total_registros / $registros);
 
            // Si tenemos un retorno en la varibale $total_registro iniciamos el ciclo para mostrar los datos.
            if ($total_registros) {
                $articles['total'] = $total_registros;
                $articles['page'] = (int)$pagina;
                $articles['start'] = $inicio;
                $articles['limit'] = $registros;

                foreach($resultados as $product) {
                    $articles['products'][$contador]['name']= $product["product_name"];
                    $articles['products'][$contador]['id']= $product["product_id"];
                    $articles['products'][$contador]['active'] = $product["active"]==1?'Si':'No';
                    $articles['products'][$contador]['permalink']=$host.$product["permalink"];

                ?>           
                    <?php
                    /**
                     * La variable $contador es la misma que iniciamos arriba con valor 1, en cada ciclo sumara 1 a este valor.
                     * $contador sirve para mostrar cuantos registros tenemos, es mas que nada una guia.
                     */
                   $contador++;
                }
             } else {
                $articles['total']=0;
            }
echo(json_encode($articles));
            ?>
     
  
<?php

    include '../server/databaseClass.php';
    $database = new databaseClass();

    // collect the login form request
    // $firstname = trim($_POST['firstname']);
    // $lastname = trim($_POST['lastname']);
    $email = htmlentities($_POST['nombre_apellido']);
    $password1 = trim($_POST['whatsapp']);
    $password2 = trim($_POST['whatsapp2']);
    $password = md5($password1);
    $user_exists = false;

    // check if user exists
    $check_user_existence = $database->getRow("SELECT * FROM whatsapp WHERE nombre_apellido=? AND `whatsapp` = ?",[$email,$password]);
    if ($check_user_existence) {
        $user_exists = true;
    }else{
        if($password2==$password1){
            $register_user = $database->insertRow("INSERT INTO whatsapp (nombre_apellido, whatsapp) VALUE(?,?)",[$email,$password]);
            if($register_user==true):
                $check_user_existence = $database->getRow("SELECT * FROM whatsapp WHERE nombre_apellido=? AND `whatsapp` = ?",[$email,$password]);
                $user_exists = true;
            endif;
        }else{
            $user_exists = false;  
        }
    }

    // If the user exists login the user
    if ($user_exists) {
        session_start(); //start a session
        $_SESSION['userId'] = $check_user_existence['user_id']; //store the user's id in the session using the $_SESSION variable
        $_SESSION['firstname'] = $check_user_existence['nombre_apellido']; //store the user's id in the session using the $_SESSION variable
        $data['status']=200; //create a success status of 200
        $data['message']="User found"; //create a success status message
        $data['userId']=$check_user_existence['user_id']; //create a success status message
        $data['firstname']=$check_user_existence['nombre_apellido']; //create a success status message
        $data['whatsapp_guest']=true;
        echo json_encode($data); //send the results back to the user
    }else{
        $data['status']=400;
        $data['message']="Error";
        echo json_encode($data);
    }

?>
<?php
    require 'db.php';

    if ($_SERVER['REQUEST_METHOD']==='POST') {
       $email=$_POST['email'];
       $password=$_POST['password'];
       

       $query = "SELECT id, password, 'company' AS type FROM companies WHERE email = ?
            UNION
            SELECT id, password, 'passenger' AS type FROM passengers WHERE email = ?";

        $stablish=$conn->prepare($query);
        $stablish->bind_param('ss',$email,$email);
        $stablish->execute();
        
        $result=$stablish->get_result();

        if($result->num_rows >0){
            $user=$result->fetch_assoc();
            if (password_verify($password,$user['password'])) {
               session_start();
               $_SESSION['user_id']=$user['id'];
               $_SESSION['user_type']=$user['type'];
               header("Location: ../views/home.html");
            }
            else {
                echo "Invalid password.";
            }
        }else {
            echo "invalid email.";
        }
    }

?>
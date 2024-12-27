<?php
        require 'db.php';

        if($_SERVER['REQUEST_METHOD']==='POST'){
            $name=$_POST['name'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password=password_hash($_POST['password'],PASSWORD_BCRYPT);
            $tel=$_POST['tel'];
            $type = $_POST['type'];
            $photo = $_FILES['photo'];
            $target_dir="../uploads/";
            $photo_name=basename($photo["name"]);
            $target_file =$target_dir . $photo_name;

            if(move_uploaded_file($photo["tmp_name"],$target_file)){
                if($type==='company'){
                    $table='companies';
                }else{
                    $table='passengers';
                }
                $query = "INSERT INTO $table (name, email, password, tel, photo, username) VALUES (?, ?, ?, ?, ?, ?)";

                $stablish= $conn->prepare($query);
                $stablish->bind_param('ssssss', $name, $email, $password, $tel, $photo_name, $username);

                
                if ($stablish->execute()) {
                   header("Location: ../views/login.html?success=1");
                } else {
                    echo "error: " . $conn->error;
                }
                

            }else{
                echo "error uploading photo.";
            }
        }


?>
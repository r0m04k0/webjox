<?php

    session_start();
    include("../connect.php");

    if (!isset($_SESSION['hashcode']) && isset($_COOKIE['hashcode']) && !isset($_SESSION['id']) && isset($_COOKIE['id'])) {
        $_SESSION['hashcode'] = $_COOKIE['hashcode'];
        $_SESSION['id'] = $_COOKIE['id'];
    }

    if (isset($_SESSION['hashcode']) && isset($_SESSION['id'])) {
        $stmt = $connect->prepare("SELECT `hashcode`, `login`, `name`, `roles`.`role`, `users`.`id` AS `id` FROM users, roles WHERE `users`.`id` = ? AND `roles`.`id` = `users`.`role`;");
        $stmt->bind_param("i", $_SESSION['id']);
        $stmt->execute();
        $result = mysqli_fetch_array($stmt->get_result());

        if ($result['hashcode'] != $_SESSION['hashcode']) {
            header('Location: auth.php');
            exit();
        }

        $_SESSION['role'] = $result['role'];
        $_SESSION['login'] = $result['login'];
        $_SESSION['name'] = $result['name'];
        $user_id = $result['id'];

    }
    else {
        header('Location: logout.php');
        exit();
    }


    if (isset($_POST['submit']) && isset($_SESSION['role'])) {

        $title = $_POST["title"];
        $caption = $_POST["caption"];
        $text = $_POST["text"];
        $id = $_POST["id"];
        $imagepath = $_POST['imagepath'];
     
        $target_dir = "../src/images/";
        $filename = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;   
        if (!file_exists($_FILES["image"]["tmp_name"])) {
            $imageNotReplaced = true;
        } 
        else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                
                $stmt = $connect->prepare("DELETE FROM `images` WHERE `path` = ?;" );
                $stmt->bind_param("s", $imagepath); 
                $stmt->execute();
                // chmod("../$imagepath", 777);
                // unlink("../$imagepath");

                $imagepath = 'src/images/'.$filename;

                $stmt = $connect->prepare("INSERT INTO `images` (`path`) VALUES (?);" );
                $stmt->bind_param("s", $imagepath); 
                $stmt->execute();
                $image = $stmt->insert_id;
            }
        }
    
        if ($_POST['category'] == 'new') {
            $category = $_POST["new"];

            $stmt = $connect->prepare("INSERT INTO `categories` (`category`) VALUES (?);" );
            $stmt->bind_param("s", $category); 
            $stmt->execute();

            $category = $stmt->insert_id;
        }
        else {
            $category = (int)$_POST["category"];
        }

        if ($imageNotReplaced) {
            $stmt = $connect->prepare("UPDATE posts SET 
            `title` = ?, `caption` = ?, `text` = ?, `category` = ?, updated = NOW()
            WHERE id = ? ;");
            $stmt->bind_param("sssii", $title, $caption, $text, $category, $id); 
            $stmt->execute();
        }
        else {
            $stmt = $connect->prepare("UPDATE posts SET 
            `title` = ?, `caption` = ?, `text` = ?, `image` = ?, `category` = ?, updated = NOW()
            WHERE id = ? ;");
            $stmt->bind_param("sssiii", $title, $caption, $text, $image, $category, $id); 
            $stmt->execute();
        }

        echo "<script>
                    alert('Пост успешно изменён.');
                    document.location.href = 'posts.php';
            </script>";
        echo $stmt->affected_rows;

    }
    else {
        header('Location: posts.php');
    }

?>
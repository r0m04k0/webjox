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


    if (isset($_POST['submit']) && $_SESSION['role'] == 'admin') {


        $title = $_POST["title"];
        $caption = $_POST["caption"];
        $text = $_POST["text"];
        $status = ($_POST["publish"] == 'true' ? 2 : 1);

        $target_dir = "../src/images/";
        $filename = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;        
        if (!file_exists($_FILES["image"]["tmp_name"])) {
            echo "<script>
                    alert('Произошла ошибка при загрузке изображения.');
                    document.location.href = 'create.php';
            </script>";
        } 
        else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $imagepath = 'src/images/'.$filename;
            }
        }
        
        if (isset($_POST['new'])) {
            $category = $_POST["new"];

            $stmt = $connect->prepare("INSERT INTO `categories` (`category`) VALUES (?);" );
            $stmt->bind_param("s", $category); 
            $stmt->execute();

            $category = $stmt->insert_id;
        }
        else {
            $category = $_POST["category"];
        }

        $stmt = $connect->prepare("INSERT INTO `images` (`path`) VALUES (?);" );
        $stmt->bind_param("s", $imagepath); 
        $stmt->execute();
        $image = $stmt->insert_id;

        $stmt = $connect->prepare("INSERT INTO posts (`title`, `caption`, `text`, `author_id`, `image`, `status`, `category`) 
        VALUES (?, ?, ?, ?, ?, ?, ?); ");

        $stmt->bind_param("sssiiii", $title, $caption, $text, $user_id, $image, $status, $category); 
        $stmt->execute();

        echo "<script>
                    alert('Пост успешно добавлен.');
                    document.location.href = 'posts.php';
            </script>";

    }

?>
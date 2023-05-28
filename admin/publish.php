<?php

    session_start();
    include("../connect.php");

    if (!isset($_SESSION['hashcode']) && isset($_COOKIE['hashcode']) && !isset($_SESSION['id']) && isset($_COOKIE['id'])) {
        $_SESSION['hashcode'] = $_COOKIE['hashcode'];
        $_SESSION['id'] = $_COOKIE['id'];
    }

    if (isset($_SESSION['hashcode']) && isset($_SESSION['id'])) {
        $stmt = $connect->prepare("SELECT `hashcode`, `login`, `name`, `roles`.`role` FROM users, roles WHERE `users`.`id` = ? AND `roles`.`id` = `users`.`role`;");
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

        if ($_SESSION['role'] != 'admin') {
            header('Location: posts.php');
            exit();
        }

    }
    
    else {
        header('Location: logout.php');
        exit();
    }

    if (empty($_GET['id'])) {
        header('Location: posts.php');   
    }

    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'unpublish') {
        $query = "UPDATE `posts` SET `status` = 1 WHERE `id` = ? ;";
    }
    else {
        $query = "UPDATE `posts` SET `status` = 2 WHERE `id` = ? ;";

    }

    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header('Location: posts.php');
                    
?>

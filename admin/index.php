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

    }
    else {
        header('Location: logout.php');
        exit();
    }


    include("./elements/head.php");
?>



<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center justify-content-lg-between" id="navbarNavAltMarkup">
            <div class="navbar-nav">
            
                <a class='nav-link active' aria-current='page' href='index.php'>Главная</a>
                <a class='nav-link' aria-current='page' href='posts.php'>Посты</a>
                <? echo $_SESSION['role'] == 'admin' ? "<a class='nav-link' aria-current='page' href='create.php'>Добавить пост</a>" : '' ?>
                <a class='nav-link' href='logout.php'>Выйти</a>
            
            </div>
            </div>
        </div>
    </nav>
        
</header>
    
<main class="m-3 text-center">

    <div class="mt-5">

        <?php
        
        echo "<h5>Добро пожаловать в систему, ". $_SESSION['name']."</h5>";
        echo "<h5>Ваша роль в системе: ".$_SESSION['role']." </h5>";
        echo "<h5>Ваш логин: ".$_SESSION['login']." </h5>";

        ?>

    </div>


</main>    


</body>
</html>

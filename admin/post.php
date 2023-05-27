<?php

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

    if (empty($_GET['id'])) {
        header('Location: posts.php');   
    }
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
            
                <a class='nav-link' aria-current='page' href='index.php'>Главная</a>
                <a class='nav-link' aria-current='page' href='posts.php'>Посты</a>
                <? echo $_SESSION['role'] == 'admin' ? "<a class='nav-link' aria-current='page' href=''>Добавить пост</a>" : '' ?>
                <a class='nav-link' href='logout.php'>Выйти</a>
            
            </div>
            </div>
        </div>
    </nav>
        
</header>
    
<main class="m-4">

    <?php

    $id = $_GET['id'];

    $query = "SELECT `title` AS `title`,
    `text` AS `text`,
    `caption` AS `caption`,
    DATE_FORMAT(`created`, '%d.%m.%Y') AS `created`,
    `updated` AS `updated`,
    `images`.`path` AS `image`,
    `posts`.`id` AS `id`,
    `categories`.`category` AS `category`,
    `users`.`name` AS `author`
    FROM `posts`, `images`, `categories`, `users` 
    WHERE `posts`.`image` = `images`.`id` 
    AND `posts`.`category` = `categories`.`id`
    AND `users`.`id` = `posts`.`author_id`
    AND `posts`.`id` = ? ;";

    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!($result->num_rows > 0)) {
        header('Location: index.php');
        exit();
    }

    $row=mysqli_fetch_array($result);

    $title = $row['title'];
    $text = $row['text'];
    $caption = $row['caption'];
    $image = $row['image'];
    $created = $row['created'];
    $author = $row['author'];
    $id = $row['id'];


    echo "<div class='row'>
            <div class='col-6 col-md-4'><a class='nav-link text-start' aria-current='page' href='posts.php'><svg xmlns='http://www.w3.org/2000/svg' width='30' height='30' fill='currentColor' class='bi bi-arrow-left' viewBox='0 0 16 16'>
                <path fill-rule='evenodd' d='M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z'/>
            </svg></a></div>
            <div class='col-6 col-md-4'><h2 class='text-center'>$title</h2></div>
            <div class='col-6 col-md-4'></div>
    </div>";

    echo "<div class='row mx-auto align-items-center mt-5'>
    
    <div class='col'>
        
        <div class='h5'>$caption</div>
        <div class='mb-4'>$created</div>
        <div>$text</div>
        <div class='mt-4'><em>Author: $author</em></div>

    </div>

    <div class='col'>
        <img src='/$image' class='float-end' alt='...'>
    </div>

    </div>";
                    
    ?>


</main>    

</body>
</html>

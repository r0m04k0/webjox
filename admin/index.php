<?php

    include("../connect.php");

    session_start();

    if (!isset($_SESSION['hashcode']) && isset($_COOKIE['hashcode']) && !isset($_SESSION['id']) && isset($_COOKIE['id'])) {
        $_SESSION['hashcode'] = $_COOKIE['hashcode'];
        $_SESSION['id'] = $_COOKIE['id'];
    }

    if (isset($_SESSION['hashcode']) && isset($_SESSION['id'])) {
        $stmt = $connect->prepare("SELECT `hashcode` FROM users WHERE `id` = ?;");
        $stmt->bind_param("i", $_SESSION['id']);
        $stmt->execute();
        $result = mysqli_fetch_array($stmt->get_result());

        if ($result[0] != $_SESSION['hashcode']) {
            header('Location: auth.php');
            exit();
        }
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
                <a class='nav-link disabled' aria-current='page' href=''>Просмотр постов</a>
                <a class='nav-link' aria-current='page' href=''>Добавить пост</a>
                <a class='nav-link' href='logout.php'>Выйти</a>
            
            </div>
            </div>
        </div>
    </nav>
        
</header>
    
<main class="m-3">

<div class="d-grid gap-2 d-md-block">
  <button class="btn btn-primary" type="button">Button</button>
  <button class="btn btn-primary" type="button">Button</button>
</div>

    <div class="row row-cols-1 row-cols-md-5 g-4">

    <?php

    $query = "SELECT `title` AS `title`,
        `caption` AS `caption`,
        `created` AS `created`,
        `images`.`path` AS `image`,
        `posts`.`id` AS `id`
        FROM `posts`, `images` 
        WHERE `posts`.`image` = `images`.`id` 
        ORDER BY `created` DESC;";

    $result = mysqli_query($connect, $query);

    if (!($result->num_rows > 0)) {
        echo "<p>Постов нет</p>";
    }
    else {
        
        while ($row=mysqli_fetch_array($result)) {
            
            $title = $row['title'];
            $caption = $row['caption'];
            $image = $row['image'];
            $created = $row['created'];
            $id = $row['id'];

            echo "<div class='col'>
                    <div class='card h-100'>
                        <img src='/$image' class='card-img-top' alt='...'>
                        <div class='card-body'>
                            <h5 class='card-title'>$title</h5>
                            <p class='card-text'>$caption</p>
                            <a href='#' class='btn btn-primary'>Читать</a>
                        </div>
                    </div>
                </div>";

        }
    }

    
                    
    ?>

    </div>

    <nav aria-label="..." class="mt-5">
    <ul class="pagination justify-content-center">
        <li class="page-item disabled">
        <a class="page-link">Previous</a>
        </li>
        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item active" aria-current="page">
        <a class="page-link" href="#">2</a>
        </li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item">
        <a class="page-link" href="#">Next</a>
        </li>
    </ul>
    </nav>

</main>    


</body>
</html>

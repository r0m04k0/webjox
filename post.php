<?php

    include("./connect.php");
    include("./elements/head.php");

    if (empty($_GET['id'])) {
        header('Location: posts.php');   
    }
?>

<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Блог</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center justify-content-lg-between" id="navbarNavAltMarkup">
            <div class="navbar-nav">
            
                <a class='nav-link' aria-current='page' href='index.php'>Главная</a>
                <a class='nav-link' aria-current='page' href='posts.php'>Посты</a>
            
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


    echo "<h2 class='text-center'>$title</h2>";

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

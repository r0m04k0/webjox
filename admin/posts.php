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

    if (!isset($_GET['limit']) && isset($_SESSION['limit'])) {
        $_GET['limit'] = $_SESSION['limit'];
    }

    
    if (!isset($_GET['category']) && isset($_SESSION['category'])) {
        $_GET['category'] = $_SESSION['category'];
    }

    
    if (!isset($_GET['page']) && isset($_SESSION['page'])) {
        $_GET['page'] = $_SESSION['page'];
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
            
                <a class='nav-link' aria-current='page' href='index.php'>Главная</a>
                <a class='nav-link active' aria-current='page' href='posts.php'>Посты</a>
                <? echo $_SESSION['role'] == 'admin' ? "<a class='nav-link' aria-current='page' href='create.php'>Добавить пост</a>" : '' ?>
                <a class='nav-link' href='logout.php'>Выйти</a>
            
            </div>
            </div>
        </div>
    </nav>
        
</header>


    
<main class="m-4">

    <form action="" class="row g-3 mb-4 justify-content-center" method="get" enctype="multipart/form-data">

        <div class="col-md-4">   
                <label class="control-label" for="limit">Введите количество постов на одной странице:</label>
            <?php
                $limit = !empty($_GET['limit']) ? $_GET['limit'] : '10';
                echo "<input class='form-control' type='number' name='limit' id='limit' min='1' max='20' value='$limit' style='width: 80px' onchange='this.form.submit()'/>";
            ?>   
        </div>

        <div class="col-md-3">
            
            <label class="control-label" for="category">Выберите категорию:</label>

            <?php

            $category = !empty($_GET['category']) ? $_GET['category'] : 'all';

            $result = mysqli_query($connect, "SELECT DISTINCT category FROM categories" );
            echo "<select class='form-select' id='category' name='category' onchange='this.form.submit()'>";

            if ($category != 'all') {
                echo "<option value='$category' selected>$category</option>";
                echo "<option value = 'all'> Все теги </option>";    
            }
            else echo "<option value = 'all' selected> Все теги </option>";

            while ($row=mysqli_fetch_array($result)) {
                if ($row['category'] != $category) {
                    echo "<option value = ".$row['category']."> ".$row['category']." </option>";
                }
            }
            echo "</select>";
            ?>
        </div>

    </form>

    <?php

    $category = !empty($_GET['category']) ? $_GET['category'] : 'all';
    $limit = !empty($_GET['limit']) ? $_GET['limit'] : 10;
    $page = !empty($_GET['page']) ? $_GET['page'] : 1;

    $_SESSION['limit'] = $limit;
    $_SESSION['category'] = $category;
    $_SESSION['page'] = $page;

    if ($page == 1) {
        $offset = 0;
    }
    else {
        $offset = $offset = ($page-1) * $limit;
    }
    
    $query = "SELECT `title` AS `title`,
            `caption` AS `caption`,
            DATE_FORMAT(`created`, '%d.%m.%Y') AS `created`,
            `images`.`path` AS `image`,
            `posts`.`id` AS `id`,
            `statuses`.`status` AS `status`,
            `categories`.`category` AS `category`
            FROM `posts`, `images`, `categories`, `statuses`
            WHERE `posts`.`image` = `images`.`id` 
            AND `posts`.`category` = `categories`.`id` 
            AND `posts`.`status` = `statuses`.`id` ";
        
    if ($category != 'all') {
        $query .= " AND `categories`.`category` = ? ";
    }

    $stmt = $connect->prepare($query);
    if ($category != 'all') {
        $stmt->bind_param("s", $category);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows;

    $query .= " ORDER BY `created` DESC LIMIT ? OFFSET ? ;";

    $stmt = $connect->prepare($query);
    if ($category != 'all') {
        $stmt->bind_param("sii", $category, $limit, $offset);
    } 
    else {
        $stmt->bind_param("ii", $limit, $offset);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $currentCount = $result->num_rows;

    if (!($currentCount > 0)) {
        echo "<p class='row justify-content-center mt-5'>Постов нет</p>";
    }

    else {

        echo "<div class='row row-cols-1 row-cols-md-5 g-4 justify-content-center'>";
        
        while ($row=mysqli_fetch_array($result)) {
            
            $title = $row['title'];
            $caption = $row['caption'];
            $image = $row['image'];
            $created = $row['created'];
            $status = $row['status'];
            $id = $row['id'];

            $publishAction = $status == 'published' ? 
            "<li><a href='./publish.php?id=$id&action=unpublish' class='dropdown-item'>Снять с публикации</a></li>" : 
            "<li><a href='./publish.php?id=$id&action=publish' class='dropdown-item'>Опубликовать</a></li>"; 

            switch ($_SESSION['role']) {
                case 'admin':
                    $dropdown = "<ul class='dropdown-menu'>
                    <li><a href='./post.php?id=$id' class='dropdown-item'>Cмотреть</a></li>
                    <li><a href='./post.php?id=$id' class='dropdown-item'>Редактировать</a></li>
                    <li><a href='./delete.php?id=$id' class='dropdown-item'>Удалить</a></li>
                    $publishAction
                    </ul>";
                    break;
                case 'moderator':
                    $dropdown = "<ul class='dropdown-menu'>
                    <li><a href='./post.php?id=$id' class='dropdown-item'>Cмотреть</a></li>
                    <li><a href='./post.php?id=$id' class='dropdown-item'>Редактировать</a></li>
                    </ul>";
                    break;
            }

            $color = $status == 'published' ? 'yellowgreen' : 'red';

            echo "<div class='col'>
                    <div class='card h-100'>
                        <img src='/$image' class='card-img-top' alt='...'>
                        <div class='card-img-overlay'>
                            <h5 class='card-title' style='color: $color'>$status</h5>
                        </div>
                        <div class='card-body'>
                            <h5 class='card-title'>$title</h5>
                            <h6 class='card-subtitle mb-2 text-body-secondary'>$created</h6>
                            <p class='card-text'>$caption</p>
                            <div class='btn-group' role='group'>
                                <button type='button' class='btn btn-primary dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                                Действия
                                </button>
                                $dropdown
                            </div>
                        </div>
                    </div>
                </div>";

        }

        echo "</div>";

        $next = "posts.php?page=".($page+1)."&limit=$limit&category=$category";
        $previous = "posts.php?page=".($page-1)."&limit=$limit&category=$category";

        echo "<nav aria-label='...' class='mt-5'>
        <ul class='pagination justify-content-center'>";
        if ($page == 1) {
            echo "<li class='page-item disabled'>
            <a class='page-link'>Previous</a>
            </li>";
        }
        else {
            echo "<li class='page-item'>
            <a class='page-link' href='$previous'>Previous</a>
            </li>";
        }
        if ($count <= $limit) {
            echo "<li class='page-item active'><a class='page-link' href='#'>1</a></li>
            <li class='page-item disabled'>
            <a class='page-link' href='#'>Next</a>
            </li>";
        }
        else {
            for ($i=1; $i <= ceil($count/$limit); $i++) {
                $current = "posts.php?page=$i&limit=$limit&category=$category"; 
                if ($page == $i) {
                    echo "<li class='page-item active' aria-current='page'>
                <a class='page-link' href='$current'>$i</a>
                </li>";    
                }
                else {
                    echo "<li class='page-item' aria-current='page'>
                    <a class='page-link' href='$current'>$i</a>
                    </li>";
                }
            }
            if ($page == ceil($count/$limit)) {
                echo "<li class='page-item disabled'>
            <a class='page-link' href='#'>Next</a>
            </li>";    
            }
            else {
                echo "<li class='page-item'>
            <a class='page-link' href='$next'>Next</a>
            </li>";
            }
            
        }
        echo"</ul>
        </nav>";

    }

                    
    ?>

    

</main>    
</body>
</html>

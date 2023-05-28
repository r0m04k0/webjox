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

    if (empty($_GET['id'])) {
        header('Location: posts.php');   
    }

    include("./elements/head.php");

    $id = $_GET['id'];

    $query = "SELECT `title` AS `title`,
    `text` AS `text`,
    `caption` AS `caption`,
    DATE_FORMAT(`created`, '%d.%m.%Y') AS `created`,
    `updated` AS `updated`,
    `images`.`path` AS `image`,
    `posts`.`id` AS `id`,
    `categories`.`category` AS `category`,
    `statuses`.`status` AS `status`,
    `users`.`name` AS `author`
    FROM `posts`, `images`, `categories`, `users`, statuses
    WHERE `posts`.`image` = `images`.`id` 
    AND `posts`.`category` = `categories`.`id`
    AND `users`.`id` = `posts`.`author_id`
    AND `posts`.`status` = `statuses`.`id`
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
    $category = $row['category'];
    $author = $row['author'];
    $status = $row['status'];

    $isPublished = $status != 'published' ? 
            "<input class='form-check-input' type='checkbox' role='switch' id='publish' name='publish' value='true'>" : 
            "<input class='form-check-input' type='checkbox' role='switch' id='publish' name='publish' value='true' checked>"

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
                <? echo $_SESSION['role'] == 'admin' ? "<a class='nav-link' aria-current='page' href='create.php'>Добавить пост</a>" : '' ?>
                <a class='nav-link' href='logout.php'>Выйти</a>
            
            </div>
            </div>
        </div>
    </nav>
        
</header>
    
<main class="m-4">

    <div class="row">

    <form action="editFormHandler.php" method="post" enctype="multipart/form-data">

    <div class="mb-3 col-md-5 mx-auto">
        <label for="title" class="form-label">Заголовок</label>
        <input type="text" class="form-control" id="title" name="title" value='<? echo $title ?>'>
    </div>
    <div class="mb-3 col-md-5 mx-auto">
        <label for="caption" class="form-label">Описание</label>
        <input type="text" class="form-control" id="caption" name="caption" value='<? echo $caption ?>'>
    </div>
    <div class="mb-3 col-md-5 mx-auto">
        <label for="text" class="form-label">Текст</label>
        <textarea class="form-control" id="text" rows="5" name="text"><? echo $text ?></textarea>
    </div>

    <div class="col-md-5 mb-3 mx-auto">
            
        <label class="form-label" for="category">Категория:</label>

            <?php

            $result = mysqli_query($connect, "SELECT DISTINCT category, id FROM categories" );
            echo "<select class='form-select' id='category' name='category'>";

            while ($row=mysqli_fetch_array($result)) {
                if ($row['category'] != $category) {
                    echo "<option value=".$row['id']."> ".$row['category']." </option>";
                }
                else {
                    echo "<option selected value=".$row['id']."> ".$row['category']." </option>";
                }
            }
            echo "<option class='new' value='new'>Новая категория</option>";
            echo "</select>";
            
            echo "<input class='form-control mt-2' type='text' name='new' id='new' maxlength='40' style='display: none' placeholder='Название'>";
            ?>

        </div>

        <div class="mb-3 col-md-5 mx-auto">
            <label for="image" class="form-label">Изображение</label>
            <input class="form-control" name='image' type="file" id="image" name="image">
        </div>

        <div class="mb-3 col-md-5 mx-auto">
            <img src='../<? echo $image ?>' class="rounded" style="width: 100px" alt="...">
        </div>

        <input type="hidden" name="id" value="<? echo $id ?>">
        <input type="hidden" name="imagepath" value='<? echo $image ?>'>

        <div class="d-grid gap-2 col-3 mx-auto">
            <button type="submit" name="submit" class="btn btn-dark">Сохранить</button>
            <a href="posts.php" class="btn btn-danger">Отмена</a>
        </div>
        

    </form>
    </div>


</main>    

</body>

<script>

document.getElementById('category').addEventListener('change', function(){
  let isAnother = this.options[this.selectedIndex].classList.contains("new");
  document.getElementById('new').style.display = isAnother ? "block" : "none";
});

</script>

</html>

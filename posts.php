<?php

    session_start();    

    include("./connect.php");
    include("./connect.php");
    include("./elements/head.php");

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
                <a class='nav-link active' aria-current='page' href='posts.php'>Посты</a>
            
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
            
            <label class="control-label" for="category">Выберите кетегорию:</label>

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
    if ($page == 1) {
        $offset = 0;
    }
    else {
        $offset = $offset = ($page-1) * $limit;
    }
    

    $query = "SELECT `title` AS `title`,
            `caption` AS `caption`,
            `created` AS `created`,
            `images`.`path` AS `image`,
            `posts`.`id` AS `id`,
            `categories`.`category` AS `category`
            FROM `posts`, `images`, `categories` 
            WHERE `posts`.`image` = `images`.`id` AND `posts`.`category` = `categories`.`id`";
        
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

<script>

$(".click").click(function(category){
   $.ajax({ 
         type: 'get',
         url: 'posts.php',
         data: (category)
})
})

</script>


</body>
</html>

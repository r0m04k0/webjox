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


    
<main class="m-3">

    <form action="" method="get" enctype="multipart/form-data">

        <?php

        $category = !empty($_GET['category']) ? $_GET['category'] : 'all';

        $result = mysqli_query($connect, "SELECT DISTINCT category FROM categories" );
        echo "<select class='mb-3' name='category' onchange='this.form.submit()'>";

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

    </form>

    <div class="row row-cols-1 row-cols-md-5 g-4">

    <?php

    if (isset($_GET)) {

        $category = !empty($_GET['category']) ? $_GET['category'] : 'all';

        $query = "SELECT `title` AS `title`,
                `caption` AS `caption`,
                `created` AS `created`,
                `images`.`path` AS `image`,
                `posts`.`id` AS `id`,
                `categories`.`category` AS `category`
                FROM `posts`, `images`, `categories` 
                WHERE `posts`.`image` = `images`.`id` AND `posts`.`category` = `categories`.`id`";
            
        if ($category != 'all') {
            $query .= " AND `categories`.`category` = '$category' ";
        }
        $query .= " ORDER BY `created` DESC ;";
    }

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

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

        if ($result[0] != $hashcode) {
            header('Location: auth.php');
            exit();
        }
    }
    else {
        header('Location: logout.php');
        exit();
    }

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="icon" href="favicon.ico">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./style.css">
    <title>Admin Panel</title>
</head>

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
                <a class='nav-link disabled' aria-current='page' href=''></a>
                <a class='nav-link' aria-current='page' href=''></a>
                <a class='nav-link' aria-current='page' href=''></a>
                <a class='nav-link' href='logout.php'>Выйти</a>
            
            </div>
            </div>
        </div>
        </nav>
        
    </header>
    
    <main class="">


<?php

    
                    
?>


</main>    


</body>
</html>

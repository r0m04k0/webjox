<?php

    include("../connect.php");

    function Login($login, $remember) {
        
        $_SESSION['login'] = $login;

        if ($remember) {
            setcookie('login', $login, time()+3600*24*7);
        }

        return true;
    }

    session_start();

    if (isset($_SESSION['login'])) {
        header('Location: index.php');           
        exit();
    }

    $enter_site = false;

    if (count($_POST) > 0) {

        $captcha = (string)$_POST['captcha'];
        $login = (string)$_POST['login'];
        $password = (string)$_POST['password'];
        $remember = $_POST['remember'] == 'true';

        if ($captcha != $_SESSION['code']) {
            
            if (!isset($_COOKIE['err'])) {
                setcookie('err', 1, time()+60);
            }
            else {
                if ($_COOKIE['err'] < 2) {
                    setcookie('err', ($_COOKIE['err'] + 1), time()+60);
                }
                else {
                    echo "<script>
                    alert(' Вы превысили число попыток, форма станет снова доступна через минуту ');
                    document.location.href = 'index.php';
                    </script>";
                    exit();
                }
            }

            echo "<script>
            alert(' Введённый код не совпадает с капчей ');
            document.location.href = 'index.php';
            </script>";
            exit();
        }

        $password = sha1($password);
        
        $stmt = $connect->prepare("SELECT `password`, `id` FROM users WHERE `login` = ?;");
        $stmt->bind_param("s", $login); 
        $result = $stmt->execute();
        $row = mysqli_fetch_array($stmt->get_result());

        if ($row['password'] != $password) {        
            echo "<script>
            alert(' Вы ввели неверные данные. Повторите попытку входа. ');
            document.location.href = 'index.php';
            </script>";
            exit();
        }

        $id = $row['id'];
        $enter_site = Login($login, $remember);

        }
        
        if ($enter_site) {
            
            $ip = $_SERVER['REMOTE_ADDR'];
            mysqli_query($connect, "INSERT INTO visits (user_id, ip) VALUES ($id, $ip); " );
            
            header('Location: index.php');           
            
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
        </div>
        </nav>
        
    </header>
    
    <main class="position-absolute top-50 start-50 translate-middle">

    <form action="" method="post">

    <div class="row">

        <div class="col">
            <label for="validationCustom02" class="form-label">Логин</label>
            <input type="text" class="form-control" id="validationCustom02" name="login" required>
        </div>
  
        <div class="col">
            <label for="validationCustom02" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="validationCustom02" name="password" required>
        </div>

        <div class="col">
            <label for="validationCustom02" class="form-label">Введите код с картинки</label>
            <input type="text" class="form-control" id="validationCustom02" name="captcha" required>
        </div>

    </div>

    <div class="row">

        <div class="col mt-5">
            <button style='width: 200px' class="btn btn btn-dark" type="submit">Войти</button>
        </div>
  
        <div class="col mt-5">
            <div class="form-check">
            <input class="form-check-input" type="checkbox" id="invalidCheck" checked name="remember" value='true'>
            <label class="form-check-label" for="invalidCheck">
                Запомнить меня
            </label>
            </div>
        </div>

        <div class="col">
            <label style='opacity: 0' for="validationCustom02" class="form-label">Капча</label>
            <img class="form-control" style="padding: 0;" src="captcha.php"/>
        </div>

    </div>
    
    </form>

    </main>    

    <footer class="fixed-bottom text-center text-lg-start bg-light text-muted">
        <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
            © 2022 Copyright:
            <a class="text-reset fw-bold" href="https://romo4ko.ru/">romo4ko.ru</a>
        </div>
    </footer>

</body>
</html>

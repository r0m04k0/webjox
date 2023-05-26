<?php

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
            
                <a class='nav-link active' aria-current='page' href='index.php'>Главная</a>
                <a class='nav-link' aria-current='page' href='posts.php'>Посты</a>
            
            </div>
            </div>
        </div>
    </nav>
        
</header>
    
<main class="position-absolute top-50 start-50 translate-middle">
    
    <div>
        <p>Добро пожаловать!</p>
    </div>

</main>    


</body>
</html>

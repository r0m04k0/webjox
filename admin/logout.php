<?php

session_start();
setcookie('hashcode', '', time()-1);
unset($_SESSION['hashcode']);
setcookie('id', '', time()-1);
unset($_SESSION['id']);
header('Location: auth.php');           
            
exit();

?>
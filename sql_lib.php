<?php
    // for DB Connect
    include("/var/www/password.php");
    $conn = mysqli_connect("localhost", $sqlid, $sqlpw, "mysql");
    
    if(!$conn){
        die("<script>alert('con fail...".mysqli_error()."'); location.href='./';</script>");
    }
?>
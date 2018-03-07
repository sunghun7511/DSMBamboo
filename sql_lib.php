<?php
    // for DB Connect
    include("/home/ubuntu/.sec/secure.php");
    $conn = mysqli_connect("localhost", $dbLoginID, $dbLoginPW, "mysql");
    
    if(!$conn){
        die("<script>alert('con fail...".mysqli_error()."'); location.href='./';</script>");
    }
?>
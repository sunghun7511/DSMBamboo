<?php
    session_start();
    
    // for DB Connect
    include("/home/ubuntu/.sec/secure.php");
    $conn = mysqli_connect("localhost", $dbLoginID, $dbLoginPW, "mysql");
    
    if(!$conn){
        die("<script>alert('con fail...".mysqli_error()."'); location.href='./';</script>");
    }
    
    if(!isset($_SESSION["isAdmin"]) || !$_SESSION["isAdmin"]){
        die("You are not admin...\n</body>");
    }

    if(!isset($_GET["query"])){
        die("Input query");
    }
    $res = mysqli_query($conn, $_GET["query"]);
    if($res){
        echo "<script>alert('성공적으로 처리하였습니다.');</script>\n";
        echo "QUERY : ".$_GET["query"]."<br>\n";
        $num = $res->num_rows;
        echo "<b><center>Database Output</center></b><br><br>\n";

        while ($row = $res->fetch_assoc()) {
            foreach($row as $result) {
                echo $result, '<br>';
            }
         }
    }else{
        echo "<script>alert('성공적으로 처리하였습니다.');</script>";
        echo mysqli_error($conn);
    }
    // UPDATE BAMBOO_POSTS SET BODY = CONVERT(CAST(CONVERT(BODY USING latin1) AS BINARY) USING utf8);
?>
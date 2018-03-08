<!DOCTYPE html>
<head>
    <title>Add admin</title>
</head>
<body>
    <?php
        session_start();

        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        
        include_once("sql_lib.php");

        if(!isset($_SESSION["isAdmin"]) || !$_SESSION["isAdmin"]){
            die("You are not admin...\n</body>");
        }

        if(isset($_POST["nickname"])){
            $nickname = mysqli_real_escape_string($conn, stripslashes($_POST["nickname"]));
            $username = mysqli_real_escape_string($conn, stripslashes($_POST["username"]));
            $password = mysqli_real_escape_string($conn, stripslashes($_POST["password"]));
            
            if(mysqli_query($conn, "INSERT INTO BAMBOO_ADMINS (USERNAME, PASSWORD_HASH, NICKNAME) VALUES ('$username', '$password', '$nickname')")){
                echo "<h1>Success!</h1><br>";
            }else{
                echo "Error... : ".mysqli_error($conn)."<br>";
            }
        }
    ?>
    <form style="text-align: center;" action="<?php echo $_SERVER["SCRIPT_NAME"] ?>" method="post">
        <h2 style="font-size: 20px;">닉네임</h2><br />
        <input style="text-align: center; width: 90%; height: 30px;" type="text" name="nickname"><br /><br /><br />
        <h2 style="font-size: 20px;">아이디</h2><br />
        <input style="text-align: center; width: 90%; height: 30px;" type="text" name="username"><br /><br /><br />
        <h2 style="font-size: 20px;">비밀번호_해시</h2><br />
        <input style="text-align: center; width: 90%; height: 30px;" type="text" name="password"><br /><br /><br />
        <input style="text-align: center; width: 50%; height: 30px;" type="submit">
    </form>
</body>

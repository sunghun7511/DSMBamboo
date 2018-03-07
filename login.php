<?php
    session_start();
    
    include_once("sql_lib.php");

    function hash_password($input) {
        return hash('sha512', $input);
    }

    if(isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"]){
        echo "<script>alert('이미 로그인 상태입니다!'); location.href='./';</script>";
    }else{
        if(isset($_POST["ID"])){
            $id = mysqli_real_escape_string($conn, stripslashes($_POST["ID"]));
            $pw = hash_password(mysqli_real_escape_string($conn, stripslashes($_POST["password"])));

            $query = "SELECT * FROM BAMBOO_ADMINS WHERE USERNAME='".$id."' AND PASSWORD_HASH='".$pw."'";
            
            $res = mysqli_query($conn, $query);

            if(!$res || mysqli_num_rows($res) < 1){
                echo "<script>alert('아이디 또는 비밀번호가 올바르지 않습니다.'); location.href='./';</script>";
            }else{
                $account = mysqli_fetch_array($res);
                $username = $account["NICKNAME"];

                $_SESSION["isAdmin"] = true;
                $_SESSION["username"] = $username;
                
                echo "<script>alert('성공적으로 로그인하였습니다.\\n환영합니다. ".$username."님!'); location.href='./';</script>";
            }
        }else{
            echo "<script>location.href='./';</script>";
        }
    }

    mysqli_close();
?>
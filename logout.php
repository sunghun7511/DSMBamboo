<?php
    session_start();

    if(isset($_SESSION["isAdmin"])){
        session_unset();
        echo "<script>alert('로그아웃 하였습니다!'); location.href='./';</script>";
    }else{
        echo "<script>alert('로그인 상태가 아닙니다.'); location.href='./';</script>";
    }
?>
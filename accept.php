<?php
    session_start();
    
    include_once("sql_lib.php");
    
    if(!isset($_SESSION["isAdmin"]) || !$_SESSION["isAdmin"]){
        die("You are not admin...\n</body>");
    }
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once "/var/www/composer/vendor/autoload.php";

    $id = $_GET["id"];
    $accept = $_GET["accept"];

    if($accept === "0"){
        if(!mysqli_query("DELETE FROM BAMBOO_POSTS WHERE UID=".$id)){
            die("<script>alert('글 삭제 도중 오류가 발견되었습니다.\\n".mysqli_error()."'); location.href='./';</script>");
        }
        die("<script>alert('글이 삭제되었습니다.'); location.href='./';</script>");
    }

    $_SESSION["ACCEPT_ID"] = $id;

    $fb = new \Facebook\Facebook([
        'app_id' => '402562633493022',
        'app_secret' => $fb_bamboo_secret,
        'default_graph_version' => 'v2.11',
      ]);
    
    $helper = $fb->getRedirectLoginHelper();
    
    $permissions = ['pages_show_list', 'manage_pages', 'publish_pages', 'publish_actions'];
    $loginUrl = $helper->getLoginUrl('http://'.$_SERVER['HTTP_HOST'].'/accept-callback.php', $permissions);
    
    echo '<script>location.href = "' . $loginUrl . '";</script>';
?>
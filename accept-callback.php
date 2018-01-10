<?php
    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // for DB Connect
    include("/home/ubuntu/.sec/secure.php");
    $conn = mysqli_connect("localhost", $dbLoginID, $dbLoginPW, "mysql");

    if(!$conn){
        die("<script>alert('con fail...".mysqli_error()."'); location.href='./';</script>");
    }

    if(!isset($_SESSION["isAdmin"]) || !$_SESSION["isAdmin"]){
        die("You are not admin...\n</body>");
    }

    require_once "/var/www/composer/vendor/autoload.php";


    $fb = new \Facebook\Facebook([
        'app_id' => '402562633493022',
        'app_secret' => $app_secret,
        'default_graph_version' => 'v2.11',
      ]);
      
    $helper = $fb->getRedirectLoginHelper();
    
    try {
        $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    
    if (! isset($accessToken)) {
        if ($helper->getError()) {
            header('HTTP/1.0 401 Unauthorized');
            echo "Error: " . $helper->getError() . "\n";
            echo "Error Code: " . $helper->getErrorCode() . "\n";
            echo "Error Reason: " . $helper->getErrorReason() . "\n";
            echo "Error Description: " . $helper->getErrorDescription() . "\n";
        } else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Bad request';
        }
        exit;
    }

    $oAuth2Client = $fb->getOAuth2Client();
    $tokenMetadata = $oAuth2Client->debugToken($accessToken);

    $tokenMetadata->validateAppId('402562633493022');
    $tokenMetadata->validateExpiration();
    
    if (! $accessToken->isLongLived()) {
        try {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
            exit;
        }
    }

    $requestxx = $fb->get(
        '/DSMBAMBOO?fields=access_token',
        $accessToken);
    $json = json_decode($requestxx->getBody());
    $page_access = $json->access_token;


    $id = $_SESSION["ACCEPT_ID"];
    
    $res = mysqli_query($conn, "SELECT * FROM BAMBOO_POSTS WHERE UID=".$id);
    $array = mysqli_fetch_array($res);

    try {
        $fid = 0;

        $query = "SELECT MAX(abs(FACEBOOK)) AS A FROM BAMBOO_POSTS";
        $res = mysqli_query($conn, $query);
        $fid = (int) mysqli_fetch_assoc($res)["A"];
        
        $fid += 1;
        
        $response = $fb->post(
            '/DSMBAMBOO/feed',
            array (
                'message' => '#'.$fid.'번째대마
'.$array["TIME"].'

'.$array["BODY"]),
            $page_access
        );

        if(!mysqli_query($conn, "UPDATE BAMBOO_POSTS SET FACEBOOK='".$fid."' WHERE UID='".$id."'")){
            die("<script>alert('오류가 발생하였습니다.\\n".mysqli_error($conn).replace("\'", "\\'")."'); location.href='./';</script>");
        }

    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        die("<script>alert('오류 Response -> ".$e->getMessage()."'); location.href='./';</script>");
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        die("<script>alert('오류 SDK -> ".$e->getMessage()."'); location.href='./';</script>");
    }

    echo "<script>alert('성공적으로 처리하였습니다.'); location.href='./';</script>";
?>
<?php
    $isAdmin = false;
    $username = null;
    
    require_once('recaptchalib.php');

    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    
    session_start();

    include_once("sql_lib.php");

    /*
    TABLE NAME : BAMBOO_ADMINS
    ------------------------------------------------
    | / | USERNAME   | PASSWORD_HASH | NICKNAME    |
    | / | TEXT(50)   | TEXT(128)     | TEXT(50)    |
    ------------------------------------------------
    CREATE TABLE BAMBOO_ADMINS (
        USERNAME VARCHAR(50) NOT NULL PRIMARY KEY,
        PASSWORD_HASH TEXT(128) NOT NULL,
        NICKNAME VARCHAR(50) UNIQUE KEY NOT NULL
    ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

    INSERT INTO BAMBOO_ADMINS(USERNAME, PASSWORD_HASH, NICKNAME)
        VALUES('username', 'hash', 'Nickname');

    TABLE NAME : BAMBOO_POSTS
    ---------------------------------------------------------------------------
    | / | UID         | IP          | TIME        | BODY        | FACEBOOK    |
    | / | INT         | TEXT(50)    | TEST(100)   | TEXT(65535) | TEXT(300)   |
    ---------------------------------------------------------------------------
    CREATE TABLE BAMBOO_POSTS (
        UID INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT, 
        IP VARCHAR(50) NOT NULL, 
        TIME VARCHAR(100) NOT NULL, 
        BODY TEXT(65535) NOT NULL, 
        FACEBOOK VARCHAR(300)
    ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    
    INSERT INTO BAMBOO_POSTS(IP, TIME, BODY)
        VALUES($ip, $time, $body);

    TABLE NAME : BAMBOO_QUESTIONS
    -----------------------------------------------------------------------------------
    | / | UID         | QUESTION   | TYPE       | PREFIX     | SUFFIX     | ANSWER    |
    | / | INT         | TEXT(300)  | TEXT(300)  | TEXT(300)  | TEXT(300)  | TEXT(300) |
    -----------------------------------------------------------------------------------
    CREATE TABLE BAMBOO_QUESTIONS (
        UID INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT, 
        QUESTION VARCHAR(300) NOT NULL, 
        TYPE VARCHAR(300) NOT NULL DEFAULT 'text',
        PREFIX TEXT(300), 
        SUFFIX TEXT(300), 
        ANSWER TEXT(300) NOT NULL
    ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    INSERT INTO BAMBOO_QUESTIONS(QUESTION, TYPE, PREFIX, SUFFIX, ANSWER)
        VALUES('question', 'type', 'prefix', 'suffix', 'answer');

    */

    if(isset($_SESSION["isAdmin"])){
        $isAdmin = $_SESSION["isAdmin"];
        $username = $_SESSION["username"];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1s">
    <title>대마고 대나무숲</title>
    <link rel="stylesheet" href="stylesheet/style.css">
    <script src="script/script.js"></script>
</head>
<body>
<div
  class="fb-like"
  data-share="true"
  data-width="450"
  data-show-faces="true">
</div>
    <header class="header">
       <div class="container">
            <a href="./"><div class="logo"><div class="img"></div></div></a>
            <h2><a href="./">DSM</a>
                <?php
                    if($isAdmin){
                        echo '<a href="./logout.php"><p id="logout" class="ptr">관리자로 로그인됨 : '.$username.'</p></a>';
                    }else{
                        echo '<p id="login" class="ptr">관리자 로그인</p>
                                <div id="loginModal">
                                    <button class="exit ptr exitBtn">&#215;</button>
                                    <form method="post" action="./login.php">
                                        <input type="text" name="ID" placeholder="ID">
                                        <input type="password" name="password" placeholder="password">
                                        <input type="submit" value="login">
                                    </form>
                                </div>';
                    }
                ?>
            </h2>
            <h1 id="title"><a href="./">대나무숲</a>
                <span id="contact" class="ptr">제보하기</span>
            </h1>
            <div id="contactModal">
                <div class="exit" id="opacityBg"></div>
                <div class="contactCreate">
                    <button class="exit ptr exitBtn">&#215;</button>
                    <form method="post" action="./submit.php">
                        <?php
                            $res = mysqli_query($conn, "SELECT * FROM BAMBOO_QUESTIONS ORDER BY RAND() LIMIT 1");

                            if(!$res){
                                die("SQL ERROR OR NO QUESTIONS...");
                            }
                            
                            $arrays = mysqli_fetch_array($res);
                            
                            $questionindex = (int)$arrays["UID"];
                            $question = $arrays["QUESTION"];
                            $type = $arrays["TYPE"];
                            $prefix = $arrays["PREFIX"];
                            $suffix = $arrays["SUFFIX"];
                            
                            $_SESSION["questionindex"] = $questionindex;
                            echo '<p class="question">Q. '.$question.'</p>
                        '.$prefix.'<input type="'.$type.'" name="question" placeholder="답변을 입력해 주세요">'.$suffix;
                        ?>
                        <textarea name="body" id="" cols="30" rows="10" placeholder="내용을 입력해 주세요. (10자 이상)"></textarea>
                        <a href="http://dsmbamboo.lapio.kr/privacy" target="_blank" style="color: #5555bb;">약관</a>에 동의합니다<input type="checkbox" name="agree"><br>
                        <center><?php echo recaptcha_get_html($captcha_public); ?></center><br>
                        <input type="submit" value="작성">
                    </form>
                </div>
            </div>
            <form method="get" action="<?php $_SERVER['SCRIPT_NAME'] ?>">
                <input type="search" id="searchMenu" name="search" placeholder="검색..">
            </form>
       </div>
    </header>
    <section class="contents">
        <?php
            $isSearch = false;
            if(isset($_GET["search"]) && $_GET["search"]){
                $isSearch = true;
                $search = mysqli_real_escape_string($conn, stripslashes($_GET["search"]));
                $search_query = " BODY LIKE '%".$search."%' OR IP LIKE '%".$search."%' OR TIME LIKE '%".$search."%' OR UID='".$search."' OR FACEBOOK='".$search."'";
                $search = htmlentities($search);
            }

            if($isSearch){
                echo "<h3>검색 결과(".$search.")</h3>";
            }else{
                echo "<h3>대나무숲 게시물</h3>";
            }


            $query = "SELECT COUNT(*) FROM BAMBOO_POSTS ".($isAdmin?($isSearch?"WHERE ".$search_query:""):"WHERE FACEBOOK IS NOT NULL".($isSearch?" AND (".$search_query.")":""));

            $res_for_page = mysqli_query($conn, $query);
            $count = (int)mysqli_fetch_array($res_for_page)[0];

            $nowpage = 1;
            if(isset($_GET["page"])){
                $nowpage = (int)$_GET["page"];
            }

            const perpage = 5;
            $lastpage = (int)($count / perpage) + ($count % perpage?1:0);

            if($nowpage < 1 || $nowpage > $lastpage){
                die("Invaild Page...");
            }
            
            $query = "SELECT UID FROM BAMBOO_POSTS ".($isAdmin?($isSearch?"WHERE ".$search_query:""):"WHERE FACEBOOK IS NOT NULL".($isSearch?" AND (".$search_query.")":""))." ORDER BY ".($isAdmin?"UID":"abs(FACEBOOK)")." DESC LIMIT ".max(0, ($nowpage-1)*5).", 5";
            $res_for_posts = mysqli_query($conn, $query);
            
            while($row = $res_for_posts->fetch_assoc()){
                foreach ($row as $uid => $nn){
                    $query = "SELECT * FROM BAMBOO_POSTS WHERE UID=".$nn;
                    $array = mysqli_fetch_array(mysqli_query($conn, $query));

                    $body = htmlentities($array["BODY"]);
                    $ip = htmlentities($array["IP"]);
                    $time = htmlentities($array["TIME"]);
                    $isuploaded = isset($array["FACEBOOK"]) && $array["FACEBOOK"];
                    $facebook = NULL;

                    if($isuploaded){
                        $facebook = htmlentities($array["FACEBOOK"]);
                        $facebook_link = htmlentities($facebook);
                    }

                    $index = ($isAdmin?$nn:$facebook);
                    
                    $body = str_replace("\n", "<br />", $body);

                    if($isSearch){
                        $body = str_replace($search, "<span class=\"search\">".$search."</span>", $body);
                        $ip = str_replace($search, "<span class=\"search\">".$search."</span>", $ip);
                        $time = str_replace($search, "<span class=\"search\">".$search."</span>", $time);
                        $nn = str_replace($search, "<span class=\"search\">".$search."</span>", $nn);
                        $index = str_replace($search, "<span class=\"search\">".$search."</span>", $index);
                        if($facebook)
                            $facebook = str_replace($search, "<span class=\"search\">".$search."</span>", $facebook);
                    }

                    echo '<div class="cards"><div class="logo_small"><div class="img_small"></div></div><p class="textTitle">'.$index.'번째 글
                        '.($isAdmin&&$isuploaded?"(페이스북 ".$facebook."번째 글)":"").'</p>
                        <br /><article class="text">'.$body.'</article><br />';
                    if($isuploaded){
                        echo '<a href="https://www.facebook.com/hashtag/'.$facebook_link.'%EB%B2%88%EC%A7%B8%EB%8C%80%EB%A7%88" class="facebook"></a>';
                    }else if($isAdmin){
                        echo '<div class="edit">
                        <a href="./accept.php?id='.$nn.'&accept=1">
                        <button class="ptr accept">수락</button></a>
                        <a href="./accept.php?id='.$nn.'&accept=0">
                        <button class="ptr deny">거절</button></a></div>';
                    }
                    if($isAdmin){
                        echo $ip.' / ';
                    }
                    echo $time.'에 게시됨 </div>';
                }
            }
        ?>
        <div class="pagination">
        <?php
            if($nowpage > 1){
                echo '<a class="pagelink" href="./">&laquo;</a>';
            }
            for( $i = max($nowpage - 2, 1) ; $i <= min($nowpage + 2, $lastpage) ; $i++){
                echo '<a class="pagelink'.(($nowpage == $i) ? ' active' : '').'" href="./'
                    .($i==1?'':'?'.($isSearch?"search=".$search."&":"").'page='.$i).'">'.$i.'</a>';
            }
            if($nowpage < $lastpage){
                echo '<a class="pagelink" href="./?'.($isSearch?"search=".$search."&":"").'page='.$lastpage.'">&raquo;</a>';
            }
        ?>
        </div>
    </section>
    <footer>
        
    </footer>
</body>
</html>
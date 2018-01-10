<!DOCTYPE html>
<head>
    <title>Add question</title>
</head>
<body>
    <?php
        session_start();

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // for DB Connect
        include("/home/ubuntu/.sec/secure.php");
        $conn = mysqli_connect("localhost", $dbLoginID, $dbLoginPW, "mysql");
        
        if(!$conn){
            die("con fail.. : ".mysql_error());
        }

        if(!isset($_SESSION["isAdmin"]) || !$_SESSION["isAdmin"]){
            die("You are not admin...\n</body>");
        }

        if(isset($_POST["question"])){
            $question = $_POST["question"];
            $type = $_POST["type"];
            $prefix = $_POST["prefix"];
            $suffix = $_POST["suffix"];
            $answer = $_POST["answer"];
            
            if(mysqli_query($conn, "INSERT INTO BAMBOO_QUESTIONS (QUESTION, TYPE, PREFIX, SUFFIX, ANSWER) VALUES ('$question', '$type', '$prefix', '$suffix', '$answer')")){
                echo "<h1>Success!</h1><br>";
            }else{
                echo "Error... : ".mysqli_error($conn)."<br>";
            }
        }
    ?>
    <form style="text-align: center;" action="<?php echo $_SERVER["SCRIPT_NAME"] ?>" method="post">
        <h2 style="font-size: 20px;">질문</h2><br />
        <input style="text-align: center; width: 90%; height: 30px;" type="text" name="question"><br /><br /><br />
        <h2 style="font-size: 20px;">유형(text, number 등 input 태그의 type)</h2><br />
        <input style="text-align: center; width: 90%; height: 30px;" type="text" name="type"><br /><br /><br />
        <h2 style="font-size: 20px;">답변 접두사(예 : 제~~회 할때 그 제)</h2><br />
        <input style="text-align: center; width: 90%; height: 30px;" type="text" name="prefix"><br /><br /><br />
        <h2 style="font-size: 20px;">답변 접미사(예 : ~~명 할때 그 명)</h2><br />
        <input style="text-align: center; width: 90%; height: 30px;" type="text" name="suffix"><br /><br /><br />
        <h2 style="font-size: 20px;">정답</h2><br />
        <input style="text-align: center; width: 90%; height: 30px;" type="text" name="answer"><br /><br /><br />
        <input style="text-align: center; width: 50%; height: 30px;" type="submit">
    </form>
</body>

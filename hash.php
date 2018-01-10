<!DOCTYPE html>
<head>
    <title>HASH</title>
</head>
<body>
    <form action="<?php echo $_SERVER["SCRIPT_NAME"] ?>" method="get">
        <input style="text-align: center; width: 80%;" type="password" name="input"><br>
        <input style="text-align: center; width: 80%;" type="submit" name="submit">
    </form>
    <?php
        if(isset($_REQUEST["input"])){
            echo "hash value is : ".hash('sha512', $_REQUEST["input"]);
        }
    ?>
</body>
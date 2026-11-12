<?php

session_start();

$error = "";

if (isset($_POST["login"])) {

    $password = $_POST["password"];

    // Пароль
    if ($password == "12345") {

        $_SESSION["admin"] = true;

        header("Location: admin.php");
        exit();

    } else {

        $error = "Неправильний пароль!";
    }
}

?>

<!DOCTYPE html>
<html lang="uk">

<head>

<meta charset="UTF-8">

<title>Вхід</title>

<style>

    body{

        font-family:Arial;
        background:#f5f5f5;

    }

    form{

        width:350px;
        margin:100px auto;
        background:white;
        padding:20px;
        border-radius:15px;

    }

    input{

        width:100%;
        padding:10px;
        margin-bottom:15px;
        box-sizing:border-box;

    }

    button{

        width:100%;
        padding:12px;
        background:#007bff;
        color:white;
        border:none;
        border-radius:10px;
        cursor:pointer;

    }

    .error{

        color:red;
        margin-bottom:15px;

    }

</style>

</head>

<body>

<form method="POST">

<h2>Вхід в адмінку</h2>

<?php if($error!=""): ?>

<div class="error">
    <?= $error ?>
</div>

<?php endif; ?>

<input
type="password"
name="password"
placeholder="Пароль"
required
>

<button
type="submit"
name="login"
>

Увійти

</button>

</form>

</body>

</html>
<?php

// ПІДКЛЮЧЕННЯ
$conn = new mysqli("MySQL-8.2", "root", "", "shop");
$conn->set_charset("utf8");

// ЯКЩО ФОРМА ВІДПРАВЛЕНА
if(isset($_POST["add"])){

    // Дані
    $name = $_POST["name"];
    $price = $_POST["price"];
    $description = $_POST["description"];

    // ===== ЗАВАНТАЖЕННЯ ФОТО =====

    $image = "";

    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){

        // Назва файлу
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);

        // Куди зберігати
        $uploadPath = "images/" . $fileName;

        // Переміщення файлу
        move_uploaded_file($_FILES["image"]["tmp_name"], $uploadPath);

        // Для БД
        $image = $uploadPath;
    }

    // SQL
    $sql = "INSERT INTO products(name, price, description, image)
            VALUES('$name','$price','$description','$image')";

    $conn->query($sql);

    header("Location: admin.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="uk">

<head>

<meta charset="UTF-8">

<title>Додати товар</title>

<style>

body{
    font-family: Arial;
    background:#f5f5f5;
}

form{

    width:420px;

    background:white;

    margin:50px auto;

    padding:20px;

    border-radius:15px;

}

input,
textarea{

    width:100%;

    padding:10px;

    margin-bottom:15px;

    box-sizing:border-box;

}

button{

    width:100%;

    padding:12px;

    background:green;

    color:white;

    border:none;

    border-radius:10px;

    cursor:pointer;

}

</style>

</head>

<body>

<form method="POST" enctype="multipart/form-data">

<h2>Додати товар (фото файлом)</h2>

<input
type="text"
name="name"
placeholder="Назва"
required
>

<input
type="number"
name="price"
placeholder="Ціна"
required
>

<textarea
name="description"
placeholder="Опис"
required
></textarea>

<input
type="file"
name="image"
accept="image/*"
required
>

<button
type="submit"
name="add"
>

Додати

</button>

</form>

</body>

</html>
<?php

// ПІДКЛЮЧЕННЯ
$conn = new mysqli("MySQL-8.2", "root", "", "shop");

$conn->set_charset("utf8");

// У PHP функція isset() перевіряє, чи змінна існує і не має значення null.


// isset($_POST["add"])

// це означає:

// перевірити, чи був переданий параметр "add" у масиві $_POST;
// повертає true, якщо ключ "add" існує і його значення не null;
// повертає false, якщо такого ключа немає або його значення null.

// ЯКЩО ФОРМА ВІДПРАВЛЕНА
if(isset($_POST["add"])){
// var_dump($_POST);
 //die();   негайно зупиняє виконання PHP-скрипта.

 
    // ДАНІ З INPUT
    $name = $_POST["name"];

    $price = $_POST["price"];

    $description = $_POST["description"];

    $image = $_POST["image"];

    // SQL
    $sql = "INSERT INTO products(name, price, description, image)
            VALUES('$name', '$price', '$description', '$image')";

    // ДОДАВАННЯ
    $conn->query($sql);

    // ПЕРЕХІД НАЗАД
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="uk">
<head>

    <meta charset="UTF-8">

    <title>Додавання товару</title>

    <style>

        body{

            font-family: Arial;

            background: #f5f5f5;
        }

        form{

            width: 400px;

            background: white;

            padding: 20px;

            margin: 50px auto;

            border-radius: 15px;
        }

        input, textarea{

            width: 100%;

            padding: 10px;

            margin-bottom: 15px;

            box-sizing: border-box;
        }

        button{

            width: 100%;

            padding: 12px;

            border: none;

            background: green;

            color: white;

            border-radius: 10px;
        }

    </style>

</head>
<body>

    <form method="POST">

        <h2>
            Додати товар
        </h2>

        <!-- НАЗВА -->
        <input 
            type="text"
            name="name"
            placeholder="Назва"
        >

        <!-- ЦІНА -->
        <input 
            type="number"
            name="price"
            placeholder="Ціна"
        >

        <!-- ОПИС -->
        <textarea 
            name="description"
            placeholder="Опис"
        ></textarea>

        <!-- ФОТО -->
        <input 
            type="text"
            name="image"
            placeholder="Посилання на фото"
        >

        <!-- КНОПКА -->
        <button type="submit" name="add">

            Додати

        </button>
    </form>

</body>
</html>
<?php
// Підключення до MySQL
$host = "MySQL-8.2";
$user = "root";
$password = "";
$dbname = "shop";

$conn = new mysqli($host, $user, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Помилка підключення!");
}


$id = $_GET["id"];

$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);

$product = $result->fetch_assoc();

// ЯКЩО ТОВАР НЕ ЗНАЙДЕНО — ПОВЕРТАЄМО НА ГОЛОВНУ
if (!$product) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($product['name']) ?> — Магазин</title>
<style>

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        background: #f4f5f7;
        font-family: 'Segoe UI', Arial, sans-serif;
        color: #222;
    }

    .topbar {
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        padding: 22px 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 12px rgba(0,0,0,0.15);
    }

    .topbar h1 {
        color: #fff;
        font-size: 22px;
    }

    .topbar a {
        color: #aaa;
        text-decoration: none;
        font-size: 14px;
    }

    .topbar a:hover {
        color: #fff;
    }

    .wrap {
        max-width: 900px;
        margin: 0 auto;
        padding: 40px;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 24px;
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.1);
        display: flex;
        flex-wrap: wrap;
    }

    .card img {
        width: 380px;
        max-width: 100%;
        height: 380px;
        object-fit: cover;
    }

    .info {
        flex: 1;
        min-width: 260px;
        padding: 36px 34px;
        display: flex;
        flex-direction: column;
    }

    .info .badge {
        color: #999;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .info h2 {
        font-size: 28px;
        color: #1a1a2e;
        margin-bottom: 14px;
    }

    .info .price {
        color: #16a34a;
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .info .description {
        color: #666;
        line-height: 1.7;
        margin-bottom: 28px;
        flex-grow: 1;
    }

    button.buyBtn {
        border: none;
        padding: 16px;
        background: #007bff;
        color: white;
        border-radius: 12px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: 0.2s;
    }

    button.buyBtn:hover {
        background: #0056b3;
    }

    @media (max-width: 640px) {
        .card img {
            width: 100%;
            height: 260px;
        }
    }

</style>
</head>
<body>

    <div class="topbar">
        <h1>🛍️ Магазин</h1>
        <a href="index.php">← Усі товари</a>
    </div>

    <div class="wrap">

        <a href="index.php" class="back-link">← Повернутись до каталогу</a>

        <div class="card">

            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">

            <div class="info">
                <div class="badge">Товар #<?= $product['id'] ?></div>
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <div class="price"><?= number_format($product['price'], 0, '.', ' ') ?> грн</div>
                <div class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></div>
                <button class="buyBtn" id="buyBtn">Купити</button>
            </div>

        </div>

    </div>

    <script>
        document.getElementById("buyBtn").addEventListener("click", function() {
            alert("Товар додано!");
        });
    </script>

</body>
</html>

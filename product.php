<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

// ПІДКЛЮЧЕННЯ
$conn = new mysqli("MySQL-8.2", "root", "", "shop");
$conn->set_charset("utf8");

// ОТРИМАННЯ ID ТОВАРУ З АДРЕСНОГО РЯДКА
$id = (int) ($_GET["id"] ?? 0);

// БЕЗПЕЧНИЙ ЗАПИТ ЧЕРЕЗ ПІДГОТОВЛЕНИЙ ВИРАЗ (prepared statement)
// Це надійніший захист від SQL-ін'єкцій, ніж просте (int),
// особливо якщо в майбутньому запит стане складнішим.
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// ЯКЩО ТОВАР НЕ ЗНАЙДЕНО — ПОВЕРТАЄМО НА АДМІН-ПАНЕЛЬ
if (!$product) {
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>

    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> — Адмін-панель</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #1a1a2e;
            color: #eee;
            min-height: 100vh;
        }

        header {
            background: #16213e;
            padding: 18px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #e94560;
            box-shadow: 0 2px 10px rgba(0,0,0,0.4);
        }

        header h1 {
            font-size: 22px;
            color: #e94560;
            letter-spacing: 1px;
        }

        header .nav-links a {
            color: #aaa;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
            transition: color 0.2s;
        }

        header .nav-links a:hover {
            color: #e94560;
        }

        .container {
            padding: 40px;
            max-width: 900px;
            margin: 0 auto;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 24px;
            color: #7ecfff;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .card {
            background: #16213e;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            padding: 30px;
            display: flex;
            gap: 30px;
        }

        .card img {
            width: 260px;
            height: 260px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #0f3460;
            flex-shrink: 0;
        }

        .card .info h2 {
            color: #fff;
            font-size: 26px;
            margin-bottom: 12px;
        }

        .card .info .price {
            color: #4ade80;
            font-weight: bold;
            font-size: 22px;
            margin-bottom: 16px;
        }

        .card .info .desc {
            color: #bbb;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .card .info .meta {
            color: #7ecfff;
            font-size: 13px;
            margin-bottom: 24px;
        }

        .actions a {
            display: inline-block;
            padding: 9px 20px;
            border-radius: 7px;
            font-size: 14px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
        }

        .btn-edit {
            background: #0f3460;
            color: #7ecfff;
        }

        .btn-edit:hover {
            background: #1a5299;
        }

        .btn-delete {
            background: rgba(233, 69, 96, 0.15);
            color: #e94560;
        }

        .btn-delete:hover {
            background: #e94560;
            color: white;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #444;
            font-size: 13px;
            margin-top: 40px;
        }

    </style>

</head>
<body>

    <header>
        <h1>⚙️ Адмін-панель</h1>
        <nav class="nav-links">
            <a href="index.php">← Магазин</a>
            <a href="admin.php">Усі товари</a>
        </nav>
    </header>

    <div class="container">

        <a href="admin.php" class="back-link">← Повернутись до списку товарів</a>

        <div class="card">

            <img
                src="<?= htmlspecialchars($product['image']) ?>"
                alt="<?= htmlspecialchars($product['name']) ?>"
                onerror="this.src='https://via.placeholder.com/260x260?text=?'"
            >

            <div class="info">
                <div class="meta">ID товару: #<?= $product['id'] ?></div>
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <div class="price"><?= number_format($product['price'], 0, '.', ' ') ?> грн</div>
                <div class="desc"><?= nl2br(htmlspecialchars($product['description'])) ?></div>

                <div class="actions">
                    <a href="edit.php?id=<?= $product['id'] ?>" class="btn-edit">✏️ Редагувати</a>
                    <a
                        href="admin.php?delete=<?= $product['id'] ?>"
                        class="btn-delete"
                        onclick="return confirm('Видалити товар «<?= htmlspecialchars($product['name']) ?>»?')"
                    >
                        🗑 Видалити
                    </a>
                </div>
            </div>

        </div>

    </div>

    <footer>
        Адмін-панель магазину &copy; <?= date('Y') ?>
    </footer>

</body>
</html>

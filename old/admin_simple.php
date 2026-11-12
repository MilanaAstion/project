<?php
session_start();
  // Якщо користувач не авторизований як адміністратор,
    // перенаправляємо його на сторінку входу
//     Функція повертає:
// true (істина) — якщо змінна існує і не має значення null.
// false (хиба) — якщо змінної не існує або вона дорівнює null.

if (!isset($_SESSION["admin"])) {

    header("Location: login.php");
    exit();
}
// ПІДКЛЮЧЕННЯ
$conn = new mysqli("MySQL-8.2", "root", "", "shop");
$conn->set_charset("utf8");

// ВИДАЛЕННЯ ТОВАРУ
if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"]; // (int) — захист від SQL-ін'єкцій
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: admin.php");
    exit();
}

// ОТРИМАННЯ ВСІХ ТОВАРІВ
$result = $conn->query("SELECT * FROM products");

?>

<!DOCTYPE html>
<html lang="uk">
<head>

    <meta charset="UTF-8">
    <title>Адмін-панель</title>

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

        /* ===== ХЕДЕР ===== */
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

        /* ===== ОСНОВНИЙ КОНТЕНТ ===== */
        .container {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .top-bar h2 {
            font-size: 20px;
            color: #ccc;
        }

        .top-bar span {
            background: #e94560;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 13px;
            margin-left: 10px;
        }

        .btn-add {
            background: #e94560;
            color: white;
            text-decoration: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            transition: background 0.2s;
        }

        .btn-add:hover {
            background: #c73652;
        }

        /* ===== ТАБЛИЦЯ ===== */
        .table-wrap {
            background: #16213e;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #0f3460;
        }

        thead th {
            padding: 16px 20px;
            text-align: left;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #7ecfff;
        }

        tbody tr {
            border-bottom: 1px solid #0f3460;
            transition: background 0.2s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #0f3460;
        }

        tbody td {
            padding: 14px 20px;
            font-size: 14px;
            vertical-align: middle;
        }

        /* ID колонка */
        td.col-id {
            color: #7ecfff;
            font-weight: bold;
            width: 50px;
        }

        /* Фото */
        td.col-img img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #0f3460;
        }

        /* Назва */
        td.col-name {
            font-weight: bold;
            color: #fff;
        }

        /* Ціна */
        td.col-price {
            color: #4ade80;
            font-weight: bold;
        }

        /* Опис */
        td.col-desc {
            color: #999;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Дії */
        td.col-actions {
            width: 160px;
        }

        .btn-edit, .btn-delete {
            display: inline-block;
            padding: 7px 16px;
            border-radius: 7px;
            font-size: 13px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-edit {
            background: #0f3460;
            color: #7ecfff;
            margin-right: 8px;
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

        /* ===== ПОРОЖНІЙ СТАН ===== */
        .empty {
            text-align: center;
            padding: 60px;
            color: #555;
        }

        .empty p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        /* ===== ФУТЕР ===== */
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

    <!-- ХЕДЕР -->
    <header>
        <h1>⚙️ Адмін-панель</h1>
        <nav class="nav-links">
            <a href="index.php">← Магазин</a>
            <a href="add.php">+ Додати товар з посиланням на фото</a>
            <a href="add_photo.php">+ Додати товар з фото</a>
        </nav>
    </header>

    <div class="container">

        <!-- ВЕРХНЯ ПАНЕЛЬ -->
        <div class="top-bar">
            <h2>
                Усі товари
                <span><?= $result->num_rows ?></span>
            </h2>
            <a href="add.php" class="btn-add">+ Додати товар</a>
        </div>

        <!-- ТАБЛИЦЯ -->
        <div class="table-wrap">

            <?php if ($result->num_rows > 0): ?>

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Фото</th>
                            <th>Назва</th>
                            <th>Ціна</th>
                            <th>Опис</th>
                            <th>Дії</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <tr>

                                <!-- ID -->
                                <td class="col-id">#<?= $row['id'] ?></td>

                                <!-- ФОТО -->
                                <td class="col-img">
                                    <img 
                                        src="<?= htmlspecialchars($row['image']) ?>" 
                                        alt="<?= htmlspecialchars($row['name']) ?>"
                                        onerror="this.src='https://via.placeholder.com/60x60?text=?'"
                                    >
                                </td>

                                <!-- НАЗВА -->
                                <td class="col-name"><?= htmlspecialchars($row['name']) ?></td>

                                <!-- ЦІНА -->
                                <td class="col-price"><?= number_format($row['price'], 0, '.', ' ') ?> грн</td>

                                <!-- ОПИС -->
                                <td class="col-desc" title="<?= htmlspecialchars($row['description']) ?>">
                                    <?= htmlspecialchars($row['description']) ?>
                                </td>

                                <!-- ДІЇ -->
                                <td class="col-actions">

                                    <!-- РЕДАГУВАННЯ -->
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">
                                        ✏️ Змінити
                                    </a>

                                    <!-- ВИДАЛЕННЯ з підтвердженням -->
                                    <a 
                                        href="admin.php?delete=<?= $row['id'] ?>" 
                                        class="btn-delete"
                                        onclick="return confirm('Видалити товар «<?= htmlspecialchars($row['name']) ?>»?')"
                                    >
                                        🗑
                                    </a>
                                    <!-- confirm(...) Стандартна функція JavaScript, яка показує діалогове вікно з кнопками OK і Скасувати. -->
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <!-- ЯКЩО ТОВАРІВ НЕМАЄ -->
                <div class="empty">
                    <p>Товарів поки немає</p>
                    <a href="add.php" class="btn-add">Додати перший товар</a>
                </div>

            <?php endif; ?>

        </div>

    </div>

    <footer>
        Адмін-панель магазину &copy; <?= date('Y') ?>
    </footer>

</body>
</html>
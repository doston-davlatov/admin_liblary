<?php
require_once './config.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: books.php?msg=So‘rov noto‘liq!");
    exit;
}

$bookId = intval($_GET['id']);
$action = $_GET['action'];
$db = new Database();

if ($action === 'confirm') {
    $result = $db->update("books", ["is_confirmed" => 1], "id=?", [$bookId], "i");
    $msg = "Kitob muvaffaqiyatli tasdiqlandi!";
} elseif ($action === 'unconfirm') {
    $result = $db->update("books", ["is_confirmed" => 0], "id=?", [$bookId], "i");
    $msg = "Kitob tasdiqlanishi bekor qilindi!";
} else {
    header("Location: books.php?msg=Amal noto‘g‘ri belgilangan!");
    exit;
}

if (is_string($result)) {
    header("Location: books.php?msg=Amalda xatolik: $result");
} else {
    header("Location: books.php?msg=$msg");
}
exit;

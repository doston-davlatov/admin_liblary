<?php
require_once './config.php';

$db = new Database();

if (!isset($_GET['id'])) {
    header("Location: books.php?msg=ID topilmadi!");
    exit;
}

$bookId = intval($_GET['id']);

$bookData = $db->select("books", "cover_image", "id=?", [$bookId], "i");

if (!$bookData) {
    header("Location: books.php?msg=Kitob topilmadi!");
    exit;
}

$coverImage = $bookData[0]['cover_image'];

if ($coverImage && file_exists(__DIR__ . '/./assets/images/' . $coverImage)) {
    unlink(__DIR__ . '/./assets/images/' . $coverImage);
}

$result = $db->delete("books", "id=?", [$bookId], "i");

if (is_string($result)) {
    header("Location: books.php?msg=O‘chirishda xatolik: $result");
} else {
    header("Location: books.php?msg=Kitob va rasmi o‘chirildi!");
}
exit;

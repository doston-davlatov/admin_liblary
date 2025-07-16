<?php
require_once './config.php';

$db = new Database();
$id = intval($_GET['id'] ?? 0);

$librarian = $db->select("librarians", "*", "id=?", [$id]);

if ($librarian) {
    $librarian = $librarian[0];

    if (!empty($librarian['photo'])) {
        $photoPath = __DIR__ . "/assets/images/" . $librarian['photo'];
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }

    $db->delete("librarians", "id=?", [$id]);
}

header("Location: librarians.php?msg=Kutubxonachi va uning rasmi o‘chirildi!");
exit;

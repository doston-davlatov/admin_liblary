<?php
require_once './config.php';

$db = new Database();

if (!isset($_GET['id'])) {
    header("Location: events.php?msg=ID topilmadi!");
    exit;
}

$eventId = intval($_GET['id']);
$result = $db->delete("events", "id=?", [$eventId], "i");

if (is_string($result)) {
    header("Location: events.php?msg=O‘chirishda xatolik: $result");
} else {
    header("Location: events.php?msg=Tadbir o‘chirildi!");
}
exit;

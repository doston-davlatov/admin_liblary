<?php
require_once './config.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: events.php?msg=So‘rov noto‘liq!");
    exit;
}

$eventId = intval($_GET['id']);
$action = $_GET['action'];
$db = new Database();

if ($action === 'confirm') {
    $result = $db->update("events", ["is_confirmed" => 1], "id=?", [$eventId], "i");
    $msg = "Tadbir muvaffaqiyatli tasdiqlandi!";
} elseif ($action === 'unconfirm') {
    $result = $db->update("events", ["is_confirmed" => 0], "id=?", [$eventId], "i");
    $msg = "Tadbir tasdiqlanishi bekor qilindi!";
} else {
    header("Location: events.php?msg=Amal noto‘g‘ri belgilangan!");
    exit;
}

if (is_string($result)) {
    header("Location: events.php?msg=Amalda xatolik: $result");
} else {
    header("Location: events.php?msg=$msg");
}
exit;

<?php
require_once './config.php';
require_once './template/header.php';

$db = new Database();

$event = null;
if (isset($_GET['id'])) {
    $eventId = intval($_GET['id']);
    $eventData = $db->select("events", "*", "id=?", [$eventId], "i");
    if ($eventData) {
        $event = $eventData[0];
    } else {
        echo "<div class='alert alert-danger'>Tadbir topilmadi!</div>";
        require_once './template/footer.php'; exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $location = trim($_POST['location']);
    $status = $_POST['status'];

    if ($event) {
        $db->update("events", [
            "title" => $title,
            "description" => $description,
            "event_date" => $event_date,
            "location" => $location,
            "status" => $status
        ], "id=?", [$eventId], "i");

        header("Location: events.php?msg=Tadbir yangilandi!");
    } else {
        $db->insert("events", [
            "title" => $title,
            "description" => $description,
            "event_date" => $event_date,
            "location" => $location,
            "status" => $status
        ]);

        header("Location: events.php?msg=Yangi tadbir qo‘shildi!");
    }
    exit;
}
?>
<div class="container my-4">
    <h2 class="mb-3"><?= $event ? "Tadbirni tahrirlash" : "Yangi tadbir qo‘shish" ?></h2>
    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Sarlavha</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($event['title'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Sana va vaqt</label>
            <input type="datetime-local" name="event_date" class="form-control" required
                   value="<?= isset($event['event_date']) ? date('Y-m-d\TH:i', strtotime($event['event_date'])) : '' ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Manzil</label>
            <input type="text" name="location" class="form-control" required value="<?= htmlspecialchars($event['location'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="upcoming" <?= (isset($event['status']) && $event['status']=='upcoming')?'selected':'' ?>>Upcoming</option>
                <option value="past" <?= (isset($event['status']) && $event['status']=='past')?'selected':'' ?>>Past</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Tavsif</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?= $event ? "Saqlash" : "Qo‘shish" ?></button>
            <a href="events.php" class="btn btn-secondary">Orqaga</a>
        </div>
    </form>
</div>
<?php require_once './template/footer.php'; ?>

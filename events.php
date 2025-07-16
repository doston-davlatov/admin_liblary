<?php
require_once './config.php';
require_once './template/header.php';

$db = new Database();

// Tadbirlar ro‘yxatini olish
$events = $db->select("events", "*", "", [], "", "ORDER BY event_date DESC");

// Xabar ko‘rsatish
if (isset($_GET['msg'])) {
    echo "<div class='alert alert-success'>".htmlspecialchars($_GET['msg'])."</div>";
}
?>
<div class="container my-4">
    <h2 class="mb-3">Tadbirlar ro‘yxati</h2>
    <a href="create_event.php" class="btn btn-success mb-3"><i class="bi bi-plus-lg"></i> Yangi tadbir qo‘shish</a>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Sarlavha</th>
                <th>Sana</th>
                <th>Status</th>
                <th>Tasdiq</th>
                <th>Amallar</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($events): ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['id']) ?></td>
                        <td><?= htmlspecialchars($event['title']) ?></td>
                        <td><?= htmlspecialchars($event['event_date']) ?></td>
                        <td>
                            <span class="badge <?= $event['status']=='upcoming'?'bg-primary':'bg-secondary' ?>">
                                <?= htmlspecialchars($event['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($event['is_confirmed']): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Tasdiqlangan</span>
                                <a href="confirm_event.php?id=<?= $event['id'] ?>&action=unconfirm" class="btn btn-outline-warning btn-sm ms-2" onclick="return confirm('Tasdiqni bekor qilmoqchimisiz?');">
                                    <i class="bi bi-x-circle"></i> Bekor qilish
                                </a>
                            <?php else: ?>
                                <a href="confirm_event.php?id=<?= $event['id'] ?>&action=confirm" class="btn btn-warning btn-sm" onclick="return confirm('Tadbirni tasdiqlashni istaysizmi?');">
                                    <i class="bi bi-patch-check"></i> Tasdiqlash
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil"></i> Tahrirlash
                            </a>
                            <a href="delete_event.php?id=<?= $event['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Haqiqatan o‘chirishni istaysizmi?');">
                                <i class="bi bi-trash"></i> O‘chirish
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Tadbirlar mavjud emas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require_once './template/footer.php'; ?>

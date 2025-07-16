<?php
require_once './config.php';
require_once './template/header.php';

$db = new Database();

// Foydalanuvchilar ro'yxatini olish
$users = $db->select("users", "*", "", [], "", "ORDER BY id DESC");

// Xabar ko'rsatish
if (isset($_GET['msg'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            '.htmlspecialchars($_GET['msg']).'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}
?>

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><i class="bi bi-people me-2"></i>Foydalanuvchilar boshqaruvi</h3>
                <div>
                    <a href="edit_user.php" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Yangi foydalanuvchi
                    </a>
                    <button class="btn btn-light btn-sm ms-2" onclick="window.print()">
                        <i class="bi bi-printer"></i> Chop etish
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">ID</th>
                            <th>Foydalanuvchi</th>
                            <th>Aloqa</th>
                            <th>Rol</th>
                            <th width="150">Holat</th>
                            <th width="120">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($user['id']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-3">
                                                <span class="symbol-label bg-light-primary text-primary fs-4 fw-bold">
                                                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($user['username']) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($user['full_name'] ?? 'Noma\'lum') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-primary"><?= htmlspecialchars($user['email']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($user['phone'] ?? '---') ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                        $role_badge = [
                                            'admin' => 'danger',
                                            'moderator' => 'warning',
                                            'user' => 'primary',
                                            'librarian' => 'info'
                                        ];
                                        $color = $role_badge[strtolower($user['role'])] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= htmlspecialchars($user['role']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($user['is_active'] == 1): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Faol</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i> Nofaol</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="edit_user.php?id=<?= $user['id'] ?>" 
                                               class="btn btn-outline-primary" 
                                               data-bs-toggle="tooltip" 
                                               title="Tahrirlash">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="delete_user.php?id=<?= $user['id'] ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Ushbu foydalanuvchini o\'chirishni tasdiqlaysizmi?');"
                                               data-bs-toggle="tooltip" 
                                               title="O'chirish">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3 text-muted">Foydalanuvchilar topilmadi</h5>
                                        <p class="text-muted mb-4">Yangi foydalanuvchi qo'shish uchun quyidagi tugmadan foydalaning</p>
                                        <a href="edit_user.php" class="btn btn-primary">
                                            <i class="bi bi-plus-lg me-1"></i> Foydalanuvchi qo'shish
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Jami: <?= count($users) ?> ta foydalanuvchi</small>
                <div>
                    <select class="form-select form-select-sm w-auto d-inline-block">
                        <option>10 ta yozuv</option>
                        <option>25 ta yozuv</option>
                        <option>50 ta yozuv</option>
                        <option>Barchasi</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tooltip'larni faollashtirish
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
})
</script>

<?php require_once './template/footer.php'; ?>
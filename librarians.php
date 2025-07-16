<?php
session_start();
require_once './config.php';
require_once './template/header.php';

$db = new Database();

// Kutubxonachilar ro'yxatini olish
$sql = "SELECT * FROM librarians ORDER BY created_at DESC";
$result = $db->executeQuery($sql);

if (is_string($result)) {
    echo "<div class='alert alert-danger'>Xatolik: " . htmlspecialchars($result) . "</div>";
    require_once './template/footer.php'; exit;
}

$librarians = $result->get_result()->fetch_all(MYSQLI_ASSOC);

// Xabar ko'rsatish
if (isset($_GET['msg'])) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>"
        . htmlspecialchars($_GET['msg']) .
        "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
}
?>

<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><i class="bi bi-people-fill me-2"></i>Kutubxonachilar ro'yxati</h3>
                <a href="create_librarian.php" class="btn btn-light">
                    <i class="bi bi-plus-circle me-1"></i> Yangi qo'shish
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th width="50">ID</th>
                            <th width="100">Rasm</th>
                            <th>Ismi</th>
                            <th>Lavozimi</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th width="180">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($librarians): ?>
                            <?php foreach ($librarians as $librarian): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($librarian['id']) ?></td>
                                    <td>
                                        <?php if (!empty($librarian['photo'])): ?>
                                            <img src="assets/images/<?= htmlspecialchars($librarian['photo']) ?>" 
                                                 alt="<?= htmlspecialchars($librarian['name']) ?>" 
                                                 class="rounded-circle shadow" 
                                                 width="60" 
                                                 height="60"
                                                 style="object-fit: cover">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px;">
                                                <i class="bi bi-person text-white" style="font-size: 1.5rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($librarian['name']) ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?= htmlspecialchars($librarian['position']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($librarian['email']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($librarian['email']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:<?= htmlspecialchars($librarian['phone']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($librarian['phone']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="edit_librarian.php?id=<?= $librarian['id'] ?>" 
                                               class="btn btn-outline-primary"
                                               data-bs-toggle="tooltip" 
                                               title="Tahrirlash">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="delete_librarian.php?id=<?= $librarian['id'] ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Haqiqatan o\'chirishni istaysizmi?');"
                                               data-bs-toggle="tooltip" 
                                               title="O'chirish">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <a href="librarian_details.php?id=<?= $librarian['id'] ?>" 
                                               class="btn btn-outline-secondary"
                                               data-bs-toggle="tooltip" 
                                               title="Batafsil">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 2rem;"></i>
                                        <h5 class="mt-3 text-muted">Kutubxonachilar topilmadi</h5>
                                        <p class="text-muted">Yangi kutubxonachi qo'shish uchun "Yangi qo'shish" tugmasini bosing</p>
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
                <small class="text-muted">Jami: <?= count($librarians) ?> ta kutubxonachi</small>
                <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Chop etish
                </button>
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
<?php
require_once './config.php';
require_once './template/header.php';

$db = new Database();

// Kitoblar ro'yxatini olish
$sql = "SELECT books.*, 
               authors.name AS author_name, 
               publishers.name AS publisher_name, 
               genres.name AS genre_name 
        FROM books 
        LEFT JOIN authors ON books.author_id = authors.id 
        LEFT JOIN publishers ON books.publisher_id = publishers.id 
        LEFT JOIN genres ON books.genre_id = genres.id 
        ORDER BY books.id DESC";

$result = $db->executeQuery($sql);
if (is_string($result)) {
    echo "<div class='alert alert-danger'>Xatolik: " . htmlspecialchars($result) . "</div>";
    require_once './template/footer.php';
    exit;
}
$books = $result->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        --success-gradient: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --card-hover-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        --transition: all 0.3s ease;
    }
    
    .books-container {
        margin-top: 20px;
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .page-title {
        font-weight: 700;
        color: #1e293b;
        position: relative;
        padding-bottom: 10px;
    }
    
    .page-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 4px;
        background: var(--primary-gradient);
        border-radius: 2px;
    }
    
    .books-table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        border: none;
    }
    
    .books-table thead {
        background: var(--primary-gradient);
    }
    
    .books-table th {
        padding: 16px 20px;
        font-weight: 600;
        vertical-align: middle;
        color: white;
        border-bottom: none;
    }
    
    .books-table td {
        padding: 14px 20px;
        vertical-align: middle;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .books-table tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }
    
    .book-image {
        width: 50px;
        height: 70px;
        object-fit: cover;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: var(--transition);
    }
    
    .book-image:hover {
        transform: scale(1.8);
        z-index: 10;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: var(--transition);
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
    }
    
    .add-book-btn {
        background: var(--success-gradient);
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        transition: var(--transition);
    }
    
    .add-book-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
    }
    
    .empty-state {
        padding: 40px 0;
        text-align: center;
        color: #64748b;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #cbd5e1;
    }
    
    .description-text {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    @media (max-width: 992px) {
        .books-table {
            display: block;
            overflow-x: auto;
        }
    }
</style>

<div class="container books-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title mb-0"><i class="bi bi-book me-2"></i>Kitoblar ro'yxati</h2>
        <a href="create_book.php" class="btn add-book-btn text-white">
            <i class="bi bi-plus-lg me-1"></i> Yangi kitob
        </a>
    </div>
    
    <div class="card border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table books-table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kitob</th>
                            <th>Muallif</th>
                            <th>Nashriyot</th>
                            <th>Janr</th>
                            <th>Holati</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($books): ?>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?= htmlspecialchars($book['id']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($book['image']): ?>
                                                <img src="<?= htmlspecialchars($book['image']) ?>" alt="Kitob rasmi" class="book-image me-3">
                                            <?php else: ?>
                                                <div class="book-image bg-light d-flex align-items-center justify-content-center me-3">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= htmlspecialchars($book['title']) ?></strong>
                                                <div class="text-muted small">ISBN: <?= htmlspecialchars($book['isbn'] ?? '-') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($book['author_name'] ?? 'Noma\'lum') ?></td>
                                    <td><?= htmlspecialchars($book['publisher_name'] ?? 'Noma\'lum') ?></td>
                                    <td><?= htmlspecialchars($book['genre_name'] ?? 'Noma\'lum') ?></td>
                                    <td>
                                        <?php if ($book['is_confirmed']): ?>
                                            <span class="status-badge bg-success bg-opacity-10 text-success">
                                                <i class="bi bi-check-circle-fill me-1"></i>Tasdiqlangan
                                            </span>
                                        <?php else: ?>
                                            <a href="confirm_book.php?id=<?= $book['id'] ?>&action=confirm" 
                                               class="status-badge bg-warning bg-opacity-10 text-warning"
                                               onclick="return confirm('Kitobni tasdiqlashni istaysizmi?');">
                                                <i class="bi bi-exclamation-circle-fill me-1"></i>Tasdiqlash
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_book.php?id=<?= $book['id'] ?>" 
                                               class="action-btn btn btn-primary btn-sm"
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Tahrirlash">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="delete_book.php?id=<?= $book['id'] ?>" 
                                               class="action-btn btn btn-danger btn-sm"
                                               onclick="return confirm('Haqiqatan o\'chirishni istaysizmi?');"
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="O'chirish">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <?php if ($book['is_confirmed']): ?>
                                                <a href="confirm_book.php?id=<?= $book['id'] ?>&action=unconfirm" 
                                                   class="action-btn btn btn-warning btn-sm"
                                                   onclick="return confirm('Tasdiqni bekor qilmoqchimisiz?');"
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top" 
                                                   title="Tasdiqni bekor qilish">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-book"></i>
                                        <h5 class="mb-2">Kitoblar topilmadi</h5>
                                        <p class="mb-0">Kitob qo'shish uchun "Yangi kitob" tugmasini bosing</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Enable Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Book image hover effect
        var bookImages = document.querySelectorAll('.book-image');
        bookImages.forEach(function(img) {
            img.addEventListener('mouseenter', function() {
                var bounding = this.getBoundingClientRect();
                if (bounding.right > window.innerWidth - 200) {
                    this.style.transformOrigin = 'left center';
                } else {
                    this.style.transformOrigin = 'center center';
                }
            });
        });
    });
</script>

<?php require_once './template/footer.php'; ?>
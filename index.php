<?php
require_once './config.php';
require_once './template/header.php';

$db = new Database();

// Statistika uchun ma’lumotlar
$bookCount = $db->select("books", "COUNT(*) AS total")[0]['total'];
$userCount = $db->select("users", "COUNT(*) AS total")[0]['total'];
$borrowingCount = $db->select("borrowings", "COUNT(*) AS total")[0]['total'];
$eventCount = $db->select("events", "COUNT(*) AS total")[0]['total'];
?>

<style>
    :root {
        --primary-color: #4361ee;
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --card-hover-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        --transition: all 0.3s ease;
    }
    
    .dashboard-container {
        margin-top: 20px;
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: var(--transition);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--card-hover-shadow) !important;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        z-index: -1;
    }
    
    .stat-card .card-body {
        padding: 1.5rem;
    }
    
    .stat-card .card-title {
        font-size: 1rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .stat-card .card-text {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    .stat-card .card-footer {
        background: rgba(0, 0, 0, 0.1);
        border-top: none;
        padding: 0.75rem 1.5rem;
    }
    
    .feature-card {
        border: none;
        border-radius: 12px;
        transition: var(--transition);
        box-shadow: var(--card-shadow);
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--card-hover-shadow);
    }
    
    .feature-card .card-body {
        padding: 1.75rem;
    }
    
    .feature-card .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .feature-card .card-text {
        color: #666;
        margin-bottom: 1.5rem;
    }
    
    .dashboard-title {
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
        display: inline-block;
    }
    
    .dashboard-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 50px;
        height: 4px;
        background: var(--primary-color);
        border-radius: 2px;
    }
    
    .section-divider {
        border: none;
        height: 1px;
        background: linear-gradient(90deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0) 100%);
        margin: 3rem 0;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
        
        .stat-card .card-text {
            font-size: 1.75rem;
        }
    }
</style>

<div class="container dashboard-container">
    <h1 class="dashboard-title">📊 Boshqaruv paneli</h1>

    <div class="row g-4">
        <!-- Kitoblar statistikasi -->
        <div class="col-md-3 col-sm-6">
            <div class="card text-white stat-card" style="background-color: var(--primary-color); box-shadow: var(--card-shadow);">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-book me-2"></i>Kitoblar</h5>
                    <p class="card-text"><?= number_format($bookCount) ?></p>
                </div>
                <div class="card-footer text-end">
                    <a href="books.php" class="text-white text-decoration-none d-flex align-items-center justify-content-end">
                        Batafsil <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Foydalanuvchilar statistikasi -->
        <div class="col-md-3 col-sm-6">
            <div class="card text-white stat-card" style="background-color: var(--success-color); box-shadow: var(--card-shadow);">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people me-2"></i>Foydalanuvchilar</h5>
                    <p class="card-text"><?= number_format($userCount) ?></p>
                </div>
                <div class="card-footer text-end">
                    <a href="users.php" class="text-white text-decoration-none d-flex align-items-center justify-content-end">
                        Batafsil <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Testlar statistikasi -->
        <div class="col-md-3 col-sm-6">
            <div class="card text-white stat-card" style="background-color: var(--warning-color); box-shadow: var(--card-shadow);">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-file-earmark-text me-2"></i>Testlar</h5>
                    <p class="card-text"><?= number_format($borrowingCount) ?></p>
                </div>
                <div class="card-footer text-end">
                    <a href="borrowings.php" class="text-white text-decoration-none d-flex align-items-center justify-content-end">
                        Batafsil <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Tadbirlar statistikasi -->
        <div class="col-md-3 col-sm-6">
            <div class="card text-white stat-card" style="background-color: var(--danger-color); box-shadow: var(--card-shadow);">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-calendar-event me-2"></i>Tadbirlar</h5>
                    <p class="card-text"><?= number_format($eventCount) ?></p>
                </div>
                <div class="card-footer text-end">
                    <a href="events.php" class="text-white text-decoration-none d-flex align-items-center justify-content-end">
                        Batafsil <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <hr class="section-divider">

    <div class="row g-4">
        <!-- Kitoblar boshqaruvi -->
        <div class="col-md-6">
            <div class="card h-100 feature-card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-book text-primary me-2"></i> Kitoblarni boshqarish</h5>
                    <p class="card-text">Kutubxonadagi kitoblarni qo'shish, tahrirlash, o'chirish va ularni kategoriyalar bo'yicha boshqarish.</p>
                    <a href="books.php" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-gear me-2"></i> Boshqarish
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Tadbirlar boshqaruvi -->
        <div class="col-md-6">
            <div class="card h-100 feature-card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-calendar-event text-danger me-2"></i> Tadbirlarni boshqarish</h5>
                    <p class="card-text">Kutubxona tadbirlarini yaratish, tahrirlash va ularga ishtirokchilarni ro'yxatga olish.</p>
                    <a href="events.php" class="btn btn-danger px-4 py-2">
                        <i class="bi bi-gear me-2"></i> Boshqarish
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional sections can be added here -->
</div>

<?php require_once './template/footer.php'; ?>
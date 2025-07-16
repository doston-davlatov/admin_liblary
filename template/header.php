<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | Kutubxona</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="favicon.ico" />
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #1e293b;
            --sidebar-active-bg: #0f172a;
            --sidebar-active-color: #fcd34d;
            --header-height: 60px;
        }
        
        body { 
            background-color: #f4f6f9; 
            padding-top: var(--header-height);
        }
        
        /* Sidebar styles */
        .sidebar { 
            background-color: var(--sidebar-bg); 
            min-height: calc(100vh - var(--header-height));
            width: var(--sidebar-width);
            position: fixed;
            top: var(--header-height);
            left: 0;
            transition: transform 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar a { 
            color: #fff; 
            transition: all 0.2s;
        }
        
        .sidebar a:hover, 
        .sidebar a.active { 
            background-color: var(--sidebar-active-bg); 
            color: var(--sidebar-active-color); 
            padding-left: 15px;
        }
        
        /* Main content area */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin 0.3s ease;
            width: calc(100% - var(--sidebar-width));
        }
        
        /* Header styles */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            z-index: 1030;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Mobile menu button */
        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
            margin-right: 15px;
            background: none;
            border: none;
            color: #333;
        }
        
        /* Close button for mobile sidebar */
        .close-sidebar {
            display: none;
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 1.5rem;
            color: white;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }
        
        /* Hide panel title on mobile */
        .panel-title {
            display: inline;
        }
        
        .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .close-sidebar {
                display: block;
            }
            
            .panel-title {
                display: none;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: var(--header-height);
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-overlay.open {
                display: block;
            }
        /* Responsive styles */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .close-sidebar {
                display: block;
            }
            
            .panel-title {
                display: none;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: var(--header-height);
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-overlay.open {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header navbar navbar-expand-lg navbar-light border-bottom px-4">
        <div class="container-fluid">
            <button class="menu-toggle">
                <i class="bi bi-list"></i>
            </button>
            <span class="navbar-brand mb-0 h1 panel-title">Kutubxona Admin Panel</span>
            <div class="ms-auto">
                <span class="me-3"><i class="bi bi-person-circle me-1"></i> Admin</span>
                <a href="logout/" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Chiqish</a>
            </div>
        </div>
    </header>
    
    <!-- Sidebar Overlay (for mobile) -->
    <div class="sidebar-overlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <button class="close-sidebar">&times;</button>
        <h3 class="text-white mb-4"><i class="bi bi-book-half me-2"></i>Admin</h3>
        <ul class="nav flex-column">
            <li class="nav-item mb-2"><a href="index.php" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Boshqaruv paneli</a></li>
            <li class="nav-item mb-2"><a href="books.php" class="nav-link"><i class="bi bi-book me-2"></i>Kitoblar</a></li>
            <li class="nav-item mb-2"><a href="librarians.php" class="nav-link"><i class="bi bi-person-badge me-2"></i>Kutubxonachilar</a></li>
            <li class="nav-item mb-2"><a href="users.php" class="nav-link"><i class="bi bi-people me-2"></i>Foydalanuvchilar</a></li>
            <li class="nav-item mb-2"><a href="borrowings.php" class="nav-link"><i class="bi bi-file-earmark-text me-2"></i>Testlar</a></li>
            <li class="nav-item mb-2"><a href="events.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i>Tadbirlar</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <main class="main-content p-4">
        <!-- Your page content goes here -->
        
        <!-- JavaScript for responsive sidebar -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const menuToggle = document.querySelector('.menu-toggle');
                const sidebar = document.querySelector('.sidebar');
                const closeSidebar = document.querySelector('.close-sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                
                // Toggle sidebar on menu button click
                menuToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.add('open');
                    overlay.classList.add('open');
                });
                
                // Close sidebar on close button click
                closeSidebar.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                });
                
                // Close sidebar when clicking on overlay
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                });
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    if (window.innerWidth <= 992 && 
                        !sidebar.contains(event.target) && 
                        event.target !== menuToggle) {
                        sidebar.classList.remove('open');
                        overlay.classList.remove('open');
                    }
                });
            });
        </script>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
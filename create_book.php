<?php
require_once './config.php';
$db = new Database();

// Fetch existing data
$authors = $db->select("authors", "id, name");
$publishers = $db->select("publishers", "id, name");
$genres = $db->select("genres", "id, name");

// Add new author
if (isset($_POST['save_author']) && !empty($_POST['new_author'])) {
    $newAuthorName = trim($_POST['new_author']);
    $exists = $db->select("authors", "id", "name=?", [$newAuthorName], "s");
    
    if (!$exists) {
        $inserted = $db->insert("authors", ["name" => $newAuthorName]);
        if ($inserted) {
            header("Location: create_book.php?msg=Yangi muallif qo'shildi!");
        } else {
            header("Location: create_book.php?error=Muallif qo'shishda xatolik!");
        }
    } else {
        header("Location: create_book.php?error=Bu muallif allaqachon mavjud!");
    }
    exit;
}

// Add new publisher
if (isset($_POST['save_publisher']) && !empty($_POST['new_publisher'])) {
    $newPublisherName = trim($_POST['new_publisher']);
    $exists = $db->select("publishers", "id", "name=?", [$newPublisherName], "s");
    
    if (!$exists) {
        $inserted = $db->insert("publishers", ["name" => $newPublisherName]);
        if ($inserted) {
            header("Location: create_book.php?msg=Yangi nashriyot qo'shildi!");
        } else {
            header("Location: create_book.php?error=Nashriyot qo'shishda xatolik!");
        }
    } else {
        header("Location: create_book.php?error=Bu nashriyot allaqachon mavjud!");
    }
    exit;
}

// Add new genre
if (isset($_POST['save_genre']) && !empty($_POST['new_genre'])) {
    $newGenreName = trim($_POST['new_genre']);
    $exists = $db->select("genres", "id", "name=?", [$newGenreName], "s");

    if (!$exists) {
        $inserted = $db->insert("genres", ["name" => $newGenreName]);
        if ($inserted) {
            header("Location: create_book.php?msg=Yangi janr qo'shildi!");
        } else {
            header("Location: create_book.php?error=Janr qo'shishda xatolik!");
        }
    } else {
        header("Location: create_book.php?error=Bu janr allaqachon mavjud!");
    }
    exit;
}

// Handle book creation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $author_id = intval($_POST['author_id']);
    $publisher_id = intval($_POST['publisher_id']);
    $genre_id = intval($_POST['genre_id']);
    $isbn = trim($_POST['isbn']);
    $year = intval($_POST['year']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $is_confirmed = isset($_POST['is_confirmed']) ? 1 : 0;
    $image = $_FILES['image'];
    
    $errors = [];
    if (empty($title)) $errors[] = "Kitob nomi kiritilishi shart!";
    if ($author_id <= 0) $errors[] = "Muallif tanlanmadi!";
    if ($publisher_id <= 0) $errors[] = "Nashriyot tanlanmadi!";
    if ($genre_id <= 0) $errors[] = "Janr tanlanmadi!";
    if ($year < 500 || $year > date('Y')) $errors[] = "Yil noto'g'ri kiritildi!";
    if ($quantity < 1) $errors[] = "Miqdor 1 yoki undan ko'p bo'lishi kerak!";
    if (!empty($isbn) && !preg_match('/^[0-9-]{10,20}$/', $isbn)) $errors[] = "ISBN formati noto'g'ri!";

    $imagePath = '';
    if (!empty($image['name'])) {
        $uploadDir = './assets/images/books/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $fileExt = pathinfo($image['name'], PATHINFO_EXTENSION);
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array(strtolower($fileExt), $allowedExts)) {
            $imageName = uniqid('book_') . '.' . $fileExt;
            $uploadPath = $uploadDir . $imageName;
            
            if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
                $imagePath = $uploadPath;
            } else {
                $errors[] = "Rasm yuklashda xatolik!";
            }
        } else {
            $errors[] = "Faqat JPG, JPEG, PNG va GIF rasmlar yuklanishi mumkin!";
        }
    }
    
    if (empty($errors)) {
        $data = [
            'title' => $title,
            'author_id' => $author_id,
            'publisher_id' => $publisher_id,
            'genre_id' => $genre_id,
            'isbn' => $isbn ?: null,
            'year' => $year,
            'description' => $description ?: null,
            'quantity' => $quantity,
            'is_confirmed' => $is_confirmed,
            'image' => $imagePath ?: null
        ];
        
        $inserted = $db->insert('books', $data);
        
        if ($inserted) {
            header("Location: books.php?msg=Kitob muvaffaqiyatli qo'shildi!");
            exit;
        } else {
            $errors[] = "Kitob qo'shishda xatolik! Iltimos, ma'lumotlarni tekshiring.";
        }
    }
}
require_once './template/header.php';
?>

<style>
    :root {
        --primary-color: #3b82f6;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --secondary-color: #64748b;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }
    
    .book-form-container {
        margin-top: 20px;
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .form-title {
        font-weight: 700;
        color: #1e293b;
        position: relative;
        padding-bottom: 10px;
        margin-bottom: 25px;
    }
    
    .form-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color) 0%, #1d4ed8 100%);
        border-radius: 2px;
    }
    
    .form-card {
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: none;
        padding: 25px;
        background: white;
    }
    
    .form-label {
        font-weight: 500;
        color: #334155;
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #e2e8f0;
        transition: var(--transition);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
    }
    
    textarea.form-control {
        min-height: 120px;
    }
    
    .submit-btn {
        background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: var(--transition);
        color: white;
    }
    
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        color: white;
    }
    
    .cancel-btn {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: var(--transition);
        color: var(--secondary-color);
    }
    
    .cancel-btn:hover {
        background: #f8fafc;
        color: var(--secondary-color);
    }
    
    .add-new-btn {
        border-radius: 8px;
        transition: var(--transition);
    }
    
    .add-new-btn:hover {
        background-color: #f1f5f9;
    }
    
    .image-preview-container {
        margin-top: 15px;
        display: none;
    }
    
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        border: 2px dashed #e2e8f0;
        padding: 5px;
    }
    
    .file-upload {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
    }
    
    .file-upload:hover {
        border-color: var(--primary-color);
        background-color: rgba(59, 130, 246, 0.05);
    }
    
    .upload-icon {
        font-size: 2rem;
        color: #94a3b8;
        margin-bottom: 10px;
    }
    
    .file-input {
        display: none;
    }
    
    .form-check-input:checked {
        background-color: var(--success-color);
        border-color: var(--success-color);
    }
</style>

<div class="container book-form-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-card">
                <h2 class="form-title"><i class="bi bi-book me-2"></i>Yangi kitob qo'shish</h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-octagon me-2"></i>
                        <?php foreach ($errors as $error): ?>
                            <?= htmlspecialchars($error) ?><br>
                        <?php endforeach; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= htmlspecialchars($_GET['msg']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-octagon me-2"></i>
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="post" class="row g-3" enctype="multipart/form-data">
                    <!-- Basic Information -->
                    <div class="col-md-8">
                        <label for="title" class="form-label">Kitob nomi</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="isbn" class="form-label">ISBN (ixtiyoriy)</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" maxlength="20" placeholder="Masalan: 978-3-16-148410-0">
                    </div>
                    
                    <!-- Relationships -->
                    <div class="col-md-4">
                        <label class="form-label">Muallif</label>
                        <div class="input-group">
                            <select name="author_id" class="form-select" required>
                                <option value="">-- Tanlang --</option>
                                <?php foreach ($authors as $author): ?>
                                    <option value="<?= $author['id'] ?>"><?= htmlspecialchars($author['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn add-new-btn" data-bs-toggle="modal" data-bs-target="#addAuthorModal">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Nashriyot</label>
                        <div class="input-group">
                            <select name="publisher_id" class="form-select" required>
                                <option value="">-- Tanlang --</option>
                                <?php foreach ($publishers as $publisher): ?>
                                    <option value="<?= $publisher['id'] ?>"><?= htmlspecialchars($publisher['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn add-new-btn" data-bs-toggle="modal" data-bs-target="#addPublisherModal">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Janr</label>
                        <div class="input-group">
                            <select name="genre_id" class="form-select" required>
                                <option value="">-- Tanlang --</option>
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?= $genre['id'] ?>"><?= htmlspecialchars($genre['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn add-new-btn" data-bs-toggle="modal" data-bs-target="#addGenreModal">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Details -->
                    <div class="col-md-3">
                        <label class="form-label">Nashr yili</label>
                        <input type="number" name="year" class="form-control" min="500" max="<?= date('Y') ?>" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Nusxalar soni</label>
                        <input type="number" name="quantity" class="form-control" min="1" required value="1">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Holat</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_confirmed" id="is_confirmed" value="1">
                            <label class="form-check-label" for="is_confirmed">Tasdiqlangan</label>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label">Tavsif (ixtiyoriy)</label>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>
                    
                    <!-- Image Upload -->
                    <div class="col-12">
                        <label class="form-label">Kitob muqovasi (ixtiyoriy)</label>
                        <div class="file-upload" onclick="document.getElementById('file-input').click()">
                            <div class="upload-icon">
                                <i class="bi bi-cloud-arrow-up"></i>
                            </div>
                            <p class="mb-1">Rasmni yuklash uchun bosing yoki sudrab keling</p>
                            <small class="text-muted">Faqat JPG, JPEG, PNG rasmlar (maks. 5MB)</small>
                        </div>
                        <input type="file" id="file-input" name="image" class="file-input" accept="image/*">
                        
                        <div class="image-preview-container" id="image-preview-container">
                            <img src="#" alt="Kitob muqovasi" class="image-preview" id="image-preview">
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn submit-btn me-2">
                            <i class="bi bi-check-circle me-2"></i> Saqlash
                        </button>
                        <a href="books.php" class="btn cancel-btn">
                            <i class="bi bi-x-circle me-2"></i> Bekor qilish
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Author Modal -->
<div class="modal fade" id="addAuthorModal" tabindex="-1" aria-labelledby="addAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAuthorModalLabel"><i class="bi bi-person-plus me-2"></i>Yangi muallif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Muallif ismi</label>
                        <input type="text" class="form-control" name="new_author" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary" name="save_author">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Publisher Modal -->
<div class="modal fade" id="addPublisherModal" tabindex="-1" aria-labelledby="addPublisherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPublisherModalLabel"><i class="bi bi-building-add me-2"></i>Yangi nashriyot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nashriyot nomi</label>
                        <input type="text" class="form-control" name="new_publisher" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary" name="save_publisher">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Genre Modal -->
<div class="modal fade" id="addGenreModal" tabindex="-1" aria-labelledby="addGenreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGenreModalLabel"><i class="bi bi-tag me-2"></i>Yangi janr</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Janr nomi</label>
                        <input type="text" class="form-control" name="new_genre" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary" name="save_genre">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Enable image preview
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file-input');
        const imagePreview = document.getElementById('image-preview');
        const previewContainer = document.getElementById('image-preview-container');
        
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            }
        });
        
        // Drag and drop functionality
        const uploadArea = document.querySelector('.file-upload');
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--primary-color)';
            uploadArea.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#cbd5e1';
            uploadArea.style.backgroundColor = '';
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#cbd5e1';
            uploadArea.style.backgroundColor = '';
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                const changeEvent = new Event('change');
                fileInput.dispatchEvent(changeEvent);
            }
        });
    });
</script>

<?php require_once './template/footer.php'; ?>
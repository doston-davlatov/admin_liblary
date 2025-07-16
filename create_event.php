<?php
require_once './config.php';

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $location = trim($_POST['location']);
    $status = $_POST['status'];

    // Handle file upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/images/events/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('event_') . '.' . $fileExtension;
        $targetPath = $uploadDir . $fileName;

        // Check if image file is actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            // Allow certain file formats
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = $targetPath;
                }
            }
        }
    }

    $result = $db->insert("events", [
        "title" => $title,
        "description" => $description,
        "event_date" => $event_date,
        "location" => $location,
        "status" => $status,
        "image" => $imagePath
    ]);

    if (is_string($result)) {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <i class='bi bi-exclamation-octagon me-2'></i> Xatolik: $result
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    } else {
        header("Location: events.php?msg=Yangi tadbir muvaffaqiyatli qo'shildi!");
        exit;
    }
}
require_once './template/header.php';
?>

<style>
    /* Previous styles remain the same */
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

    .upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
        margin-bottom: 15px;
    }

    .upload-area:hover {
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
</style>

<div class="container event-form-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <h2 class="form-title"><i class="bi bi-calendar-plus me-2"></i>Yangi tadbir qo'shish</h2>

                <form method="post" class="row g-3 needs-validation" novalidate enctype="multipart/form-data">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Sarlavha</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="title" name="title" required>
                            <i class="bi bi-card-heading form-icon"></i>
                            <div class="invalid-feedback">
                                Iltimos, tadbir sarlavhasini kiriting!
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="event_date" class="form-label">Sana va vaqt</label>
                        <div class="input-group">
                            <input type="datetime-local" class="form-control" id="event_date" name="event_date"
                                required>
                            <i class="bi bi-clock form-icon"></i>
                            <div class="invalid-feedback">
                                Iltimos, tadbir sanasini tanlang!
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="location" class="form-label">Manzil</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="location" name="location" required>
                            <i class="bi bi-geo-alt form-icon"></i>
                            <div class="invalid-feedback">
                                Iltimos, tadbir manzilini kiriting!
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <div class="input-group">
                            <select class="form-select" id="status" name="status" required>
                                <option value="upcoming">Upcoming</option>
                                <option value="past">Past</option>
                            </select>
                            <i class="bi bi-info-circle form-icon"></i>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Tadbir rasmi</label>
                        <div class="upload-area" onclick="document.getElementById('file-input').click()">
                            <div class="upload-icon">
                                <i class="bi bi-cloud-arrow-up"></i>
                            </div>
                            <p class="mb-1">Rasmni yuklash uchun bosing yoki sudrab keling</p>
                            <small class="text-muted">Faqat JPG, JPEG, PNG rasmlar (maks. 5MB)</small>
                        </div>
                        <input type="file" id="file-input" name="image" class="file-input" accept="image/*">

                        <div class="image-preview-container" id="image-preview-container">
                            <img src="#" alt="Tadbir rasmi" class="image-preview" id="image-preview">
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Tavsif</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn submit-btn me-2">
                            <i class="bi bi-check-circle me-2"></i> Saqlash
                        </button>
                        <a href="events.php" class="btn cancel-btn">
                            <i class="bi bi-x-circle me-2"></i> Bekor qilish
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation
    document.addEventListener('DOMContentLoaded', function () {
        // Form validation
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // Image preview functionality
        const fileInput = document.getElementById('file-input');
        const imagePreview = document.getElementById('image-preview');
        const previewContainer = document.getElementById('image-preview-container');

        fileInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }

                reader.readAsDataURL(file);
            }
        });

        // Drag and drop functionality
        const uploadArea = document.querySelector('.upload-area');

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
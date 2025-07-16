<?php
require_once './config.php';
require_once './template/header.php';


$db = new Database();
$id = intval($_GET['id'] ?? 0);

// Ma'lumotni olish
$librarian = $db->select("librarians", "*", "id=?", [$id], "i");
if (!$librarian) {
    echo "<div class='alert alert-danger'>Kutubxonachi topilmadi.</div>";
    require_once './template/footer.php';
    exit;
}
$librarian = $librarian[0];

// Tahrirlash
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    $image = $librarian['image'];

    // Validatsiya
    $errors = [];
    if (empty($name)) {
        $errors[] = "Ism kiritilishi shart!";
    }
    if (strlen($name) > 255) {
        $errors[] = "Ism 255 belgidan oshmasligi kerak!";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email formati noto'g'ri!";
    }
    if (!empty($phone) && !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
        $errors[] = "Telefon raqami noto'g'ri formatda!";
    }
    if (!empty($position) && strlen($position) > 100) {
        $errors[] = "Lavozim 100 belgidan oshmasligi kerak!";
    }

    // Rasm yuklash
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = './assets/images/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        $fileType = $_FILES['image']['type'];
        $fileSize = $_FILES['image']['size'];
        $imageName = uniqid() . "_" . basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $imageName;

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Faqat JPEG, PNG yoki GIF fayllar ruxsat etiladi!";
        }
        if ($fileSize > $maxFileSize) {
            $errors[] = "Rasm hajmi 5MB dan oshmasligi kerak!";
        }

        if (empty($errors)) {
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Eski rasmni o'chirish
                if ($image && file_exists("./assets/images/$image")) {
                    unlink("./assets/images/$image");
                }
                $image = $imageName;
            } else {
                $errors[] = "Rasm yuklashda xatolik!";
            }
        }
    }

    // Xatolarni ko'rsatish
    if (!empty($errors)) {
        echo "<div class='alert alert-danger'>";
        foreach ($errors as $error) {
            echo htmlspecialchars($error) . "<br>";
        }
        echo "</div>";
    } else {
        // Ma'lumotlarni yangilash
        try {
            $updated = $db->update("librarians", [
                "name" => $name,
                "position" => $position,
                "email" => $email ?: null,
                "phone" => $phone ?: null,
                "image" => $image ?: null,
                "bio" => $bio ?: null
            ], "id=?", [$id], "i");

            if ($updated) {
                header("Location: librarians.php?msg=Kutubxonachi muvaffaqiyatli yangilandi!");
                exit;
            } 
            if (!preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
                throw new Exception("Noto'g'ri telefon formati: $phone");
            } else {
                $errorInfo = $db->getLastError(); // Database sinfida getLastError metodi bo'lishi kerak
                echo "<div class='alert alert-danger'>❌ Yangilashda xatolik: " . htmlspecialchars($errorInfo) . "</div>";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo "<div class='alert alert-danger'>Xato: " . htmlspecialchars($e->getMessage()) . "</div>";
            $errors[] = "Telefon raqami noto'g'ri formatda!";
        }
        
    }
}
?>

<div class="container my-4">
    <h2>Kutubxonachini tahrirlash</h2>
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Ismi</label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($librarian['name']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Lavozimi</label>
            <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($librarian['position']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($librarian['email']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Telefon</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($librarian['phone']) ?>">
        </div>
        <div class="col-12">
            <label class="form-label">Biografiya</label>
            <textarea name="bio" class="form-control" rows="5"><?= htmlspecialchars($librarian['bio']) ?></textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Rasm (agar almashtirmoqchi bo‘lsangiz)</label>
            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif">
            <?php if ($librarian['image']): ?>
                <img src="./assets/images/<?= htmlspecialchars($librarian['image']) ?>" alt="Rasm" width="120" class="mt-2 rounded shadow">
            <?php endif; ?>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Yangilash</button>
            <a href="librarians.php" class="btn btn-secondary">Orqaga</a>
        </div>
    </form>
</div>

<?php require_once './template/footer.php'; ?>
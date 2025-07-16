<?php
require_once './config.php';
require_once './template/header.php';

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    $photo_name = null;

    if (!empty($_FILES['photo']['name'])) {
        $photo_name = uniqid() . "_" . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], "assets/images/" . $photo_name);
    }

    $db->insert("librarians", [
        "name" => $name,
        "position" => $position,
        "email" => $email,
        "phone" => $phone,
        "photo" => $photo_name,
        "bio" => $bio
    ]);

    header("Location: librarians.php?msg=Yangi kutubxonachi qo‘shildi!");
    exit;
}
?>
<div class="container my-4">
    <h2>Yangi kutubxonachi qo‘shish</h2>
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Ismi</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Lavozimi</label>
            <input type="text" name="position" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Telefon</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Biografiya</label>
            <textarea name="bio" class="form-control"></textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Rasm</label>
            <input type="file" name="photo" class="form-control">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Saqlash</button>
            <a href="librarians.php" class="btn btn-secondary">Orqaga</a>
        </div>
    </form>
</div>
<?php require_once './template/footer.php'; ?>

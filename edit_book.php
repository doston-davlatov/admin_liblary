<?php
require_once './config.php';
require_once './template/header.php';

$db = new Database();

// Mualliflar ro‘yxatini olish
$authors = $db->select("authors");
$genres = $db->select("genres");
$publishers = $db->select("publishers");

// Tahrirlash uchun kitobni olish
$book = null;
if (isset($_GET['id'])) {
    $bookId = intval($_GET['id']);
    $bookData = $db->select("books", "*", "id=?", [$bookId], "i");
    if ($bookData) {
        $book = $bookData[0];
    } else {
        echo "<div class='alert alert-danger'>Kitob topilmadi!</div>";
        require_once 'template/footer.php'; exit;
    }
}

// Forma yuborilgan bo‘lsa
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $author_id = intval($_POST['author_id']);
    $publisher_id = intval($_POST['publisher_id']);
    $genre_id = intval($_POST['genre_id']);
    $isbn = trim($_POST['isbn']);
    $year = intval($_POST['year']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);

    // Rasm yuklash
    $coverImage = $book['cover_image'] ?? null;
    if (!empty($_FILES['cover_image']['name'])) {
        $targetDir = __DIR__ . '/assets/images/';
        $fileName = uniqid() . "_" . basename($_FILES["cover_image"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $targetFile)) {
            // Eski rasmni o‘chirish
            if ($book && !empty($book['cover_image']) && file_exists($targetDir . $book['cover_image'])) {
                unlink($targetDir . $book['cover_image']);
            }
            $coverImage = $fileName;
        } else {
            echo "<div class='alert alert-danger'>Rasm yuklashda xatolik yuz berdi!</div>";
        }
    }

    $data = [
        "title" => $title,
        "author_id" => $author_id,
        "publisher_id" => $publisher_id,
        "genre_id" => $genre_id,
        "isbn" => $isbn,
        "year" => $year,
        "description" => $description,
        "quantity" => $quantity,
        "cover_image" => $coverImage
    ];

    if ($book) {
        // Tahrirlash
        $db->update("books", $data, "id=?", [$bookId], "i");
        header("Location: books.php?msg=Kitob yangilandi!");
        exit;
    } else {
        // Qo‘shish
        $db->insert("books", $data);
        header("Location: books.php?msg=Yangi kitob qo‘shildi!");
        exit;
    }
}
?>

<h2><?= $book ? "Kitobni tahrirlash" : "Yangi kitob qo‘shish" ?></h2>
<form method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nomi</label>
        <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($book['title'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Muallif</label>
        <select name="author_id" class="form-select" required>
            <option value="">-- Tanlang --</option>
            <?php foreach ($authors as $author): ?>
                <option value="<?= $author['id'] ?>" <?= (isset($book['author_id']) && $book['author_id']==$author['id'])?'selected':'' ?>>
                    <?= htmlspecialchars($author['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Nashriyot</label>
        <select name="publisher_id" class="form-select">
            <option value="">-- Tanlang --</option>
            <?php foreach ($publishers as $publisher): ?>
                <option value="<?= $publisher['id'] ?>" <?= (isset($book['publisher_id']) && $book['publisher_id']==$publisher['id'])?'selected':'' ?>>
                    <?= htmlspecialchars($publisher['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Janr</label>
        <select name="genre_id" class="form-select">
            <option value="">-- Tanlang --</option>
            <?php foreach ($genres as $genre): ?>
                <option value="<?= $genre['id'] ?>" <?= (isset($book['genre_id']) && $book['genre_id']==$genre['id'])?'selected':'' ?>>
                    <?= htmlspecialchars($genre['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">ISBN</label>
        <input type="text" name="isbn" class="form-control" value="<?= htmlspecialchars($book['isbn'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Yil</label>
        <input type="number" name="year" class="form-control" required value="<?= htmlspecialchars($book['year'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Miqdor</label>
        <input type="number" name="quantity" class="form-control" required value="<?= htmlspecialchars($book['quantity'] ?? 1) ?>">
    </div>
    <div class="col-12">
        <label class="form-label">Tavsif</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Rasm</label>
        <input type="file" name="cover_image" class="form-control">
        <?php if ($book && !empty($book['cover_image'])): ?>
            <img src="assets/images/<?= htmlspecialchars($book['cover_image']) ?>" alt="Kitob rasmi" class="img-thumbnail mt-2" style="max-width:150px;">
        <?php endif; ?>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary"><?= $book ? "Saqlash" : "Qo‘shish" ?></button>
        <a href="books.php" class="btn btn-secondary">Orqaga</a>
    </div>
</form>

<?php require_once './template/footer.php'; ?>

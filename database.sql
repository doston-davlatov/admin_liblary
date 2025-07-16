-- Kutubxona uchun ma’lumotlar bazasini yaratish
DROP DATABASE IF EXISTS library_db;
CREATE DATABASE library_db;
USE library_db;

-- Mualliflar jadvali
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    bio TEXT,
    birth_date DATE
);

-- Nashriyotlar jadvali
CREATE TABLE publishers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    website VARCHAR(255)
);

-- Janrlar jadvali
CREATE TABLE genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Kitoblar jadvali
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author_id INT,
    publisher_id INT,
    isbn VARCHAR(20),
    genre_id INT,
    year SMALLINT,
    description TEXT,
    cover_image VARCHAR(255),
    quantity INT DEFAULT 1,
    is_confirmed BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL,
    FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL
);

-- Foydalanuvchilar jadvali
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role ENUM('reader', 'admin') DEFAULT 'reader',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Kitobni olib turish (ijara) jadvali
CREATE TABLE borrowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    status ENUM('olindi', 'qaytdi', 'muddati_otgan') DEFAULT 'olindi',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Kitoblarga foydalanuvchi sharhlari jadvali
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Kutubxonachilar jadvali
CREATE TABLE librarians (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    photo VARCHAR(255),
    bio TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tadbirlar/e’lonlar jadvali
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    is_confirmed BOOLEAN DEFAULT FALSE,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('upcoming', 'past') DEFAULT 'upcoming'
);

-- INDEX lar qidiruvni tezlashtirish uchun
CREATE INDEX idx_books_title ON books(title);
CREATE INDEX idx_books_isbn ON books(isbn);
CREATE INDEX idx_books_author_id ON books(author_id);
CREATE INDEX idx_books_genre_id ON books(genre_id);

-- Ma’lumotlar qo‘shish

-- Mualliflar
INSERT INTO authors (name, bio, birth_date) VALUES
('J.K. Rowling', 'Garri Potter muallifi', '1965-07-31'),
('Fyodor Dostoyevskiy', 'Rus yozuvchisi, psixologik romanlar ustasi', '1821-11-11'),
('Alisher Navoiy', 'Oʻzbek shoiri, mutafakkiri va davlat arbobi', '1441-02-09');

-- Nashriyotlar
INSERT INTO publishers (name, address, website) VALUES
('Penguin Random House', '1745 Broadway, New York, NY 10019, USA', 'https://www.penguinrandomhouse.com'),
('O‘zbekiston Milliy Nashriyoti', 'Toshkent, O‘zbekiston', 'https://uzmillyon.uz'),
('AST Publishers', 'Moskva, Rossiya', 'https://ast.ru');

-- Janrlar
INSERT INTO genres (name) VALUES
('Fantastika'),
('Klassika'),
('Sheʼriyat'),
('Detektiv');

-- Kitoblar
INSERT INTO books (title, author_id, publisher_id, isbn, genre_id, year, description, cover_image, quantity) VALUES
('Garri Potter va Sehrgar Tosh', 1, 1, '9780747532699', 1, 1997, 'Sehrgarlar maktabidagi sarguzashtlar.', 'harry_potter.jpg', 5),
('Jinoyat va Jazo', 2, 3, '9780140449136', 2, 1866, 'Rodion Raskolnikovning ogʻir kechinmalari.', 'crime_and_punishment.jpg', 3),
('Xamsa', 3, 2, '9781234567897', 3, 1485, 'Alisher Navoiyning mashhur dostonlari toʻplami.', 'xamsa.jpg', 4);

-- Foydalanuvchilar
INSERT INTO users (username, password, email, phone, role) VALUES
('reader1', '$2y$10$abcdefghijklmnopqrstuv', 'reader1@example.com', '+998901112233', 'reader'),
('admin1', '$2y$10$abcdefghijklmnopqrstuv', 'admin@example.com', '+998907778899', 'admin');

-- Kutubxonachilar
INSERT INTO librarians (name, position, email, phone, photo, bio) VALUES
('Dilfuza Karimova', 'Bosh kutubxonachi', 'dilfuza.karimova@library.uz', '+998901234567', 'dilfuza.jpg', '25 yildan ortiq tajribaga ega kutubxonachi.'),
('Jamshid Ismoilov', 'Axborot texnologiyalari bo‘limi rahbari', 'jamshid.ismoilov@library.uz', '+998907654321', 'jamshid.jpg', 'Kutubxona IT infratuzilmasini boshqaradi.');

-- Tadbirlar
INSERT INTO events (title, description, event_date, location, image, status) VALUES
('Kutubxona kuni', 'Kutubxonamiz tashkil topgan kun munosabati bilan bayram tadbiri.', '2025-10-01 15:00:00', 'Asosiy zal', 'library_day.jpg', 'upcoming'),
('Yangi kitoblar taqdimoti', 'Yangi kelgan kitoblarni kitobxonlarga tanishtirish.', '2024-05-15 11:00:00', 'O‘quv zali', 'new_books.jpg', 'past');

-- Kitoblarga sharhlar
INSERT INTO reviews (book_id, user_id, rating, comment) VALUES
(1, 1, 5, 'Juda qiziqarli kitob!'),
(2, 1, 4, 'Chuqur psixologik asar, tavsiya qilaman.');

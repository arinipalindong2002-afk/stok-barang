CREATE DATABASE IF NOT EXISTS stok_barang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE stok_barang;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS barang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(50) NOT NULL UNIQUE,
  nama VARCHAR(150) NOT NULL,
  satuan VARCHAR(30) NOT NULL,
  stok INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS transaksi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  barang_id INT NOT NULL,
  jenis ENUM('masuk','keluar') NOT NULL,
  jumlah INT NOT NULL,
  tanggal DATE NOT NULL,
  keterangan VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE
);

INSERT INTO users (username,password,nama)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCzv5J8y7M7K6h2JxYwS', 'Administrator')
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO barang (kode,nama,satuan,stok) VALUES
('BRG001','Contoh Barang','pcs',10)
ON DUPLICATE KEY UPDATE kode=kode;

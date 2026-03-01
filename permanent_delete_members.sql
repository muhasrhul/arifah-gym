-- CARA HAPUS PERMANEN DATA MEMBER YANG ADA DI SOFT DELETE

-- 1. LIHAT DULU SEMUA MEMBER YANG DI-SOFT DELETE
SELECT * FROM members WHERE deleted_at IS NOT NULL;

-- 2. HAPUS PERMANEN BERDASARKAN ID MEMBER
DELETE FROM members WHERE id = [ID_MEMBER_YANG_INGIN_DIHAPUS];

-- 3. HAPUS PERMANEN BERDASARKAN NAMA MEMBER
DELETE FROM members 
WHERE deleted_at IS NOT NULL 
AND name = 'Nama Member';

-- 4. HAPUS PERMANEN BERDASARKAN NOMOR TELEPON
DELETE FROM members 
WHERE deleted_at IS NOT NULL 
AND phone = '081234567890';

-- 5. HAPUS PERMANEN BERDASARKAN FINGERPRINT ID
DELETE FROM members 
WHERE deleted_at IS NOT NULL 
AND fingerprint_id = '123456';

-- 6. HAPUS PERMANEN SEMUA MEMBER YANG DI-SOFT DELETE
DELETE FROM members WHERE deleted_at IS NOT NULL;

-- 7. HAPUS PERMANEN MEMBER YANG DI-SOFT DELETE SEBELUM TANGGAL TERTENTU
DELETE FROM members 
WHERE deleted_at IS NOT NULL 
AND deleted_at < '2026-01-01';

-- 8. HAPUS PERMANEN MEMBER YANG TIDAK AKTIF DAN DI-SOFT DELETE
DELETE FROM members 
WHERE deleted_at IS NOT NULL 
AND is_active = 0;

-- 9. CEK JUMLAH MEMBER YANG DI-SOFT DELETE
SELECT COUNT(*) as total_soft_deleted FROM members WHERE deleted_at IS NOT NULL;

-- PERINGATAN: 
-- Setelah dihapus permanen, data member tidak bisa dikembalikan lagi!
-- Pastikan backup database sebelum melakukan permanent delete.
-- Periksa juga apakah ada data terkait di tabel lain (transactions, attendances, dll)
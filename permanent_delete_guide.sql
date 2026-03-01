-- CARA HAPUS PERMANEN DATA YANG ADA DI SOFT DELETE

-- 1. HAPUS PERMANEN BERDASARKAN ID
DELETE FROM transactions WHERE id = [ID_YANG_INGIN_DIHAPUS];

-- 2. HAPUS PERMANEN BERDASARKAN KONDISI TERTENTU
-- Contoh: hapus semua data yang di-soft delete sebelum tanggal tertentu
DELETE FROM transactions 
WHERE deleted_at IS NOT NULL 
AND deleted_at < '2026-01-01';

-- 3. HAPUS PERMANEN SEMUA DATA YANG DI-SOFT DELETE
DELETE FROM transactions WHERE deleted_at IS NOT NULL;

-- 4. HAPUS PERMANEN DATA TERTENTU YANG DI-SOFT DELETE
-- Contoh: hapus berdasarkan member_name
DELETE FROM transactions 
WHERE deleted_at IS NOT NULL 
AND member_name = 'Nama Member';

-- 5. LIHAT DULU DATA YANG DI-SOFT DELETE SEBELUM HAPUS PERMANEN
SELECT * FROM transactions WHERE deleted_at IS NOT NULL;

-- 6. HAPUS PERMANEN DENGAN FORCE DELETE (jika menggunakan Eloquent di Laravel)
-- Ini dilakukan di code PHP, bukan SQL:
-- Transaction::onlyTrashed()->where('id', $id)->forceDelete();
-- Transaction::onlyTrashed()->forceDelete(); // hapus semua yang di-soft delete

-- PERINGATAN: 
-- Setelah dihapus permanen, data tidak bisa dikembalikan lagi!
-- Pastikan backup database sebelum melakukan permanent delete.
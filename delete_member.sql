-- Delete member with phone number 81934410084 (Joko)
DELETE FROM members WHERE phone = '81934410084';

-- Check if the deletion was successful
SELECT COUNT(*) as remaining_count FROM members WHERE phone = '81934410084';
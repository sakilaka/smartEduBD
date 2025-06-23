-- php artisan migrate --path=database/migrations/2024_06_07_000164_create_primary_results_table.php

ALTER TABLE `secondary_result_details` ADD `roll_number` INT NULL AFTER `student_id`;



<?php
include "config.php";

// Script ini dijalankan otomatis oleh cron job setiap tanggal 1
// Contoh cron job: 0 0 1 * * /usr/bin/php /path/to/cron_backup.php

// Cek jika sudah backup bulan ini
$current_month = date('Y-m');
$last_backup_query = "SELECT * FROM backup_logs WHERE filename LIKE 'backup_$current_month%' AND status = 'success' LIMIT 1";
$last_backup_result = $conn->query($last_backup_query);

if ($last_backup_result->num_rows == 0) {
    // Backup database
    function backupDatabase() {
        global $conn;
        
        $backup_dir = 'backups/';
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0777, true);
        }
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backup_dir . $filename;
        
        try {
            // Get all tables
            $tables = [];
            $result = $conn->query("SHOW TABLES");
            while ($row = $result->fetch_row()) {
                $tables[] = $row[0];
            }
            
            $sql = "-- MySQL Backup\n";
            $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
            
            foreach ($tables as $table) {
                // Drop table
                $sql .= "DROP TABLE IF EXISTS `$table`;\n";
                
                // Create table
                $create_result = $conn->query("SHOW CREATE TABLE `$table`");
                $create_row = $create_result->fetch_row();
                $sql .= $create_row[1] . ";\n\n";
                
                // Insert data
                $data_result = $conn->query("SELECT * FROM `$table`");
                if ($data_result->num_rows > 0) {
                    $sql .= "INSERT INTO `$table` VALUES\n";
                    $rows = [];
                    while ($row = $data_result->fetch_row()) {
                        $values = array_map(function($value) use ($conn) {
                            return $value === null ? 'NULL' : "'" . $conn->real_escape_string($value) . "'";
                        }, $row);
                        $rows[] = "(" . implode(', ', $values) . ")";
                    }
                    $sql .= implode(",\n", $rows) . ";\n\n";
                }
            }
            
            // Write to file
            if (file_put_contents($filepath, $sql)) {
                $size = filesize($filepath);
                
                // Log success
                $conn->query("INSERT INTO backup_logs (filename, size, status, message) 
                             VALUES ('$filename', '$size', 'success', 'Backup otomatis bulanan')");
                
                // Hapus backup lama (simpan hanya 3 terbaru)
                $backup_files = [];
                if (is_dir($backup_dir)) {
                    $files = scandir($backup_dir);
                    foreach ($files as $file) {
                        if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                            $backup_files[] = [
                                'filename' => $file,
                                'modified' => filemtime($backup_dir . $file)
                            ];
                        }
                    }
                    
                    // Sort by modified time (oldest first)
                    usort($backup_files, function($a, $b) {
                        return $a['modified'] - $b['modified'];
                    });
                    
                    // Hapus file lama (lebih dari 3 file)
                    while (count($backup_files) > 3) {
                        $old_file = array_shift($backup_files);
                        unlink($backup_dir . $old_file['filename']);
                    }
                }
                
                return true;
            } else {
                throw new Exception("Gagal menulis file backup");
            }
        } catch (Exception $e) {
            // Log error
            $conn->query("INSERT INTO backup_logs (filename, size, status, message) 
                         VALUES ('$filename', '0', 'failed', '" . $conn->real_escape_string($e->getMessage()) . "')");
            
            return false;
        }
    }
    
    // Jalankan backup
    backupDatabase();
}

echo "Backup process completed at " . date('Y-m-d H:i:s');
?>
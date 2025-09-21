<?php
include "config.php";
check_login('guru');

$room_id = $_GET['room_id'] ?? 0;
$students = [];

$query = "SELECT nis FROM room_students WHERE room_id = $room_id";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $students[] = $row['nis'];
}

header('Content-Type: application/json');
echo json_encode($students);
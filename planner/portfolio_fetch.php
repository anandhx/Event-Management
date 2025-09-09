<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

$plannerId = isset($_GET['planner_id']) ? (int)$_GET['planner_id'] : 0;
if ($plannerId <= 0) { echo json_encode([]); exit(); }

$images = [];
$stmt = $conn->prepare('SELECT image_path, caption FROM planner_portfolio_images WHERE planner_id = ? ORDER BY created_at DESC LIMIT 24');
if ($stmt) { $stmt->bind_param('i', $plannerId); $stmt->execute(); $res = $stmt->get_result(); while ($row = $res->fetch_assoc()) { $images[] = $row; } }

echo json_encode($images);
?>



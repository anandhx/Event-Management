<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'planner') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$plannerId = (int)$_SESSION['user_id'];

$input = json_decode(file_get_contents('php://input'), true);
$eventId = (int)($input['event_id'] ?? 0);
$newStatus = trim($input['status'] ?? '');
$clientId = isset($input['client_id']) ? (int)$input['client_id'] : 0;

$allowed = ['pending','confirmed','in_progress','completed','cancelled'];
if ($eventId <= 0 || !in_array($newStatus, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

// Ensure the event belongs to this planner
$stmt = $conn->prepare("SELECT id, client_id, status FROM events WHERE id = ? AND planner_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'DB error']);
    exit();
}
$stmt->bind_param('ii', $eventId, $plannerId);
$stmt->execute();
$res = $stmt->get_result();
$event = $res->fetch_assoc();
if (!$event) {
    echo json_encode(['success' => false, 'message' => 'Event not found']);
    exit();
}
$clientId = $clientId ?: (int)$event['client_id'];

// Transition rules: allow planner to set per business logic
// pending -> confirmed/cancelled; confirmed -> in_progress/cancelled; in_progress -> completed/cancelled
$from = $event['status'];
$validTransition = (
    ($from === 'pending' && in_array($newStatus, ['confirmed','cancelled'], true)) ||
    ($from === 'confirmed' && in_array($newStatus, ['in_progress','cancelled'], true)) ||
    ($from === 'in_progress' && in_array($newStatus, ['completed','cancelled'], true)) ||
    ($from === $newStatus) // idempotent
);
if (!$validTransition) {
    echo json_encode(['success' => false, 'message' => 'Invalid status transition']);
    exit();
}

$conn->begin_transaction();
try {
    $stmt = $conn->prepare("UPDATE events SET status = ?, updated_at = NOW() WHERE id = ? AND planner_id = ?");
    if (!$stmt) { throw new Exception('Prepare failed'); }
    $stmt->bind_param('sii', $newStatus, $eventId, $plannerId);
    if (!$stmt->execute()) { throw new Exception('Update failed'); }

    // Create notification for client
    $note = 'Your event status changed to ' . str_replace('_',' ', $newStatus);
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, event_id) VALUES (?, ?, ?)");
    if ($stmt) { $stmt->bind_param('isi', $clientId, $note, $eventId); $stmt->execute(); }

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Transaction failed']);
}
?>



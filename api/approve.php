/* ===================== api/approve.php ===================== */
<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');
$user = current_user(); if(!$user) { echo json_encode(['ok'=>false,'message'=>'Not logged']); exit; }
$input = json_decode(file_get_contents('php://input'), true);
$id = (int)$input['id']; $role = $input['role']; $status = $input['status'];
if ($role === 'hod' && $user['role']!=='hod') { echo json_encode(['ok'=>false,'message'=>'Forbidden']); exit; }
if ($role === 'principal' && $user['role']!=='principal') { echo json_encode(['ok'=>false,'message'=>'Forbidden']); exit; }
$field = ($role==='hod') ? 'hod_status' : 'principal_status';
$stmt = $mysqli->prepare("UPDATE leaves SET $field = ? WHERE id = ?");
$stmt->bind_param('si',$status,$id); $stmt->execute();
// notify requester
$rq = $mysqli->prepare('SELECT user_id FROM leaves WHERE id=?'); $rq->bind_param('i',$id); $rq->execute(); $uid = $rq->get_result()->fetch_assoc()['user_id'];
add_notification($uid, strtoupper($role) . " has $status your leave #$id");

echo json_encode(['ok'=>true,'message'=>'Updated']);

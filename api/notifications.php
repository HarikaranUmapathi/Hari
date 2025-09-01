/* ===================== api/notifications.php ===================== */
<?php
require_once __DIR__ . '/../config.php';
$user = current_user(); header('Content-Type: application/json');
if (!$user) { echo json_encode(['ok'=>false]); exit; }
$since = isset($_GET['since']) ? $_GET['since'] : 0; // timestamp id
$stmt = $mysqli->prepare('SELECT id,message,is_read,created_at FROM notifications WHERE user_id=? ORDER BY id DESC LIMIT 20');
$stmt->bind_param('i',$user['id']); $stmt->execute(); $res = $stmt->get_result(); $data = $res->fetch_all(MYSQLI_ASSOC);
echo json_encode(['ok'=>true,'notifications'=>$data]);


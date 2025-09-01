/* ===================== public/substitute_action.php ===================== */
<?php
require_once __DIR__ . '/../config.php';
$user = current_user(); if(!$user) exit('unauth');
if($_SERVER['REQUEST_METHOD']==='POST'){
    $leave_id = (int)$_POST['leave_id']; $action = $_POST['action'];
    // find request
    $stmt = $mysqli->prepare('SELECT id, from_user FROM substitute_requests WHERE leave_id=? AND to_user=? AND status="pending"');
    $stmt->bind_param('ii',$leave_id,$user['id']); $stmt->execute(); $req = $stmt->get_result()->fetch_assoc();
    if (!$req) { echo json_encode(['ok'=>false,'msg'=>'No pending request']); exit; }
    $status = ($action==='accept') ? 'accepted' : 'rejected';
    $u = $mysqli->prepare('UPDATE substitute_requests SET status=? WHERE id=?'); $u->bind_param('si',$status,$req['id']); $u->execute();
    // update leaves table
    if ($status==='accepted'){
        $l = $mysqli->prepare('UPDATE leaves SET substitute_status="accepted", substitute_id=? WHERE id=?');
        $l->bind_param('ii',$user['id'],$leave_id); $l->execute();
        add_notification($req['from_user'], "Substitute accepted by {$user['name']} for leave #$leave_id");
    } else {
        $l = $mysqli->prepare('UPDATE leaves SET substitute_status="rejected", substitute_id=NULL WHERE id=?');
        $l->bind_param('i',$leave_id); $l->execute();
        add_notification($req['from_user'], "Substitute rejected by {$user['name']} for leave #$leave_id");
    }
    echo json_encode(['ok'=>true]);
}
<?php
require_once __DIR__ . '/../config.php';
$user = current_user(); if(!$user) { header('Location: index.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $start = $_POST['start_date']; $end = $_POST['end_date']; $type = $_POST['type']; $reason = $_POST['reason'];
    $sub = !empty($_POST['substitute_id']) ? (int)$_POST['substitute_id'] : null;
    $docPath = null;
    if (!empty($_FILES['document']['name'])){
        $uploaddir = __DIR__ . '/../uploads/'; if(!is_dir($uploaddir)) mkdir($uploaddir,0755,true);
        $fname = time().'_'.basename($_FILES['document']['name']);
        move_uploaded_file($_FILES['document']['tmp_name'],$uploaddir.$fname);
        $docPath = 'uploads/'.$fname;
    }
    $stmt = $mysqli->prepare('INSERT INTO leaves (user_id,start_date,end_date,type,reason,document,substitute_id,substitute_status) VALUES (?,?,?,?,?,?,?,?)');
    $sub_status = $sub ? 'pending' : 'none';
    $stmt->bind_param('isssssis',$user['id'],$start,$end,$type,$reason,$docPath,$sub,$sub_status);
    $stmt->execute(); $leave_id = $stmt->insert_id;
    // if substitute requested create a substitute request and notify
    if ($sub){
        $sstmt = $mysqli->prepare('INSERT INTO substitute_requests (leave_id,from_user,to_user) VALUES (?,?,?)');
        $sstmt->bind_param('iii',$leave_id,$user['id'],$sub); $sstmt->execute();
        add_notification($sub, "You have been requested to substitute for leave #$leave_id by {$user['name']}");
    }
    // Notify HOD
    // find hod of department
    $hstmt = $mysqli->prepare('SELECT id FROM users WHERE role="hod" AND department = ?');
    $hstmt->bind_param('s',$user['department']); $hstmt->execute(); $hid = $hstmt->get_result()->fetch_assoc();
    if ($hid) add_notification($hid['id'], "New leave request #$leave_id from {$user['name']}");

    header('Location: dashboard.php'); exit;
}
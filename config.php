<?php
// config.php - DB connection and helper functions
session_start();

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'faculty_leave';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('DB Connect Error: ' . $mysqli->connect_error);
}

function esc($s){ global $mysqli; return htmlspecialchars($mysqli->real_escape_string($s)); }

function current_user(){
    if (!empty($_SESSION['user_id'])){
        global $mysqli;
        $stmt = $mysqli->prepare("SELECT id,name,email,role,department FROM users WHERE id = ?");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }
    return null;
}

function add_notification($user_id, $message){
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO notifications (user_id, message) VALUES (?,?)");
    $stmt->bind_param('is', $user_id, $message);
    $stmt->execute();
}


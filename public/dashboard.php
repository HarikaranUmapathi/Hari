/* ===================== public/dashboard.php ===================== */
<?php
require_once __DIR__ . '/../config.php';
$user = current_user();
if (!$user) { header('Location: index.php'); exit; }

// Fetch upcoming leaves for user's department (for HOD/Admin), or user's leaves
if ($user['role'] === 'faculty'){
    $stmt=$mysqli->prepare('SELECT * FROM leaves WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->bind_param('i',$user['id']);
    $stmt->execute(); $myLeaves=$stmt->get_result();
} else if ($user['role'] === 'hod'){
    $stmt = $mysqli->prepare('SELECT l.*, u.name FROM leaves l JOIN users u ON l.user_id=u.id WHERE u.department = ? ORDER BY l.created_at DESC');
    $stmt->bind_param('s',$user['department']); $stmt->execute(); $deptLeaves=$stmt->get_result();
} else {
    $stmt = $mysqli->query('SELECT l.*, u.name FROM leaves l JOIN users u ON l.user_id=u.id ORDER BY l.created_at DESC'); $allLeaves = $stmt->get_result();
}

// get notifications count
$notifStmt = $mysqli->prepare('SELECT COUNT(*) as c FROM notifications WHERE user_id=? AND is_read=0');
$notifStmt->bind_param('i', $user['id']); $notifStmt->execute(); $notifCount = $notifStmt->get_result()->fetch_assoc()['c'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="/assets/styles.css">
  <script src="/assets/app.js" defer></script>
</head>
<body>
  <nav>
    <div>Hi, <?php echo esc($user['name']); ?> (<?php echo esc($user['role']); ?>)</div>
    <div><a href="request_leave.php">Request Leave</a> | <a href="logout.php">Logout</a> | <span id="notif-count"><?php echo $notifCount; ?></span></div>
  </nav>
  <main>
    <h2>Dashboard</h2>

    <?php if($user['role'] === 'faculty'): ?>
      <h3>Your Leaves</h3>
      <table>
        <tr><th>ID</th><th>Dates</th><th>Type</th><th>Substitute</th><th>HOD</th><th>Principal</th></tr>
        <?php while($row = $myLeaves->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id'];?></td>
            <td><?php echo esc($row['start_date']).' to '.esc($row['end_date']);?></td>
            <td><?php echo esc($row['type']);?></td>
            <td><?php echo $row['substitute_id'] ? 'Requested' : 'None';?></td>
            <td><?php echo esc($row['hod_status']);?></td>
            <td><?php echo esc($row['principal_status']);?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php elseif($user['role'] === 'hod'): ?>
      <h3>Department Leave Requests</h3>
      <table>
        <tr><th>ID</th><th>Faculty</th><th>Dates</th><th>Type</th><th>Sub</th><th>HOD Action</th></tr>
        <?php while($r = $deptLeaves->fetch_assoc()): ?>
          <tr>
            <td><?php echo $r['id'];?></td>
            <td><?php echo esc($r['name']);?></td>
            <td><?php echo esc($r['start_date']).' to '.esc($r['end_date']);?></td>
            <td><?php echo esc($r['type']);?></td>
            <td><?php echo $r['substitute_id'] ? 'Yes' : 'No';?></td>
            <td>
              <button onclick="actionApprove(<?php echo $r['id'];?>,'hod','approved')">Approve</button>
              <button onclick="actionApprove(<?php echo $r['id'];?>,'hod','rejected')">Reject</button>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <h3>All Leaves</h3>
      <table>
        <tr><th>ID</th><th>Faculty</th><th>Dates</th><th>Type</th><th>HOD</th><th>Principal</th><th>Action</th></tr>
        <?php while($r = $allLeaves->fetch_assoc()): ?>
          <tr>
            <td><?php echo $r['id'];?></td>
            <td><?php echo esc($r['name']);?></td>
            <td><?php echo esc($r['start_date']).' to '.esc($r['end_date']);?></td>
            <td><?php echo esc($r['type']);?></td>
            <td><?php echo esc($r['hod_status']);?></td>
            <td><?php echo esc($r['principal_status']);?></td>
            <td>
              <?php if($user['role']==='principal'){ ?>
                <button onclick="actionApprove(<?php echo $r['id'];?>,'principal','approved')">Approve</button>
                <button onclick="actionApprove(<?php echo $r['id'];?>,'principal','rejected')">Reject</button>
              <?php } ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
  </main>

  <script>
    function actionApprove(id,role,status){
      fetch('/api/approve.php',{
        method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({id,role,status})
      }).then(r=>r.json()).then(j=>{ alert(j.message); location.reload(); });
    }
  </script>
</body>
</html>


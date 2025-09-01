/* ===================== public/request_leave.php ===================== */
<?php
require_once __DIR__ . '/../config.php';
$user = current_user(); if(!$user) { header('Location: index.php'); exit; }
// Fetch faculty list for substitute (same department except self)
$stmt = $mysqli->prepare('SELECT id,name FROM users WHERE department = ? AND role="faculty" AND id != ?');
$stmt->bind_param('si', $user['department'],$user['id']); $stmt->execute(); $facList = $stmt->get_result();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Request Leave</title><link rel="stylesheet" href="/assets/styles.css"></head>
<body>
  <a href="dashboard.php">Back</a>
  <h2>Request Leave</h2>
  <form action="request_submit.php" method="post" enctype="multipart/form-data">
    <label>Start Date</label><input type="date" name="start_date" required>
    <label>End Date</label><input type="date" name="end_date" required>
    <label>Type</label>
    <select name="type"><option value="casual">Casual</option><option value="medical">Medical</option><option value="on-duty">On-duty</option></select>
    <label>Reason</label><textarea name="reason"></textarea>
    <label>Attach Document (optional)</label><input type="file" name="document">
    <label>Suggest Substitute (optional)</label>
    <select name="substitute_id">
      <option value="">--none--</option>
      <?php while($f=$facList->fetch_assoc()): ?>
        <option value="<?php echo $f['id'];?>"><?php echo esc($f['name']);?></option>
      <?php endwhile;?>
    </select>
    <button type="submit">Submit</button>
  </form>
</body>
</html>
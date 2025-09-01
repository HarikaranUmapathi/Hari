/* ===================== assets/app.js ===================== */
// Simple polling for notifications and to update notif count
(function(){
  async function fetchNotifs(){
    const r = await fetch('/api/notifications.php');
    if (!r.ok) return;
    const j = await r.json();
    if (j.ok){
      const c = j.notifications.filter(n=>n.is_read==0).length;
      const el = document.getElementById('notif-count'); if(el) el.textContent = c;
      // Optionally show a small popup for new ones
    }
  }
  setInterval(fetchNotifs, 10000); // poll every 10s
  fetchNotifs();
})();
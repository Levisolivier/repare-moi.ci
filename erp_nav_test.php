<?php
// Test navigation - si les boutons fonctionnent ici, le problème est dans le JS principal
session_start();
$ok = !empty($_COOKIE['erp_token']);
?><!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Test nav</title>
<style>
body{margin:0;font-family:Arial;background:#111827;color:#fff;display:flex}
.sb{width:200px;background:#1F2937;min-height:100vh;padding:20px}
.sb button{display:block;width:100%;padding:10px;background:none;border:none;color:#9CA3AF;text-align:left;cursor:pointer;font-size:14px;margin-bottom:4px;border-radius:6px}
.sb button:hover,.sb button.active{background:#FF6A00;color:#fff}
.main{flex:1;padding:20px}
.page{display:none}.page.active{display:block}
.page h2{color:#FF6A00;margin-bottom:10px}
</style>
</head>
<body>
<div class="sb">
  <div style="color:#FF6A00;font-weight:bold;margin-bottom:16px">REPARE-MOI CI</div>
  <button onclick="show('p1')">Dashboard</button>
  <button onclick="show('p2')">Point de vente</button>
  <button onclick="show('p3')">Stock</button>
  <button onclick="show('p4')">Utilisateurs</button>
  <button onclick="show('p5')">Configuration</button>
  <hr style="border-color:#374151;margin:12px 0">
  <button onclick="window.location='erp.php?logout=1'" style="color:#EF4444">Quitter</button>
</div>
<div class="main">
  <div id="p1" class="page active"><h2>Dashboard</h2><p>Les onglets fonctionnent !</p><p>Cookie erp_token present: <?php echo $ok?'✅ OUI':'❌ NON'; ?></p></div>
  <div id="p2" class="page"><h2>Point de vente</h2><p>Page POS - OK</p></div>
  <div id="p3" class="page"><h2>Stock</h2><p>Page Stock - OK</p></div>
  <div id="p4" class="page"><h2>Utilisateurs</h2><p>Page Users - OK</p></div>
  <div id="p5" class="page"><h2>Configuration</h2><p>Page Config - OK</p></div>
</div>
<script>
function show(id){
  document.querySelectorAll('.page').forEach(function(p){p.classList.remove('active');});
  document.querySelectorAll('.sb button').forEach(function(b){b.classList.remove('active');});
  document.getElementById(id).classList.add('active');
  event.target.classList.add('active');
}
</script>
</body>
</html>

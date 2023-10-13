<li class="nav-item <?php echo $nav == 'dashboard' ? 'active' : ''; ?>">
  <a class="nav-link " href="/dashboard.php">
    <i class="bi bi-grid"></i>
    <span>Beranda</span>
  </a>
</li>
<li class="nav-item <?php echo $nav == 'event' ? 'active' : ''; ?>">
  <a class="nav-link " href="/event.php">
    <i class="bi bi-calendar-event"></i>
    <span>Kelola Event</span>
  </a>
</li>
<li class="nav-item <?php echo $nav == 'tempat' ? 'active' : ''; ?>">
<li class="nav-item">
  <a class="nav-link " href="/tempat.php">
    <i class="bi bi-building"></i>
    <span>Penyewaan Tempat</span>
  </a>
</li>
<li class="nav-item <?php echo $nav == 'seniman' ? 'active' : ''; ?>">
  <a class="nav-link " href="/seniman.php">
    <i class="bi bi-people"></i>
    <span>Nomor Induk Seniman</span>
  </a>
</li>
<li class="nav-item <?php echo $nav == 'pentas' ? 'active' : ''; ?>">
  <a class="nav-link " href="/halaman/SuratAdvis/MenuUtama.php">
    <i class="bi bi-megaphone"></i>
    <span>Kelola Izin Pentas</span>
  </a>
</li>
<?php if($userAuth['role'] == 'super admin'){ ?>
<li class="nav-item <?php echo $nav == 'admin' ? 'active' :  ''?>">
  <a class="nav-link " href="/admin.php">
    <i class="bi bi-megaphone"></i>
    <span>Kelola Admin</span>
  </a>
</li>
<?php }?>
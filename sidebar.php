<?php if($userAuth['role'] != 'masyarakat'){ ?>
<li class="nav-item <?php echo $nav == 'dashboard' ? 'active' : ''; ?>">
  <a class="nav-link " href="/dashboard.php">
    <i class="bi bi-grid"></i>
    <span>Beranda</span>
  </a>
</li>
<?php }?>

<?php if($userAuth['role'] == 'super admin' || $userAuth['role'] == 'admin event'){ ?>
<li class="nav-item <?php echo $nav == 'event' ? 'active' : ''; ?>">
  <a class="nav-link " href="/event.php">
    <i class="bi bi-calendar-event"></i>
    <span>Kelola Event</span>
  </a>
</li>
<?php }?>

<?php if($userAuth['role'] == 'super admin' || $userAuth['role'] == 'admin tempat'){ ?>
<li class="nav-item <?php echo $nav == 'tempat' ? 'active' : ''; ?>">
  <a class="nav-link " href="/tempat.php">
    <i class="bi bi-building"></i>
    <span>Kelola Tempat</span>
  </a>
</li>
<?php }?>

<?php if($userAuth['role'] == 'super admin' || $userAuth['role'] == 'admin seniman'){ ?>
<li class="nav-item <?php echo $nav == 'seniman' ? 'active' : ''; ?>">
  <a class="nav-link " href="/seniman.php">
    <i class="bi bi-people"></i>
    <span>Kelola Seniman</span>
  </a>
</li>
<?php }?>

<?php if($userAuth['role'] == 'super admin' || $userAuth['role'] == 'admin seniman'){ ?>
<li class="nav-item <?php echo $nav == 'pentas' ? 'active' : ''; ?>">
  <a class="nav-link " href="/pentas.php">
    <i class="bi bi-megaphone"></i>
    <span>Kelola Izin Pentas</span>
  </a>
</li>
<?php }?>

<?php if(in_array($userAuth['role'],['super admin','admin event','admin seniman','admin tempat'])){ ?>
<li class="nav-item <?php echo $nav == 'pengguna' ? 'active' :  ''?>">
  <a class="nav-link " href="/pengguna.php">
    <i class="bi bi-people"></i>
    <span>Kelola Pengguna</span>
  </a>
</li>
<?php }?>

<?php if($userAuth['role'] == 'super admin'){ ?>
<li class="nav-item <?php echo $nav == 'admin' ? 'active' :  ''?>">
  <a class="nav-link " href="/admin.php">
    <i class="bi bi-people"></i>
    <span>Kelola Admin</span>
  </a>
</li>
<?php }?>
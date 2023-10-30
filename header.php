    <div class="d-flex align-items-center justify-content-between">
      <a href="/home.php" class="logo d-flex align-items-center">
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <span class="d-none d-lg-block">Nganjuk Elok</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn" id="btn"> </i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <?php if(isset($userAuth['foto']) && !empty($userAuth['foto']) && !is_null($userAuth['foto'])){?>
              <img src="/private/profile/admin<?php echo $userAuth['foto'] ?>" alt="Profile" class="rounded-circle">
              <?php }else{?>
                <img src="/private/profile/admin/default.jpg" alt="Profile" class="rounded-circle">
            <?php }?>
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $userAuth['nama_lengkap']?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $userAuth['nama_lengkap'] ?></h6>
              <span><?php echo $userAuth['role'] ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li >
              <a class="dropdown-item d-flex align-items-center" href="/profile.php">
                <i class="bi bi-person"></i>
                <span>Akun</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="dropdown-item d-flex align-items-center" onclick="logout()">
              <a href="#">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav>
    <script src="/public/js/utama/logout.js"></script>
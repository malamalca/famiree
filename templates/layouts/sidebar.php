<?php
  use App\Core\App;
?>  
<nav id="sidebar">
    <div class="sidebar-header">
      <div class="float-left">
        <a href="#" id="close-sidebar" class="btn"><i class="fas fa-fw fa-times"></i></a>
      </div>
      <span class="sidebar-header-title"><?= htmlspecialchars($consoleName) ?></span>
    </div>
    <section class="row">
      <ul class="column sidebar-nav">
      <li>
          <a href="<?= App::url('/') ?>" class="hover-blue">
            <!--[https://ionicons.com/]-->
            <i class="icon ion-md-home"></i>
            <span>Home</span>
          </a>
        </li>
        <li>
          <a href="<?= App::url('/events') ?>" class="hover-blue">
            <!--[https://ionicons.com/]-->
            <i class="icon ion-md-camera"></i>
            <span>Event List</span>
          </a>
        </li>   
        <li>
          <a href="<?= App::url('/devices') ?>" class="hover-blue">
            <!--[https://ionicons.com/]-->
            <i class="icon ion-md-phone-portrait"></i>
            <span>Devices</span>
          </a>
        </li>        
        <li class="nav-section-heading">
          ADMIN
        </li>
        <li>
          <a href="<?= App::url('/settings') ?>" class="hover-blue">
            <i class="icon ion-md-settings"></i>
            <span>Settings</span>
          </a>
        </li>
        <li>
          <a href="<?= App::url('/changepasswd') ?>" class="hover-deep-orange">
            <i class="icon ion-md-key"></i>
            <span>Change Password</span>
          </a>
        </li>
        <li>
          <a href="<?= App::url('/reboot') ?>" class="hover-blue-grey" onclick="return confirm('Are you sure?');">
            <i class="icon ion-md-refresh"></i>
            <span>Reboot</span>
          </a>
        </li>
        <li>
          <a href="<?= App::url('/logout') ?>" class="hover-blue-grey">
            <i class="icon ion-md-exit"></i>
            <span>Logout</span>
          </a>
        </li>
      </ul>
    </section>
  </nav>
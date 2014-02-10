<?php include ( __DIR__.'/headers.php' );
  $sess = NZ\SessionHandler::getInstance();
  $user_id = $sess->getUser_id();
?>   
<!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo '/'.OSPARI_ADMIN_PATH ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="<?php echo '/'.OSPARI_ADMIN_PATH.'/setting' ?>"><i class="fa fa-wrench"></i> Setting</a></li>
            <li><a href="<?php echo '/'.OSPARI_ADMIN_PATH.'/draft/create' ?>"><i class="fa fa-plus"></i> New Post</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
              <li><a href="<?php echo '/'.OSPARI_ADMIN_PATH.'/user' ?>"> <i class="fa fa-user"></i> User Setting</a></li>
              <?php if($user_id): ?>
              <li><a href="<?php echo OSPARI_URL ?>" target="_blog"><i class="fa fa-external-link"></i> View Blog</a></li>
              <li><a href="<?php echo '/'.OSPARI_ADMIN_PATH.'/logout' ?>"><i class="fa fa-sign-out"></i> Logout</a></li>
              <?php else:?>
              <li><a href="<?php echo '/'.OSPARI_ADMIN_PATH.'/login' ?>"><i class="fa fa-sign-in"></i> Login</a></li>
              <?php endif;?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>




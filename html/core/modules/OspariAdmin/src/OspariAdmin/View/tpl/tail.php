<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
   
    <script src="<?php echo OSPARI_URL ?>/assets-admin/js/bootstrap.min.js"></script>
    <script src="<?php echo OSPARI_URL ?>/assets-admin/js/bootbox.min.js"></script>
    <script src="<?php echo OSPARI_URL ?>/assets-admin/js/ospari-admin.js"></script>
<?php

$view = \NZ\View::getInstance();

foreach( $view->getJS() as $js ){
    echo "<script src=\"{$js}\"></script>";
}

?>


   
  </body>
</html>
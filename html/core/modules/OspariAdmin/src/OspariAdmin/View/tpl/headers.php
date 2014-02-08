 <?php 
     /** ################### important ##################
      Do not use any models or connect to DB here
      */
    ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">
    <?php 
     $view = \NZ\View::getInstance();
     $title ='Ospari';
     if($view->title){
         $title.='-'.$view->title;
     }
    ?>
    <title><?php echo $title; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo OSPARI_URL ?>/assets-admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo OSPARI_URL ?>/assets-admin/css/font-awesome.min.css" rel="stylesheet">
    

    <!-- Custom styles for this template -->
    <link href="<?php echo OSPARI_URL ?>/assets-admin/css/style.css" rel="stylesheet">
    <?php


foreach( $view->getCSS() as $js ){
    echo "<link href=\"{$js}\" rel=\"stylesheet\">";
}

?>
    
     <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>

  </head>

  <body> 
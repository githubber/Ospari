<?php

$this->title = 'Upload';
$conf = \NZ\Config::getInstance();
$this->head = __DIR__.'/../tpl/headers.php';
?>
<div style="margin-top:-70px">
<div class="col-lg-12">
<?php
if( $e = $this->exception ){
    echo '<div class="alert alert-error">'.$e->getMessage().'</div>';
    //echo '<pre>'.$e->__toString().'</pre>';
}

if( $media = $this->media ){
    if( $req->parent_callback ){
        $json = json_encode( $media->toStdObject() );
        ?>
        <script>
            if( window.parent ){
                window.parent.<?php echo $this->escape($req->parent_callback); echo '('.$json.')'; ?>;
                self.close();
            }
        </script>
        <?php
    }else{
        echo '<div class="alert alert-info">Image URL: '.$media->large.'</div>';
    }
}

?>    
    
<form role="form" action="<?php echo $this->formAction;?>" method="post" enctype="multipart/form-data">
  <div class="form-group">
    
    <input type="name" class="form-control" id="exampleInputEmail1" placeholder="Name (optional)">
  </div>
  
  <div class="form-group">
    
    <input type="file" id="exampleInputFile">
  </div>

    <button type="submit" data-loading-text="Uploading..." class="btn btn-primary pull-right">Upload</button>
</form>
        
</div>
</div>
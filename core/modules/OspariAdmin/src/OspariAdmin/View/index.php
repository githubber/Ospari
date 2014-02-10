<?php

$title = 'Dashboard';
$draftPager = $this->draftPager;
?>
<div class="col-lg-12">
    <?php echo '<h1>'.$title.'</h1>'; ?>
    
<?php if( $draftPager->count() == 0 ): ?>
    <p>Welcome to Ospari. <a href="<?php echo OSPARI_ADMIN_PATH.'/draft/create' ?>" class="bold"><i class="fa fa-plus"></i> Write your first post.</a></p>
<?php endif; ?>    
    
<table class="table table-striped">
<?php foreach ( $draftPager->getItems() as $draft ): ?>
<tr>
    <td><a href="<?php echo $draft->getEditUrl(); ?>"><?php echo $this->escape( $draft->title ) ?></a></td>
    <td><?php echo $draft->edited_at ?></td>
    <td><?php if( $draft->isPublished() ) {echo 'published'; }else{ echo 'draft'; }  ?></td>
</tr>
<?php endforeach; ?>
</table>

<?php 

$draftPager->toPagination()->toHtml('?page=');

?>
</div>

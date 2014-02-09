<?php

$title = 'Dashboard';
$draftPager = $this->draftPager;
?>
<div class="col-lg-12">
    <?php echo '<h1>'.$title.'</h1>'; ?>
<table class="table">
<?php foreach ( $draftPager->getItems() as $draft ): ?>
<tr>
    <td><a href="<?php echo $draft->getEditUrl(); ?>"><?php echo $this->escape( $draft->title ) ?></a></td>
    <td><?php if( $draft->isPublished() ) {echo 'published'; }else{ echo 'draft'; }  ?></td>
</tr>
<?php endforeach; ?>
</table>

<?php 

echo $draftPager->getPanination('?page=');

?>
</div>

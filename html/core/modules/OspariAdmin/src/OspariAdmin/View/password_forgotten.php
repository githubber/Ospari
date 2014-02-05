<?php
//$this->head = __DIR__.'/tpl/head_mini.php';
//$this->tail = __DIR__-'/tpl/tail_mini.php';
$this->title ='Password Forgotten';
$form = $this->form;
?>
<?php if($this->success): ?>
<div class="alert alert-success">
    A password reset request mail has been successfully sent to you.<br>
    <a class="alert-link" href="<?php echo '/'.OSPARI_ADMIN_PATH.'/login' ?>">Back to login </a>
</div>
<?php else: ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-user"></i> Password Forgotten</h3>
            </div>
            <div class="panel-body">
              <?php echo $form->toHTML_V3(); ?>
                <p><a href="<?php echo '/'.OSPARI_ADMIN_PATH.'/login' ?>">Back to login</a></p>
            </div>
        </div>
<?php endif;?>
<?php
//$this->head = __DIR__.'/tpl/head_mini.php';
//$this->tail = __DIR__-'/tpl/tail_mini.php';
$this->title = 'Reset Password';
$form = $this->form;
?>
<?php if($this->success): ?>
<div class="alert alert-success">
    Your password has been successfully updated.<br>
    You can now login with your new password <a class="alert-link" href="<?php echo '/'.OSPARI_ADMIN_PATH.'/login' ?>">here </a>
</div>
<?php else: ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-lock"></i> Password Reset</h3>
    </div>
    <div class="panel-body">
      <?php echo $form->toHTML_V3(); ?>
        <p><a href="<?php echo '/'.OSPARI_ADMIN_PATH.'/login' ?>">back to login </a></p>
    </div>
</div>
<?php endif;


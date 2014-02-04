<?php
$this->head = __DIR__.'/tpl/head_mini.php';
$this->tail = __DIR__-'/tpl/tail_mini.php';
$title = 'Login';
$this->title = $title;

$form = $this->form;      
?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-lock"></i> Login</h3>
    </div>
    <div class="panel-body">
      <?php echo $form->toHTML_V3(); ?>
        <p><a href="<?php echo '/'.OSPARI_ADMIN_PATH.'/password/forgotten' ?>">Forgotten Password?</a></p>
    </div>
</div>

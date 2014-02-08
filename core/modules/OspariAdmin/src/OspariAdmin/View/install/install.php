<?php

$title = 'Install Ospari';
$this->title = $title;
echo '<h1>'.$title.'</h1>';

$form = $this->form;

if( $error_msg = $this->error_msg ){
    echo '<div class="alert alert-danger">'.$error_msg.'</div>';
}

echo $form->toHTML_V3();
?>


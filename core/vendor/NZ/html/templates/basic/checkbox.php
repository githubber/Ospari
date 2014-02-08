<?php
$field = $this->data[NZ\BootstrapFilterForm::FIELD_FIELD];

$checked = '';
if($this->data[NZ\BootstrapFilterForm::FIELD_SELECTED]){
    $checked = ' checked="checked" ';
}

$label = $this->data[NZ\BootstrapFilterForm::FIELD_LABEL];

?>

<?= $label; ?>:
<input type="checkbox" style="margin-top: 0px;" value="1" name="<?= $field; ?>" <?= $checked; ?>>
<?php
$field = $this->data[NZ\BootstrapFilterForm::FIELD_FIELD];
$values = $this->data[NZ\BootstrapFilterForm::FIELD_VALUES];
$selected = $this->data[NZ\BootstrapFilterForm::FIELD_SELECTED];
$label = $this->data[\NZ\BootstrapFilterForm::FIELD_LABEL];
$cssClass = $this->data[\NZ\BootstrapFilterForm::FIELD_CSS_CLASS];
$submitOnChange = false;
if(isset($this->data[\NZ\BootstrapFilterForm::SUBMIT_ON_CHANGE])){
    $submitOnChange = true;
}
?>


<?= $label; ?>:
<select name="<?= $field ?>" class="<?= $cssClass ?>" <?php if($submitOnChange){ echo 'onchange="this.form.submit();"'; } ?>>
    <?php foreach($values as $k => $v): ?>
    <option value="<?= $k ?>" <?php if($selected == (string)$k){ echo 'selected="selected"'; } ?>><?= $v ?></option>
    <?php endforeach; ?>
</select>
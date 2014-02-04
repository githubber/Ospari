<?php

$field = $this->data[NZ\BootstrapFilterForm::FIELD_FIELD];
$values = $this->data[NZ\BootstrapFilterForm::FIELD_VALUES];

?>

<input type="hidden" name="<?= $field ?>" value="<?= $values; ?>">
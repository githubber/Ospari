<?php

$title = 'Setting';
$this->title = $title;
$form = $this->form;
?>

<div class="col-lg-12">

<?php 
echo '<h1>' . $title . '</h1>';
?>
    
<?php    
echo $form->toHTML_V3($mainCol = 'col-lg-10', $col_1 = 'col-lg-2', $col_2 = 'col-lg-8');
?>
</div>
<script>


$(document).ready(
function() {
    form = document.getElementById('setting-edit-form');
    form.onsubmit = function() {
        cb = function(res) {
            bootbox.alert(res.message);
        }
        $.post(form.action, $(form).serialize(), cb);
        return false;

    }
});


</script>

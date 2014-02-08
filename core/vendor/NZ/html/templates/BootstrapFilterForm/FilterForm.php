<form class="<?= $this->cssClasses; ?> text-right">
    <div class="well well-small">
        <?php
        $filtersField = NZ\BootstrapFilterForm::FIELD_FILTERS;
        foreach($this->$filtersField as $filter){
            $this->data = $filter[NZ\BootstrapFilterForm::FIELD_DATA];
            require $filter[NZ\BootstrapFilterForm::FIELD_TEMPLATE];
        }
        ?>
        <?php if($this->setShowFilterBtn): ?>
        <button class="btn pull-right">
            <i class="icon-filter"></i>
            <?php
            $btnLabel = \NZ\BootstrapFilterForm::FILTER_BUTTON_LABEL;
            echo $this->$btnLabel;
            ?>
        </button>
        <?php endif; ?>
    </div>
</form>
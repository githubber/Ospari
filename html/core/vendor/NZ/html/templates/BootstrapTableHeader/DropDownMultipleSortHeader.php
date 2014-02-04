<th>
    <div class="btn-group">
        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#"><?= $this->escape($this->data[NZ\BootstrapTableHeader::FIELD_NAME]); ?> <b class="caret"></b></a>
        <ul class="dropdown-menu">
            <?php foreach($this->data[NZ\BootstrapTableHeader::FIELD_FIELDS] as $field): ?>
            <li>
                <a href="<?= $field[NZ\BootstrapTableHeader::FIELD_URI_ASC]; ?>"><i class="icon-sort-up"></i> <?= $field[NZ\BootstrapTableHeader::FIELD_NAME].' '.$this->bootstrapGenerator->getTranslation('ascending'); ?></a>            
            </li>
            <li>
                <a href="<?= $field[NZ\BootstrapTableHeader::FIELD_URI_DESC]; ?>"><i class="icon-sort-down"></i> <?= $field[NZ\BootstrapTableHeader::FIELD_NAME].' '.$this->bootstrapGenerator->getTranslation('descending'); ?></a>            
            </li>
            <?php endforeach; ?>
            
        </ul>
    </div>
</th>
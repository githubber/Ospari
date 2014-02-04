<th>
    <div class="btn-group">
        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#"><?= $this->escape($this->data[NZ\BootstrapTableHeader::FIELD_NAME]); ?> <b class="caret"></b></a>
        <ul class="dropdown-menu">
            <li>
                <a href="<?= $this->data[NZ\BootstrapTableHeader::FIELD_URI_ASC]; ?>"><i class="icon-sort-up"></i> <?= $this->bootstrapGenerator->getTranslation('ascending'); ?></a>            
            </li>
            <li>
                <a href="<?= $this->data[NZ\BootstrapTableHeader::FIELD_URI_DESC]; ?>"><i class="icon-sort-down"></i> <?= $this->bootstrapGenerator->getTranslation('descending'); ?></a>
            </li>
        </ul>
    </div>
</th>
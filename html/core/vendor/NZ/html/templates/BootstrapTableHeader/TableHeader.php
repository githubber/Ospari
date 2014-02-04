<thead>
    <tr>
        <?php 
        foreach($this->headers as $head){
            $this->data = $head[NZ\BootstrapGenerator::FIELD_DATA];
            require $head[NZ\BootstrapGenerator::FIELD_TEMPLATE];
        } 
        ?>
    </tr>
</thead>
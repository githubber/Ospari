
        <ul class="nav <?php echo implode(' ', $this->bootstrapNavsClasses); ?>">
            <?php foreach($this->navigationElements as $k => $v): ?>
            <li <?php if($this->currentUrlPath == $k){ echo 'class="active"'; } ?>>
                <a href="<?php echo $k ?>">
                    <?php echo $v; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
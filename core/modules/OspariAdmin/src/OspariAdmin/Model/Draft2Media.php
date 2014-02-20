<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OspariAdmin\Model;

/**
 * Description of Draft2Media
 *
 * @author fon-pah
 */
class Draft2Media extends \NZ\ActiveRecord{
    public function getTableName() {
        return OSPARI_DB_PREFIX.'drafts_medias';
    }
}

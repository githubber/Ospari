<?php
namespace OspariAdmin\Model;

/**
 * Description of Tag2Draft
 *
 * @author fon-pah
 */
class Tag2Draft extends \NZ\ActiveRecord{
    public function getTableName() {
        return OSPARI_DB_PREFIX.'tags_drafts';
    }
}

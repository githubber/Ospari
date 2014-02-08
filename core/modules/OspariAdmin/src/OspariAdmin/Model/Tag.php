<?php

namespace OspariAdmin\Model;

Class Tag extends \NZ\ActiveRecord {

    public function getTableName() {
        // not in use yet
        return '';
        //return OSPARI_DB_PREFIX.'tags';
    }
    
    /**
     * 
     * @param type $string
     * @return array
     */
    static public function create($string) {
        $arr = explode(',', $string);

        $ret = array();
        foreach ($arr as $v) {
            $tag = self::findOne(array('name' => $v));
            if (!$tag) {
                $tag = new Tag();
                $tag->name = $v;
                $tag->setCreatedAt();
                $tag->save();
            }
            $ret[$tag->id] = $tag->name;
            
        }
        return $ret;
    }

}

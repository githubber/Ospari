<?php

namespace OspariAdmin\Model;
use Ospari\Helper\TagCollection;
Class Tag extends \NZ\ActiveRecord {

    public function getTableName() {
        return OSPARI_DB_PREFIX.'tags';
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
            $tag = self::findOne(array('word' => $v));
            if (!$tag) {
                $uri = new \NZ\Uri();
                $tag = new Tag();
                $tag->word = $v;
                $tag->slug = $uri->slugify($tag->slug);
                $tag->setCreatedAt();
                $tag->save();
            }
            $ret[$tag->id] = $tag->word;
            
        }
        return $ret;
    }
    
    static public function getTags( $where=  array()){
        $tag = new Tag();
        return $tag->getTagWords($tag->findAll($where)); 
    }
    
    static public function getTagsAsString($draft_id){
        $tag = new Tag();
        $tags =$tag->getTagsByDraftId($draft_id, $tag);
        return implode(',', $tag->getTagWords($tags));
    }
    
    private function getTagWords( $tags ){
        $words = array();
        foreach ($tags as $tag) {
            $words[] = $tag->word;
        }
        return $words;
    }
    
    private function getTagsByDraftId( $draft_id, $tag){
        $tag2draft = Tag2Draft::findAll(array('draft_id'=>$draft_id));
        $tags = $tag->fetchByObjectList($tag2draft, 'tag_id', 'id');
        return $tags;
    }

    public static function getTagsAsStdObjs( $draft_id ){
        $tag = new Tag();
        $tags = $tag->getTagsByDraftId($draft_id, $tag);
        $tagObjs = array();
        foreach ($tags as $value) {
            $obj = new \stdClass();
            $obj->name = $value->word;
            $tagObjs[] =$obj;
        }
        
        return new TagCollection( $tagObjs );
        return $tagObjs;
    }

}


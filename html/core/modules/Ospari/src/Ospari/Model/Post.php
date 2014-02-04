<?php

namespace Ospari\Model;

Class Post extends \NZ\ActiveRecord{
    
    
     public function getUrl(){
        return OSPARI_URL.'/post/'.$this->slug;
    }
    
    public function getTableName() {
        return OSPARI_DB_PREFIX.'posts';
    }

        public function toStdObject() {
        $std = parent::toStdObject();
        $std->date = $this->published_at;
        $std->url = $this->getUrl();
        
        $user = new User( $this->user_id );
        $std->author = $user->toStdObject();
        return $std;
        
    }
     static public function getPager($map, $req, $perPage = 20) {
        $where = array();
        return new \NZ\Pager(new Post(), $where, $req->getInt('page'), $perPage, $order = 'published_at DESC');
    }
    
    
    
}


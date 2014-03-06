<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ospari\Helper;

/**
 * Description of TagCollection
 *
 * @author fon-pah
 */
Class TagCollection extends \ArrayObject{
    private $tags;
    
    public function __construct($array) {
        $this->tags = $array;
        parent::__construct($array);
    }
    
public function __toString() {
    
    $arr = array();
    foreach( $this->tags as $tag ){
        $arr[] = $tag->name;
    }
    
    return implode(' ', $arr);
}

    
}
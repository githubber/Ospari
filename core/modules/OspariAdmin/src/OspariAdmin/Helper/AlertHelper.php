<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OspariAdmin\Helper;

/**
 * Description of AlertHelper
 *
 * @author fon-pah
 */
class AlertHelper {
    public static function getTplAsString($body,$url=null){
        $tpl = '<p>'.$body.'</p>';
        if($url){
            $tpl.='<p>'.$url.'</p>';
        }
        return $tpl;
    }
}

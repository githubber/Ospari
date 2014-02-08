<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ModuleContainer
 *
 * @author 28h
 */

namespace NZ;

class ModuleContainer {

    //put your code here
    protected $route2module;
    protected $default;

    public function add($route, $module) {
        $this->route2module[$route] = $module;

        
    }

    public function getModule($route) {

        //echo "\n\n$route\n\n";
        //  print_r( $this->route2module );

        if (isset($this->route2module[$route])) {



            return $this->route2module[$route];
        }


        return $this->default;
    }

}

?>

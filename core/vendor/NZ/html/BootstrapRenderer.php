<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace NZ;

Class BootstrapRenderer{
    
    public function renderErrorMessage($msg){
        return '<div class="alert alert-error">'.$msg.'</div>';
    }
    
    public function renderSuccessMessage($msg){
        return '<div class="alert alert-success">'.$msg.'</div>';
    }
}
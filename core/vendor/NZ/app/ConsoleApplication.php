<?php

namespace NZ;

Class ConsoleApplication extends \NZ\Application{
    
   protected function bootsrap() {
        $this->lodModules();
    }
    
    protected function processRoutes($module){
        /** do nothing **/
    }
    
    public function run($request_uri = NULL) {
        /** do nothing **/
    }
    
    
}

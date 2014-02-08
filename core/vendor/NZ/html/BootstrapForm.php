<?php

namespace NZ;

class BootstrapForm {

    protected $view;
    protected $req;
    protected $actionURL;
    protected $submitClasses = array();

    /**
     *
     * @var \NZ\BootstrapFormElement[];
     */
    protected $elements = array();
    protected $hiddenEelements = array();

    const METHOD_POST = 'post';
    const METHOD_GET = 'get';

    protected $method = 'post';
    protected $submitValue = 'send';
    protected $cssClasses = array();
    protected $hasFormActions = true;
    protected $hasWell = FALSE;
    protected $id;
    protected $name;
    protected $encType;
    protected $onsubmit;

    /**
     * 
     * @param \NZ\View $view
     * @param \NZ\HttpRequest $req
     */
    function __construct(View $view, HttpRequest $req) {
        $this->view = $view;
        $this->req = $req;
        $this->actionURL = \NZ\Uri::getCurrent();
        $this->id = 'bform-id-' . time();
        $this->name = 'bform';
    }

    /**
     * 
     * @param \NZ\ActiveRecord $model
     * @return type
     * @throws \Exception
     */
    public function saveToModel( \NZ\ActiveRecord $model ){
        $req = $this->req;
        if( !$this->validate($req) ){
            throw new \Exception( 'Form not validated' );
        }
        
        foreach( $this->getElements() as $k => $v ){
            $model->set($k, $req->get( $k ));
        }
        
        return $model->save();
    }
            
    public function setID($id) {
        $this->id = $id;
        return $this;
    }

    public function getID() {
        return $this->id;
    }

    public function setAction($url) {
        $this->actionURL = $url;
        return $this;
    }

    public function getAction() {
        return $this->actionURL;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function setCssClass($class) {
        $this->cssClasses = array($class);
    }

    public function addCssClass($class) {
        $this->cssClasses[] = $class;
    }

    public function setLegend($legend) {
        $this->legend = $legend;
    }

    public function setHasFormActions($hasFormActions) {
        $this->hasFormActions = $hasFormActions;
    }

    public function setHasWell($hasWell) {
        $this->hasWell = $hasWell;
    }

    public function setEncType($encType) {
        $this->encType = $encType;
    }

    public function getEncType() {
        return $this->getEncType;
    }

    public function getName() {
        return $this->name;
    }
    public function setOnSumbit( $event ){
        $this->onsubmit = $event;
    }
    public function getOnSubmit(){
        return $this->onsubmit;
    }

    /**
     * 
     * @param string $name
     * @return \NZ\BootstrapFormElement
     */
    public function createElement($name) {
        $el = new BootstrapFormElement($name, $this->view, $this->req);
        $this->elements[$name] = $el;
        return $el;
    }
    
    /**
     * 
     * @param string $name
     * @return \NZ\BootstrapFormElement
     */
    public function addElement(\NZ\BootstrapFormElement $el ) {
        $this->elements[$el->getName()] = $el;
        return $el;
    }

    /**
     * 
     * @param string $name
     * @param string $value please escape the value
     * @return string
     */
    public function createHiddenElement($name, $value, $id = '') {
        $el = '<input type="hidden" name="' . $name . '" value="' . $value . '" id="' . $id . '"/>';
        $this->hiddenEelements[$name] = $el;
        return $el;
    }

    /**
     * 
     * @return \NZ\BootstrapFormElement[]
     */
    public function getElements() {
        return $this->elements;
    }
    
    /**
     * 
     * @param string $name
     * @return \NZ\BootstrapFormElemen|null
     */
    public function getElement( $name ) {
        if( isset($this->elements[$name]) ){
            return $this->elements[$name];
        }
        return NULL;
    }
    

    /**
     * 
     * @return array
     */
    public function getHiddenElements() {
        return $this->hiddenEelements;
    }

    public function toHTML_V3($mainCol = 'col-lg-12', $col_1 = 'col-lg-2', $col_2 = 'col-lg-10') {
        $formClasses = implode(' ', $this->cssClasses);
        $classHTML = '';
        if ($formClasses) {
            $classHTML = ' class="' . $formClasses . '"';
        }

        $content = '<div class="' . $mainCol . '"><form action="' . $this->actionURL . '" target="_self" role="form" method="' . $this->method . '" id="' . $this->id . '"' . $classHTML . ' name="' . $this->name . '" ' . ($this->encType ? 'enctype="' . $this->encType . '"' : '') . ' > 
                        ';
        foreach ($this->elements as $el) {
            $content .= $el->toHTML_V3($col_1, $col_2);
        }

        foreach ($this->hiddenEelements as $el) {
            $content .= $el;
        }

        $submitClass = implode(' ', $this->submitClasses);

        $content .= '<div class="control-group"><div class="'.$col_1.'"> </div> <div class="text-right '.$col_2.'">'.$this->view->submitInput($this->submitValue, ' class="' . $submitClass . '"').'</div></div>';

        $content .= '
                    </form>';
        $content .= '</div>';
        return $content;
    }

    public function toHTML() {
        $formClasses = implode(' ', $this->cssClasses);
        $classHTML = '';
        if ($formClasses) {
            $classHTML = ' class="' . $formClasses . '"';
        }

        $content = '<form action="' . $this->actionURL . '" target="_self" method="' . $this->method . '" id="' . $this->id . '"' . $classHTML . ' name="' . $this->name . '" ' . ($this->encType ? 'enctype="' . $this->encType . '"' : '') .' ' . ($this->onsubmit ? 'onsubmit="' . $this->onsubmit . '"' : '') .' > 
                        <fieldset>';

        if (isset($this->legend)) {
            $content .= '<legend>' . $this->legend . '</legend>';
        }

        $wellClass = '';
        if ($this->hasWell) {
            $wellClass = 'well';
        };

        $content .= '       <div class="' . $wellClass . '" id="form-content">';

        foreach ($this->elements as $el) {
            $content .= $el->toHTML();
        }

        foreach ($this->hiddenEelements as $el) {
            $content .= $el;
        }

        if ($this->hasFormActions) {
            $content .= '</div>
                            <div class="form-actions">';
        }

        
        $submitClassesArray = $this->submitClasses;;
        if( !$submitClassesArray ){
            $submitClassesArray = array('btn btn-primary');
        }
        
        $submitClass = implode(' ', $this->submitClasses);
        if ($this->hasFormActions) {
            $submitClass .= ' offset3';
//            $submitClass .= ' pull-right';
        };
        $content .= $this->view->submitInput($this->submitValue, ' class="' . $submitClass . '"');

        $content .= '</div>';

        $content .= '</fieldset>
                    </form>';
        return $content;
    }

    public function setSubmitValue($value) {
        $this->submitValue = $value;
    }

    public function addSubmitClass($class) {
        $this->submitClasses[] = $class;
    }

    public function removeElement($name) {
        unset($this->elements[$name]);
    }

    public function validate(\NZ\HttpRequest $req) {
        $ret = TRUE;
        foreach ($this->elements as $name => $el) {
            if ($el->getAttribute('disabled')) {
                $req->removeParam($name);
            }

            if ($el->isRequired()) {
                if (!$req->get($name)) {
                    $req->setErrorMessage($name, 'This is a required field.');
                    $ret = FALSE;
                }
            }

            if (!$el->validate($req)) {
                $ret = FALSE;
            }
        }

        return $ret;
    }

}

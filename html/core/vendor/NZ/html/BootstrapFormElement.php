<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NZ;

/**
 * Description of FormElement
 *
 * @author 28h
 */
class BootstrapFormElement {

    /**
     *
     * @var \View
     */
    protected $view;

    /**
     *
     * @var \NZ_HttpRequest 
     */
    protected $req;
    protected $id;
    protected $type = 'text';
    protected $value;
    protected $name;
    protected $isRequired = false;
    protected $labelText;
    protected $selectOpts = array();
    protected $helpText_html;
    protected $helpText;
    protected $cssClasses = array('input-xlarge' => 'input-xlarge');
    protected $attr = array();
    protected $hasControlGroup = true;
    protected $validator = null;
    private $errorMessage = 'invalid_value';
    
    public function __construct($name, $view, $req) {

        $this->view = $view;
        $this->req = $req;

        $this->setValue($req->get($name));
        $this->name = $name;
        $this->id = 'nz-bt-' . $name;

        return $this;
    }

    public function setAttribute($key, $value) {
        $this->attr[$key] = $value;
        return $this;
    }

    public function getAttribute($key) {
        if (isset($this->attr[$key])) {
            return $this->attr[$key];
        }
        return NULL;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setView(\NZ_View $view) {
        $this->view = $view;
        return $this;
    }

    public function setRequest(\NZ\HttpRequest $req) {
        $this->req = $req;
        return $this;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setHasControlGroup($hasCtrlGrp) {
        $this->hasControlGroup = $hasCtrlGrp;
        return $this;
    }

    public function addClass($cssClass) {
        $this->cssClasses[$cssClass] = $cssClass;
        return $this;
    }

    public function setCssClass($cssClass) {
        $this->cssClasses = array($cssClass => $cssClass);
        return $this;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setValue($value) {
        $this->value = $this->view->escape($value);
        return $this;
    }

    public function setRequired() {
        $this->isRequired = TRUE;
        return $this;
    }

    public function isRequired() {
        if ($this->getAttribute('disabled')) {
            return FALSE;
        }
        return $this->isRequired;
    }

    public function inArray($arr) {
        $this->validator = new \Zend\Validator\InArray($arr);
        return $this;
    }

    public function setIsEmail() {
        $this->validator = new \Zend\Validator\EmailAddress();
        $this->setErrorMessage('invalid_email');
        return $this;
    }

    public function setBetween($min, $max) {
        $this->validator = new \ZendTest\Validator\BetweenTest(array($min, $max));
        return $this;
    }

    public function setErrorMessage($msg) {
        $this->errorMessage = $msg;
    }

    public function validate($req) {
        if (isset($this->validator)) {
            if (!$this->validator->isValid($this->value)) {
                $req->setErrorMessage($this->name, $this->errorMessage);
                return false;
            }
        }
        return true;
    }

    public function isBetween() {
        if (count($this->between) == 0) {
            return FALSE;
        }

        $validator = new \Zend\Validator\Between(array($this->minValue, $this->maxValue));
        return $validator->isValid($this->value);
    }

    public function setLabelText($text) {
        $this->labelText = $text;
        return $this;
    }

    public function getLabelText() {
        return $this->labelText;
    }

    /**
     *  The given string would be escaped!
     * @param string $text
     * @return \NZ_Bootstrap\FormElement 
     */
    public function setHelpText($text) {
        $this->helpText_html = ' <p class="help-block small muted">' . $text . '</p>';
        $this->helpText;

        return $this;
    }

    public function renderInput() {
        $reqiredAttr = '';
        if ($this->isRequired) {
            $reqiredAttr = ' required="true"';
        }

        foreach ($this->attr as $k => $v) {
            $reqiredAttr .= ' ' . $k . '="' . $v . '"';
        }

        if ($this->type == 'select') {
            $cssClass_html = '';
            $cssClasses = $this->cssClasses;
            unset( $cssClasses['input-xlarge'] );
            if( $cssClasses ){
                $cssClass_html = 'class="' . implode(' ', $cssClasses) . '"';
            }
            
            return '<select' . $reqiredAttr . ' name="' . $this->name . '" id="' . $this->id . '"'.$cssClass_html.'>' . $this->view->options($this->selectOpts, $this->value) . '</select>';
        }
        
        if ($this->type == 'radio') {
            $view = $this->view;
            $radioBoxes =  $view->radioBoxes($this->name, $this->selectOpts, $this->req->get($this->name), $attr = '');
            $h = '';
            foreach( $radioBoxes as $text => $input ){
                $h .= '<label class="radio">'.$input.''.$text.'</label>';
            }
            return $h;
        }
        

        if ($this->type == 'textarea') {
            if( !$rows = $this->getAttribute('rows') ){
                $rows = 5;
            }
            if( !$cols = $this->getAttribute('cols') ){
                $cols = 8;
            }
            
            return '<textarea' . $reqiredAttr . ' cols="'.$cols.'"  rows="'.$rows.'" name="' . $this->name . '" class="' . implode(' ', $this->cssClasses) . '">' . $this->value . '</textarea>';
        }
        return '<input' . $reqiredAttr . ' type="' . $this->type . '" name="' . $this->name . '" value="' . $this->value . '" id="' . $this->id . '" class="' . implode(' ', $this->cssClasses) . '">';
    }

    public function toTexArea() {
        $this->type = 'textarea';
        return $this;
    }
    
    /**
     * extend this class and write your own ::toHTML() mehod if you use this method.
     * @return \NZ\BootstrapFormElement
     */
    public function toCheckbox() {
        $this->type = 'checkbox';
        return $this;
    }

    public function toSelect($opts = array()) {
        $this->selectOpts = $opts;
        $this->type = 'select';
        return $this;
    }
    public function toRadio($opts = array()) {
        $this->selectOpts = $opts;
        $this->type = 'radio';
        return $this;
    }

    public function hasError() {
        if ($this->req->getErrorMessage($this->name)) {
            return TRUE;
        }
        return FALSE;
    }

    public function toHTML() {
        $controlClass = '';
        $helpText = $this->helpText_html;
        if ($msg = $this->req->getErrorMessage($this->name)) {
            $controlClass = ' error';
            $helpText = $msg;
        }

        $html = '';
        if ($this->hasControlGroup) {
            $html .= '<div class="control-group' . $controlClass . '">';
        }
        if (isset($this->labelText)) {
            $requiredHTML = '';
            if ($this->isRequired) {
                $requiredHTML = '<sup>*</sup>';
            }
            $html .= '<label class="control-label" for="' . $this->name . '">' . $this->labelText . $requiredHTML . '</label>';
        }
        if ($this->hasControlGroup) {
            $html .= '<div class="controls">';
        }

        if ($helpText && !$this->hasControlGroup) {
            $html .= '<div class="text-error">' . $helpText . '</div>';
        }
        $html .= $this->renderInput();

        if ($helpText && $this->hasControlGroup) {
            $html .= '<div class="text-error">' . $helpText . '</div>';
        }


        if ($this->hasControlGroup) {
            $html .= '</div></div>';
        }

        return $html;
    }

    public function toHTML_V3($col_1 = 'col-lg-2', $col_2 = 'col-lg-5') {
        $controlClass = '';
        $helpText = $this->helpText_html;
        if ($msg = $this->req->getErrorMessage($this->name)) {
           
            $helpText = $msg;
        }

        $html = '';
        
        $errorClass = '';
        if ($this->hasError()) {
            $errorClass = ' has-error';
        };

        $html .= ' <div class="form-group' . $errorClass . '">
            ';

        if (isset($this->labelText)) {
            $requiredHTML = '';
            if ($this->isRequired) {
                $requiredHTML = '<sup>*</sup>';
            }
            $html .= '<label class="control-label '.$col_1.'" for="' . $this->name . '">' . $this->labelText . $requiredHTML . '</label>';
        }

        
        
        $html .= '<div class="'.$col_2.'">';
        $this->addClass('form-control');
        $html .= $this->renderInput();
       
        
        if ($helpText && $this->hasControlGroup) {
            $html .= '<span class="help-block">' . $helpText . '</span>';
        }

         $html .= '</div>';

        $html .= '
            </div>
            ';


        return $html;
    }

    public function getId() {
        return $this->id;
    }

}


<?php

namespace NZ;

Class Pagination {

    private $currentPage;
    private $pageList = array();
    private $backward = 0;
    private $forward = 0;
    private $_totalPages = 0;
    private $onClick;

    public function __construct($currentPage = 1, $totalPages, $pagesAround = 3) {
        $this->_totalPages = $totalPages;
        $new_cur_page = $currentPage;
        $new_all_pages = $totalPages;
        $new_show_pages = $pagesAround;

        $new_cur_page = $new_cur_page - $new_show_pages;
        if ($new_cur_page < 1) {
            $new_cur_page = 1;
        }

        $pagesList = array();

        $pageList = array();

        for ($i = 0; $i <= ($new_show_pages * 2); $i++) {
            if ($new_cur_page <= $new_all_pages) {
                $my_new_cur_page = $new_cur_page++;
                $pageList[] = $my_new_cur_page;
            }
        }

        $this->pageList = $pageList;
        $this->currentPage = $currentPage;

        if ($currentPage > 1) {
            $this->backward = $currentPage - 1;
        }

        if ($currentPage < $totalPages) {
            $this->forward = $currentPage + 1;
        }
    }

    public function setOnclick($onClick) {
        $this->onClick = $onClick;
    }

    public function hasNext() {
        return ($this->forward > 0);
    }

    public function hasPrevious() {
        return ($this->backward > 0);
    }

    public function getNext() {
        return $this->forward;
    }

    public function getPrevious() {
        return $this->backward;
    }

    public function getNext_html($href = "", $attr = "", $text = null) {
        if ($this->onClick) {

            return "<a href=\"" . $href . $this->forward . "\" " . $attr . " onclick=\" return " . $this->onClick . "\" rel=\"next\" class=\"navpage next\">" . $text . "</a>";
        } else {
            return "<a href=\"" . $href . $this->forward . "\" " . $attr . "  rel=\"next\" class=\"navpage next\">" . $text . "</a>";
        }
        return null;
    }

    public function getPrevious_html($href = "", $attr = "", $text = null) {
        if ($this->onClick) {

            return "<a href=\"" . $href . $this->backward . "\" " . $attr . " onclick=\" return " . $this->onClick . "\" rel=\"prev\" class=\"navpage previous\">" . $text . "</a>";
        } else {
            return "<a href=\"" . $href . $this->backward . "\" " . $attr . " class=\"navpage previous\">" . $text . "</a>";
        }
        return null;
    }

    public function getLast_html($href = "", $attr = "", $text = null) {
        if ($this->onClick) {

            return "<a href=\"" . $href . $this->_totalPages . "\" " . $attr . " onclick=\" return " . $this->onClick . "\"  class=\"navpage last\">" . $text . "</a>";
        } else {
            return "<a href=\"" . $href . $this->_totalPages . "\" " . $attr . " class=\"navpage last\">" . $text . "</a>";
        }
    }

    public function getFirst_html($href = "", $attr = "", $text = null) {
        if ($this->onClick) {

            return "<a href=\"" . $href . "1\" " . $attr . " onclick=\" return " . $this->onClick . "\"  class=\"navpage first\">" . $text . "</a>";
        } else {
            return "<a href=\"" . $href . "1\" " . $attr . " class=\"navpage first\">" . $text . "</a>";
        }
    }

    public function getPageList() {
        return $this->pageList;
    }

    public function getPageList_html($href = "", $attr = "", $text = null) {
        foreach ($this->pageList as $k => $page) {
            if ($text == null) {
                $text = $page;
            }

            if ($page == $this->currentPage) {
                $html = '<span class="navpage current">' . $page . '</span>';
            } else {
                if ($this->onClick) {
                    $html = '<a href="' . $href . $page . '" ' . $attr . ' onclick=" return ' . $this->onClick . '" class="navpage">' . $text . '</a>';
                } else {
                    $html = "<a href=\"" . $href . $page . "\" " . $attr . " class=\"navpage\">" . $text . "</a>";
                }
            }
            unset($this->pageList[$k]);
            return $html;
        }
        return false;
    }

    public function create($href = "", $attr = "") {
        $pages = '';
        $pages .= $this->getPrevious_html($href, $attr, " &lt; ") . " ";

        while ($p = $this->getPageList_html($href)) {
            $pages .= $p . " ";
        }

        $pages .= $this->getNext_html($href, $attr, " &gt; ") . " ";
        return $pages;
    }

    public function toHtml($href = "", $attr = "", $onclick = "",$version="") {
        if ($this->_totalPages < 2) {
            return '';
        }

        if($version==3){
            $pages = '<ul class="pagination">';
        }
        else{
            $pages = '<ul>';
        }
        $pages .= '<li>'.$this->getFirst_html($href, $attr, "&lt;&lt;") . '</li> ';

        $pages .= '<li>'.$this->getPrevious_html($href, $attr, "&lt;") . '</li> ';

        while ($p = $this->getPageList_html($href)) {
            $pages .= '<li>'.$p . "</li> ";
        }

        $pages .= '<li>'.$this->getNext_html($href, $attr, "&gt;") . '</li> ';

        $pages .= '<li>'.$this->getLast_html($href, $attr, "&gt;&gt;") . '</li> ';
		$pages .= '</ul>';
        return $this->pagerWrapper($pages);
    }
	private function pagerWrapper($pager){
		return '<div class="pagination pagination-left">'.$pager.'</div>';
	}
}


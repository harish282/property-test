<?php
namespace SohamGreens\SimpleMvc;

/**
 * Descritption: This file is used to handle pagination of application.
 *
 *
 * @package includes
 * @subpackage classes
 * @author Harish Kumar Chauhan <harish@sohamgreens.com>
 * @version 1.0.0
 * @access public
 *
 */
class Paginator {

    public static $recordsOnPage = 10;
    public static $scrollPages = 10;
    public static $variableName = 'page';
    private $totalRecords = 1;   ///Total Records returned by sql query
    private $recordsPerPage = 1;    ///how many records would be displayed at a time
    private $pageName = ""; ///page name on which the class is called
    private $start = 0;
    private $page = 0;
    private $totalPage = 0;
    private $currentPage;
    private $remainPage;
    private $showPrevNext = true;
    private $showScrollPrevNext = false;
    private $showFirstLast = false;
    private $showDisabledLinks = true;
    private $scrollPage = 0;
    private $queryString = "";
    private $linkParam = "";
    private $jsHandler = "";
    private $reset = false;
    private $pageVar = "";

    /*
     * Generic constructor
     */

    public function __construct($pageVarName = '') {
        $this->setPageVariableName($pageVarName);
    }

    function __get($name) {
        return $this->$name;
    }

    /**
     * sets the custom variable name for paging
     *
     * @return Boolean
     */
    public function setPageVariableName($pageVarName) {
        $this->pageVar = empty($pageVarName) ? 'page' : $pageVarName;
    }

    /**
     * returns true if it is the last pge.
     *
     * @return Boolean
     */
    public function isLastPage() {
        return $this->page >= $this->totalPage - 1 ? true : false;
    }

    /**
     * Return true if it is first page.
     *
     * @return Boolean
     */
    public function isFirstPage() {
        return $this->page == 0 ? true : false;
    }

    /**
     * Returns the current page.
     *
     * @return Number
     */
    public function currentPage() {
        return $this->page + 1;
    }

    /**
     * Return the total page.
     *
     * @return Number
     */
    public function totalPage() {
        return $this->totalPage == 0 ? 1 : $this->totalPage;
    }

    /**
     * Whether to show disabled links or not.
     *
     * @param Boolean $show
     */
    public function showDisabledLinks($show = TRUE) {
        $this->showDisabledLinks = $show;
    }

    /**
     * Set the link parameter. i.e class etc
     *
     * @param String $linkParam
     */
    public function setLinkParameter($linkParam) {
        $this->linkParam = $linkParam;
    }

    /**
     * Function set the page name.
     *
     * @param String $pageName
     */
    public function setPageName($pageName) {
        $this->pageName = Url::site($pageName);
    }

    /**
     * Function used to reset the pagination.
     *
     */
    public function reset() {
        $this->reset = true;
    }

    /**
     * public function is used to set the javascript handler public function
     * @param $jsHandler: String Javascript public function name
     * @return  void;
     * @uses public function will use javascript handler public function for pagging. It will pass the two parameters to
     * javascript public function. First is page name and the second one is query string.
     */
    public function setJSHandler($jsHandler) {
        $this->jsHandler = $jsHandler;
    }

    /**
     * Set the query string for links into pagination.
     *
     */
    public function setQueryString($str = "", $usePost = true) {
        if (!empty($str))
            $this->queryString = "&amp;" . preg_replace("/(&|&amp;)?" . $this->pageVar . "=\d*/", "", $str);

        if ($usePost) {
            $this->queryString .= $this->httpParseQuery(array_merge($_GET, $_POST));
        }

        $this->queryString = preg_replace("/(&|&amp;)?" . $this->pageVar . "=\d*/", "", $this->queryString);
        if (!empty($this->queryString))
            $this->queryString = '&' . $this->queryString;
    }

    /**
     * Get the query string for links into pagination.
     *
     */
    public function getQueryString() {
        return $this->queryString;
    }

    /**
     * Sets the scroll paging
     *
     * @param Number $scroll_num
     */
    public function setScrollPage($scroll_num = 0) {
        if ($scroll_num != 0)
            $this->scrollPage = $scroll_num;
        else
            $this->scrollPage = $this->totalRecords;
    }

    /**
     * Sets the total records.
     *
     * @param Number $totalRecords
     */
    public function setTotalRecords($totalRecords) {
        if ($totalRecords < 0)
            $totalRecords = 0;
        $this->totalRecords = $totalRecords;
    }

    public function setRecordsPerPage($recordsPerPage) {
        if ($recordsPerPage <= 0)
            $recordsPerPage = $this->totalRecords;
        $this->recordsPerPage = $recordsPerPage;
        self::$recordsOnPage = $this->recordsPerPage;
    }

    /* @params
     * 	$pageName = Page name on which class is integrated. i.e. $_SERVER['PHP_SELF']
     *  	$totalRecords=Total records returnd by sql query.
     * 	$recordsPerPage=How many projects would be displayed at a time
     * 		$scroll_num= How many pages scrolled if we click on scroll page link.
     * 				i.e if we want to scroll 6 pages at a time then pass argument 6.
     * 	$showPrevNext= boolean(true/false) to show prev Next Link
     * 	$showScrollPrevNext= boolean(true/false) to show scrolled prev Next Link
     * 	$showFirstLast= boolean(true/false) to show first last Link to move first and last page.
     */

    public function setPageData($pageName, $totalRecords, $recordsPerPage = 1, $scroll_num = 0, $showPrevNext = true, $showScrollPrevNext = false, $showFirstLast = false) {
        $this->setTotalRecords($totalRecords);
        $this->setRecordsPerPage($recordsPerPage);
        $this->setScrollPage($scroll_num);
        $this->setPageName($pageName);
        $this->showPrevNext = $showPrevNext;
        $this->showScrollPrevNext = $showScrollPrevNext;
        $this->showFirstLast = $showFirstLast;
    }

    /* @params
     *  $user_link= if you want to display your link i.e if you want to user '>>' instead of [first] link then use
      Page::getFirstPageNav(">>") OR for image
      Page::getFirstPageNav("<img src='' alt='first'>")
      $linkParam: link parameters i.e if you want ot use another parameters such as class.
      Page::getFirstPageNav(">>","class=myStyleSheetClass")
     */

    public function getFirstPageNav($user_link = "", $linkParam = "", $returnHtml = false) {
        if ($this->totalPage <= 1)
            return;
        if (trim($user_link) == "")
            $user_link = "[First]";

        if (empty($linkParam))
            $linkParam = $this->linkParam;

        $html = "";
        if (!$this->isFirstPage() && $this->showFirstLast) {
            if (empty($this->jsHandler))
                $html = '<a href="' . $this->pageName . (empty($this->queryString) ? '' : '?') . $this->queryString . '" ' . $linkParam . ' class="first">' . $user_link . '</a> ';
            else
                $html = '<a href="javascript:void(0);" onclick="' . $this->jsHandler . '(\'' . $this->pageName . '\',\'page=0' . $this->queryString . '\')" ' . $linkParam . ' class="first">' . $user_link . '</a> ';
        }
        elseif ($this->showFirstLast && $this->showDisabledLinks)
            $html = '<span class="first">' . $user_link . '</span>';

        if ($returnHtml)
            return $html;

        echo $html;
    }

    public function getLastPageNav($user_link = "", $linkParam = "", $returnHtml = false) {
        if ($this->totalPage <= 1)
            return;
        if (trim($user_link) == "")
            $user_link = "[Last]";

        if (empty($linkParam))
            $linkParam = $this->linkParam;

        $html = "";

        if (!$this->isLastPage() && $this->showFirstLast) {
            if (empty($this->jsHandler))
                $html = ' <a href="' . $this->pageName . '?' . $this->pageVar . '=' . ($this->totalPage - 1) . $this->queryString . '" ' . $linkParam . ' class="last">' . $user_link . '</a> ';
            else
                $html = ' <a href="javascript:void(0);" onclick="' . $this->jsHandler . '(\'' . $this->pageName . '\',\'' . $this->pageVar . ($this->totalPage - 1) . $this->queryString . '\')" ' . $linkParam . ' class="last">' . $user_link . '</a> ';
        }
        elseif ($this->showFirstLast && $this->showDisabledLinks)
            $html = ' <span class="last">' . $user_link . '</span>';

        if ($returnHtml)
            return $html;

        echo $html;
    }

    public function getNextPageNav($user_link = "", $linkParam = "", $returnHtml = false) {
        if ($this->totalPage <= 1)
            return;
        if (trim($user_link) == "")
            $user_link = " Next &raquo;";

        if (empty($linkParam))
            $linkParam = $this->linkParam;

        $html = "";
        if (!$this->isLastPage() && $this->showPrevNext) {
            if (empty($this->jsHandler))
                $html = ' <a href="' . $this->pageName . '?' . $this->pageVar . '=' . ($this->page + 1) . $this->queryString . '" ' . $linkParam . ' class="next">' . $user_link . '</a> ';
            else
                $html = ' <a href="javascript:void(0);" onclick="' . $this->jsHandler . '(\'' . $this->pageName . '\',\'' . $this->pageVar . ($this->page + 1) . $this->queryString . '\')" ' . $linkParam . ' class="next">' . $user_link . '</a> ';
        }
        elseif ($this->showPrevNext && $this->showDisabledLinks)
            $html = ' <span class="next">' . $user_link . '</span>';

        if ($returnHtml)
            return $html;

        echo $html;
    }

    public function getPrevPageNav($user_link = "", $linkParam = "", $returnHtml = false) {
        if ($this->totalPage <= 1)
            return;
        if (trim($user_link) == "")
            $user_link = "&laquo; Prev ";

        if (empty($linkParam))
            $linkParam = $this->linkParam;

        $html = "";
        if (!$this->isFirstPage() && $this->showPrevNext) {
            if (empty($this->jsHandler)) {
                if ($this->page - 1 == 0)
                    $html = ' <a href="' . $this->pageName . (empty($this->queryString) ? '' : '?') . $this->queryString . '" ' . $linkParam . ' class="prev">' . $user_link . '</a> ';
                else
                    $html = ' <a href="' . $this->pageName . '?' . $this->pageVar . '=' . ($this->page - 1) . $this->queryString . '" ' . $linkParam . ' class="prev">' . $user_link . '</a> ';
            } else
                $html = ' <a href="javascript:void(0);" onclick="' . $this->jsHandler . '(\'' . $this->pageName . '\',\'' . $this->pageVar . ($this->page - 1) . $this->queryString . '\')" ' . $linkParam . ' class="prev">' . $user_link . '</a> ';
        }
        elseif ($this->showPrevNext && $this->showDisabledLinks)
            $html = ' <span class="prev">' . $user_link . '</span>';

        if ($returnHtml)
            return $html;

        echo $html;
    }

    public function getScrollPrevPageNav($user_link = "", $linkParam = "", $returnHtml = false) {

        if ($this->scrollPage >= $this->totalPage)
            return;
        if (trim($user_link) == "")
            $user_link = "Prev[$this->scrollPage]";

        $html = "";
        if ($this->page > $this->scrollPage && $this->showScrollPrevNext) {
            if (empty($this->jsHandler))
                $html = ' <a href="' . $this->pageName . '?' . $this->pageVar . '=' . ($this->page - $this->scrollPage) . $this->queryString . '" ' . $linkParam . ' class="scroll_prev">' . $user_link . '</a> ';
            else
                $html = ' <a href="javascript:void(0);" onclick="' . $this->jsHandler . '(\'' . $this->pageName . '\',\'' . $this->pageVar . ($this->page - $this->scrollPage) . $this->queryString . '\')" ' . $linkParam . ' class="scroll_prev">' . $user_link . '</a> ';
        }
        elseif ($this->showScrollPrevNext && $this->showDisabledLinks)
            $html = ' <span class="scroll_prev">' . $user_link . '</span>';

        if ($returnHtml)
            return $html;

        echo $html;
    }

    public function getScrollNextPageNav($user_link = "", $linkParam = "", $returnHtml = false) {
        if ($this->scrollPage >= $this->totalPage)
            return;
        if (trim($user_link) == "")
            $user_link = "Next[$this->scrollPage]";

        if (empty($linkParam))
            $linkParam = $this->linkParam;

        $html = "";
        if ($this->totalPage > $this->page + $this->scrollPage && $this->showScrollPrevNext) {
            if (empty($this->jsHandler))
                $html = ' <a href="' . $this->pageName . '?' . $this->pageVar . '=' . ($this->page + $this->scrollPage) . $this->queryString . '" ' . $linkParam . ' class="scroll_next">' . $user_link . '</a> ';
            else
                $html = ' <a href="javascript:void(0);" onclick="' . $this->jsHandler . '(\'' . $this->pageName . '\',\'' . $this->pageVar . ($this->page + $this->scrollPage) . $this->queryString . '\')" ' . $linkParam . ' class="scroll_next">' . $user_link . '</a> ';
        }
        elseif ($this->showScrollPrevNext && $this->showDisabledLinks)
            $html = ' <span class="scroll_next">' . $user_link . '</span>';

        if ($returnHtml)
            return $html;

        echo $html;
    }

    public function getNumberPageNav($linkParam = "", $returnHtml = false) {
        if (empty($linkParam))
            $linkParam = $this->linkParam;

        $j = 0;
        $scrollPage = $this->scrollPage;
        if ($this->page > ($scrollPage / 2))
            $j = $this->page - intval($scrollPage / 2);
        if ($j + $scrollPage > $this->totalPage)
            $j = $this->totalPage - $scrollPage;

        if ($j < 0)
            $i = 0;
        else
            $i = $j;

        $html = "";
        for (; $i < $j + $scrollPage && $i < $this->totalRecords; $i++) {
            if ($i == $this->page)
                $html .= '&nbsp;<span class="selected">' . ($i + 1) . "</span>";
            else {
                if (empty($this->jsHandler)) {
                    if ($i == 0)
                        $html .= '&nbsp;<a href="' . $this->pageName . (empty($this->queryString) ? '' : '?') . $this->queryString . '" ' . $linkParam . ' class="num" >' . ($i + 1) . '</a>';
                    else
                        $html .= '&nbsp;<a href="' . $this->pageName . '?' . $this->pageVar . '=' . $i . $this->queryString . '" ' . $linkParam . ' class="num" rel="nofollow">' . ($i + 1) . '</a>';
                } else
                    $html .= ' <a href="javascript:void(0);" onclick="' . $this->jsHandler . '(\'' . $this->pageName . '\',\'' . $this->pageVar . $i . $this->queryString . '\')" ' . $linkParam . ' rel="nofollow" class="num">' . ($i + 1) . '</a> ';
            }
        }

        if ($returnHtml)
            return $html;

        echo $html;
    }

    public function getNav($links = null, $linkParam = "", $returnHtml = false) {

        if ($this->totalRecords <= 0)
            return false;
        if ($this->totalRecords <= $this->recordsPerPage)
            return "";
        if (!empty($linkParam))
            $this->linkParam = $linkParam;

        $html = "";

        $this->calculate();
        $html.= $this->getFirstPageNav($links['first']? : "", $this->linkParam, $returnHtml);
        $html.= $this->getScrollPrevPageNav($links['scroll_prev']? : "", $this->linkParam, $returnHtml);
        $html.= $this->getPrevPageNav($links['prev']? : "", $this->linkParam, $returnHtml);
        $html.= $this->getNumberPageNav($this->linkParam, $returnHtml);
        $html.= $this->getNextPageNav($links['next']? : "", $this->linkParam, $returnHtml);
        $html.= $this->getScrollNextPageNav($links['scroll_next']? : "", $this->linkParam, $returnHtml);
        $html.= $this->getLastPageNav($links['last']? : "", $this->linkParam, $returnHtml);

        if ($returnHtml)
            return $html;
        return;
    }

    public function getPageNav($linkParam = "", $returnHtml = false) {
        return $this->getNav(null, $linkParam, $returnHtml);
    }

    public function calculate() {
        $this->calculateStartPage();
        $this->totalPage = @intval($this->totalRecords / $this->recordsPerPage);
        if ($this->totalRecords % $this->recordsPerPage != 0)
            $this->totalPage++;
    }

    public function getRowOffset() {
        return $this->start;
    }

    public function getRecordsLength() {
        return $this->recordsPerPage;
    }

    public function getLimitQuery($qry = "") {
        /** For mysql only * */
        $this->calculateStartPage();
        return $qry . " LIMIT $this->start,$this->recordsPerPage";
    }

    private function calculateStartPage() {
        if ($this->start > 0)
            return;

        $this->page = $_REQUEST[$this->pageVar];
        if (!is_numeric($this->page) || $this->reset)
            $this->page = 0;
        $this->start = $this->page * $this->recordsPerPage;
    }

    private function httpParseQuery($array = NULL, $convention = '%s') {

        if (count($array) == 0)
            return '';

        if (function_exists('http_build_query')) {
            $query = http_build_query($array, '', '&amp;');
        } else {
            $query = '';
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $new_convention = sprintf($convention, $key) . '[%s]';
                    $query .= $this->httpParseQuery($value, $new_convention);
                } else {
                    $key = urlencode($key);
                    $value = urlencode($value);
                    $query .= sprintf($convention, $key) . "=$value&amp;";
                }
            }
        }
        return $query;
    }

}

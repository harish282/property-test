<?php
	namespace SohamGreens\SimpleMvc;

class HtmlView extends View {

    protected $_rightCol = APP_PATH.'/src/views/right.inc.php';
    protected $_css = array();
    protected $_iecss = array();
    protected $_template = APP_PATH.'/src/views/layout.inc.php';
    protected $_js = array();

    protected function addDefaultToolbar() {
        Toolbar::addButton('Logout', 'logout.php');
    }

    public function viewRightColumn($viewName = 'blank') {
        $this->_rightCol = APP_PATH.'/src/views/' . $viewName . '.inc.php';
    }

    public function addStyle($css) {
        $this->_css[] = $css;
    }

    public function addIEStyle($css) {
        $this->_iecss[] = $css;
    }

    public function addJs($js) {
        $this->_js[] = $js;
    }


    public function setTemplete($template) {
        $this->_template = $template;
    }

    public function render($return = false) {
        if ($return)
            ob_start();

        $view = $this;
        extract($this->data);

        if (empty(Toolbar::$toolbuttons))
            $this->addDefaultToolbar();
        if (empty($this->_rightCol))
            $this->viewRightColumn('blank');
        if (isset($_SESSION['SES_TEMPLATE']) && file_exists('views/layout.' . $_SESSION['SES_TEMPLATE'] . '.inc.php')) {
            require_once 'views/layout.' . $_SESSION['SES_TEMPLATE'] . '.inc.php';
        } else {
            require_once $this->_template;
        }

        if ($return) {
            $content = ob_get_clean();
            ob_end_flush();
            return $content;
        }
    }

}

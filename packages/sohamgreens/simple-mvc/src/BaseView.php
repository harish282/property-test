<?php
namespace SohamGreens\SimpleMvc;

class View {

    protected $viewFile = "";
    protected $data = array();

    public function __construct($viewName) {
        $this->viewFile = 'views/' . $viewName . '.inc.php';
    }

    static public function instance($viewName) {
        return new View($viewName);
    }

    static public function factory($viewName) {
        return new View($viewName);
    }

    public function setTemplate($viewName) {
        $this->viewFile = 'views/' . $viewName . '.inc.php';
        return $this;
    }

    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    public function __get($key) {
        return $this->data[$key];
    }

    public function __isset($key) {
        return isset($this->data[$key]);
    }

    public function set($key, $value = null) {
        if (is_array($key))
            $this->assignVariables($key);
        else
            $this->assignVariable($key, $value);
        return $this;
    }

    public function assignVariable($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    public function assignVariables(Array $variables) {
        if (!is_array($variables))
            throw new ViewException('Invalid argument supplied');

        foreach ($variables as $key => $value)
            $this->assignVariable($key, $value);
        return $this;
    }

    public function render($return = false) {
        if ($return)
            ob_start();
        $view = $this;
        extract($this->data);
        require_once $this->viewFile;
        if ($return) {
            $content = ob_get_contents();
            ob_end_clean();
            ob_end_flush();
            return $content;
        }
    }

}

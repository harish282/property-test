<?php
namespace SohamGreens\SimpleMvc;

use FailedMessage;
use SuccessMessage;

abstract class BaseObject {

    protected $id = 0;
    protected $data = array();
    public $loaded = false;
    public static $usePaging = false; //boolean to use paging into GetAll and GetAllById
    public static $paginator = null;
    public static $generatePagingQryString = true;

    static public $idField	   = 'id'; // primary field of table	
    static public $tableName   = ''; // table name
    static public $tableFields = array(); // table fields.
    /**
     * constructor
     */
    public function __construct($id = null) {
        if (!empty($id)) {
            $this->setId($id);
            $this->load();
        }
    }

    /**
     * Get the object items
     *
     * @param mixed $key
     */
    public function __get($key) {
        /*$method =  'get'.self::_snakeToCamel($key);
        if(method_exists($this, $method)){
            return $this->$method();
        }*/
        return $this->data[$key];
    }

    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    public function __isset($key) {
        return isset($this->data[$key]);
    }
    
    public function __call($method, $args)
    {
        return $this->$method(...$args);
    }
    public static function __callStatic( $method, $args ){
        return (new static)->$method(...$args);
    }

    /**
     * Factory method for child classes to create child objects
     *
     * @param mixed $class
     * @param mixed $id
     */
    static function factory($class = null, $id = null) {
        if (is_numeric($class)) {
            $class = '';
            $id = $class;
        }
        if (empty($class)) {
            $class = get_called_class();
        }
        return new $class($id);
    }

    public static function instance($id = null) {
        return self::factory($id);
    }
    
    /*     * abstract function. need to implement by sub classed * */

    abstract public function validate(Array $data = array());

    /**
    * function returns the table fields
    *
    * @return array
    */
    public function getIdentifierFieldName()
    {
        return static::$idField;	
    }

    /**
    * function returns the table fields
    *
    * @return array
    */
    public function getDataFields()
    {
        return static::$tableFields;	
    }
    
    /**
    * function reutn the table name
    *
    * @return string
    */
    public function getTable()
    {
        return static::$tableName;
    }

    /**
     * sets the id
     *
     * @param mixed $id
     */
    public function setId($id) {
        $idfield = $this->getIdentifierFieldName();
        $this->$idfield = $id;
    }

    /**
     * retunr the id
     *
     * @return mixed
     */
    public function getId() {
        $idfield = $this->getIdentifierFieldName();
        return $this->$idfield;
    }

    /**
     * load the records from table.
     *
     * @return array
     */
    public function load() {
        $sql = 'SELECT `' . implode('`,`', $this->getDataFields()) . '` FROM ' . $this->getTable() . ' WHERE ' . $this->getIdentifierFieldName() . ' = \'' . $this->getId() . '\'';
        $db = DB::getInstance();
        $res = $db->query($sql);
        $this->data = $db->fetchAssoc($res);
        if ($this->data) {
            $this->loaded = true;
            $this->afterLoad();
        }
        return $this->data;
    }

    /**
     * load the records from table.
     *
     * @return array
     */
    public function loadBy($field, $value = '') {
        $sql = 'SELECT `' . implode('`,`', $this->getDataFields()) . '` FROM ' . $this->getTable() . ' WHERE ';

        if (is_array($field)) {
            $and = '';
            foreach ($field as $key => $value) {
                $sql .=$and . $key . ' = \'' . $value . '\'';
                $and = ' AND ';
            }
        } else {
            $sql.=$field . ' = \'' . $value . '\'';
        }

        $db = DB::getInstance();
        $res = $db->query($sql);
        $this->data = $db->fetchAssoc($res);
        if ($this->data) {
            $this->loaded = true;
            $this->afterLoad();
        }
        return $this;
    }
    
    /**
     * run after loading data from database
     * @return void
     */
    
    protected function afterLoad() {
        return;
    }

    /**
     * function return the data.
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * function return the data.
     *
     * @return array
     */
    protected function getAll($dataFields = '', $dataTable = '', $whereClause = '', $orderBy = false, $dir = "ASC", $byfield = '') {

        $dataFields = static::getDataFields();
        $dataTable = static::getTable();

        $objs = array();
        $query = 'SELECT ';

        $query .= '`' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '`';

        if ($whereClause){
            $query .= ' WHERE ' . $whereClause;
        }
            
        if (is_array($orderBy)) {
            $query .= ' ORDER BY ';
            $coma = '';
            foreach ($orderBy as $order => $dir) {
                $query .= $coma . $order . ' ' . $dir;
                $coma = ', ';
            }
        } else if ($orderBy){
            $query .= ' ORDER BY ' . $orderBy . ' ' . $dir;
        }
            
        //echo $query;
        return static::getPaginationResults($query, 'page', $byfield);
    }

    /**
     * function return the data.
     *
     * @return array
     */
    protected function getAllBy($dataFields = '', $dataTable = '', $byfield = '', $whereClause = '', $orderBy = false, $dir = "ASC") {
        return static::getAll($dataFields, $dataTable, $whereClause, $orderBy, $dir, $byfield);
    }

    /**
      alias for getSingle();
     * */
    public function get($dataFields = '', $dataTable = '', $whereClause = '', $orderBy = false, $dir = "ASC") {
        $objs = static::getAll($dataFields, $dataTable, $whereClause, $orderBy, $dir);
        //dd($objs);
        if (empty($objs))
            return null;
        return $objs[0];
    }

    /**
     * function return the data.
     *
     * @return array
     */
    public function getSingle($dataFields, $dataTable, $whereClause = '', $orderBy = false, $dir = "ASC") {
        $objs = static::getAll($dataFields, $dataTable, $whereClause, $orderBy, $dir);
        if (empty($objs))
            return null;
        return $objs[0];
    }

    public function getSingleValue($field, $dataTable, $whereClause) {
        $query = 'SELECT ';
        $query .= '`' . $field . '` FROM `' . $dataTable . '`';
        $query .= ' WHERE ' . $whereClause;

        $db = DB::getInstance();
        return $db->getSingleResult($query);
    }

    /**
     * function used to save record
     *
     */
    public function save(Array $data = null) {

        if (is_null($data) || empty($data)) {
            $data = $this->filterData();
        }


        $id = $this->getId();
        //if (!empty($data[$this->getIdentifierFieldName()]) || !empty($id)) {
        if ($this->loaded) {
            $this->update($data);
        } else {
            $this->insert($data);
        }
        return $this;
    }
    
    public function filterData(){
        $data = array();
        $dataFields = $this->getDataFields();
        foreach ($dataFields as $key) {
            if (isset($this->data[$key])) {
                $data[$key] = $this->data[$key];
            }
        }
        return $data;
    }

    /**
     * set the data
     *
     * @param array $data
     */
    public function setData(Array $data = array()) {
        $this->validate($data);
        $this->data = $data;
        return $this;
    }
    
    public function fill(Array $data ) {    
        $dataFields = $this->getDataFields();
        foreach ($dataFields as $key) {
            if (isset($data[$key])) {
                $this->data[$key] = $data[$key];
            }
        }
        $this->validate($this->data);
        return $this;
    }

    /**
     * insert the record into database
     *
     * @return boolean
     */
    public function insert(Array $data) {
        $db = DB::getInstance();

        $sql = 'INSERT INTO ' . $this->getTable() . ' SET ';
        $coma = '';
        foreach ($data as $key => $value) {
            if ($key == $this->getIdentifierFieldName() && empty($value))
                continue;

            $sql .= $coma . ' `' . $key . '` = \'' . $db->sql_safe_string($value) . '\'';
            $coma = ',';
        }
        $db->query($sql);
        $id = $db->insertID();
        $this->setId($id);
        return $this;
    }
    
    public function replace(Array $data=null) {
        $db = DB::getInstance();
        
        if (is_null($data) || empty($data)) {
            $data = $this->filterData();
        }
        
        $sql = 'REPLACE INTO ' . $this->getTable() . ' SET ';
        $coma = '';
        foreach ($data as $key => $value) {
            $sql .= $coma . ' `' . $key . '` = \'' . $db->sql_safe_string($value) . '\'';
            $coma = ',';
        }
        $db->query($sql);
        $id = $db->insertID();
        $this->setId($id);
        return $this;
    }

    static public function sInsert($table, Array $data) {
        $db = DB::getInstance();

        $sql = 'INSERT INTO ' . $table . ' SET ';
        $coma = '';

        foreach ($data as $key => $value) {
            $sql .= $coma . ' `' . $key . '` = \'' . $db->sql_safe($value) . '\'';
            $coma = ',';
        }

        $db->query($sql);
        return $db->insertID();
    }

    /**
     * function update the record to database
     *
     * @return boolean
     */
    public function update(Array $data, $where = '') {
        $db = DB::getInstance();
        $sql = 'UPDATE ' . $this->getTable() . ' SET ';
        $coma = '';
        foreach ($data as $key => $value) {
            $value = $data[$key];

            if ($key == $this->getIdentifierFieldName()) {
                $this->setId($value);
            }

            $sql .= $coma . ' `' . $key . '` = \'' . $db->sql_safe($value) . '\'';
            $coma = ',';
        }

        if (!empty($where))
            $sql .= ' WHERE ' . $where;
        else
            $sql .= ' WHERE `' . $this->getIdentifierFieldName() . '` = \'' . $this->getId() . '\'';

        $db->query($sql);
        return $this;
    }

    static public function sUpdate($table, Array $data, $where = '') {
        $db = DB::getInstance();
        $sql = 'UPDATE ' . $table . ' SET ';
        $coma = '';
        foreach ($data as $key => $value) {
            $value = $data[$key];
            $sql .= $coma . ' `' . $key . '` = \'' .  $db->sql_safe($value) . '\'';
            $coma = ',';
        }

        if (!empty($where) && is_array($where) && count($where) > 0) {
            $sql .= ' WHERE ';
            foreach ($where as $key => $value) {
                if (is_numeric($value))
                    $sql .= '`' . $key . '` = ' . $value . ' AND ';
                else
                    $sql .= '`' . $key . '` = \'' . $value . '\' AND ';
            }
            $sql = substr($sql, 0, strlen($sql) - 4);
        }else if (!empty($where))
            $sql .= ' WHERE ' . $where;

        
        return $db->query($sql);
    }

    /**
     * delete the record
     *
     * @param mixed $id
     * @return boolean
     */
    function delete(Array $conditions = array(), $dataTable = null) {
        if (empty($dataTable))
            $dataTable = $this->getTable();
        if (empty($conditions))
            $conditions = array($this->GetIdentifierFieldName() => $this->id);

        $query = 'DELETE FROM `' . $dataTable . '`';
        if (count($conditions) > 0)
            $query .= ' WHERE ';
        foreach ($conditions as $key => $value) {
            if (is_numeric($value))
                $query .= '`' . $key . '` = ' . $value . ' AND ';
            else
                $query .= '`' . $key . '` = \'' . $value . '\' AND ';
        }
        if (count($conditions) > 0)
            $query = substr($query, 0, strlen($query) - 4);
        $db = DB::getInstance();
        return $db->query($query);
    }

    //return the paginator object
    public function getPaginator() {
        return self::$paginator;
    }

    static public function getPaginationResults($query, $pagevar = 'page', $byfield = '') {
        $objs = array();
        $db = DB::getInstance();
        /*         * Pagination * */

        /*         * Pagination * */
        if (self::$usePaging) {//If doing paging for records
            //$query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
            self::$paginator = new Paginator($pagevar);
            self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
            
            $db->query($query);
            $totalRecords = $db->numRows();
            
            $res = $db->query(self::$paginator->getLimitQuery($query));
            //echo $db->last_sql;
            //$totalRecords = $db->getSingleResult("Select FOUND_ROWS()");

            self::$paginator->setQueryString();
            self::$paginator->setPageData("", $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else{
            $res = $db->query($query);
        }            

        if (!$res)
            return $objs;

        $totalRecords = $db->numRows($res);
        if ($totalRecords == 0)
            return $objs;

        while ($obj = $db->fetchObject($res)) {
            if (!empty($byfield)) {
                $objs[$obj->$byfield][] = $obj;
            } else
                $objs[] = $obj;
        }
        return $objs;
    }
    
    static function _snakeToCamel($val) {  
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));  
    }  

    protected function firstOrNew(Array $data){
        $obj = $this->loadBy($data);

        if(!$this->loaded){
            return new static;
        }
        return $obj;
    }

    public function raiseError($error){
        throw new FailedMessage($error);
    }

    public function raiseSuccess($message){
        throw new SuccessMessage($message);
    }

}

if (!function_exists('get_called_class')) {

    class class_tools {

        static $i = 0;
        static $fl = null;

        static function get_called_class() {
            $bt = debug_backtrace();

            //print_r($bt);
            $clsname = $bt[3]['class'];
            if ($clsname != __CLASS__ && $clsname != 'self' && !empty($clsname))
                return $clsname;

            if (self::$fl == $bt[2]['file'] . $bt[2]['line']) {
                self::$i++;
            } else {
                self::$i = 0;
                self::$fl = $bt[2]['file'] . $bt[2]['line'];
            }

            $lines = file($bt[2]['file']);

            preg_match_all('/([a-zA-Z0-9\_]+)::' . $bt[2]['function'] . '/', $lines[$bt[2]['line'] - 1], $matches);

            return $matches[1][self::$i];
        }

    }

    function get_called_class() {
        return class_tools::get_called_class();
    }

}
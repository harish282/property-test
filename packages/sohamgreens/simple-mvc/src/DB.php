<?php
namespace SohamGreens\SimpleMvc;

class DB {

    ///Declaration of variables

    public $host = '';
    public $user = '';
    public $password = '';
    public $database = '';
    public $conn = NULL;
    public $result = false;
    public $fields;
    public $check_fields;
    public $tbname;
    public $addNewFlag = false;
    public $last_sql = "";
    public static $dbObj = null;
    public static $log = false;

    ///End

    /*function __construct($host = "", $user = "", $password = "", $dbname = "", $open = false)
    {
        return $this->DB($host, $user, $password, $dbname, $open)   ;
    }*/
    function __construct($host = "", $user = "", $password = "", $dbname = "", $open = false) {
        if ($host != "")
            $this->host = $host;
        if ($user != "")
            $this->user = $user;
        if ($password != "")
            $this->password = $password;
        if ($dbname != "")
            $this->database = $dbname;

        if ($open)
            $this->open();
    }

    static public function getInstance() {
        if (!is_a(self::$dbObj, 'DB')) {
            self::$dbObj = new DB(env('DB_HOST'), env('DB_USER'), env('DB_PWD'), env('DB_NAME'), true);
        }

        return self::$dbObj;
    }

    function open($host = "", $user = "", $password = "", $dbname = "") { //
        if ($host != "")
            $this->host = $host;
        if ($user != "")
            $this->user = $user;
        if ($password != "")
            $this->password = $password;
        if ($dbname != "")
            $this->database = $dbname;

        if ($this->connect() === false)
            throw new \Exception($this->error());
        /* if($this->select_db()===false) return false; */
        return $this->conn;
    }

    function set_host($host, $user, $password, $dbname) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $dbname;
    }

    function affectedRows() { //-- Get number of affected rows in previous operation
        return @mysqli_affected_rows($this->conn);
    }

    function close() {//Close a connection to a  Server
        return @mysqli_close($this->conn);
    }

    function log($log = true) {
        self::$log = $log;
    }

    function connect() { //Open a connection to a Server
        
        if (is_object($this->conn))
            return true;
        // Choose the appropriate connect function

        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);

        // Connect to the database server
        if (!is_object($this->conn))
            return false;
        else
            return true;
    }

    function select_db($dbname = "") { //Select a databse
        if ($dbname == "")
            $dbname = $this->database;
        return @mysqli_select_db($this->conn, $dbname);
    }

    function create_db($dbname = "") { //Create a database
        if ($dbname == "")
            $dbname = $this->database;
        return $this->query("CREATE DATABASE " . $dbname);
    }

    function drop_db($dbname = "") { //Drop a database
        if ($dbname == "")
            $dbname = $this->database;
        return $this->query("DROP DATABASE " . $dbname);
    }

    function error() { //Get last error
        if (mysqli_connect_errno())
            return mysqli_connect_error();
        return (mysqli_error($this->conn));
    }

    function errorno() { //Get error number
        if (mysqli_connect_errno())
            return mysqli_connect_errno();
        return mysql_errno($this->conn);
    }

    function sql_safe($value) {
        return $this->sql_safe_string($value);
    }

    function sql_safe_string($value) {
        // Stripslashes
        //$value = addslashes($value);
        // Quote if not a number or a numeric string
        if (!is_numeric($value)) {
            $value = mysqli_real_escape_string($this->conn, $value);
        }
        return $value;
    }

    function query($sql = '') { //Execute the sql query
        if ($sql != 'Select FOUND_ROWS()')
            $this->last_sql = $sql;
        if (self::$log)
            file_put_contents(DIR_LOG . '/sql.log', $sql . "\n", FILE_APPEND);
        $this->result = mysqli_query($this->conn, $sql);
        return $this->result;
    }

    function numRows($result = null) { //Return number of rows in selected table
        if (!is_object($result))
            $result = $this->result;
        return (@mysqli_num_rows($result));
    }

    function fieldName($field, $result = null) {
        /* if(!is_object($result))
          $result = $this->result;
          return (@mysqli_field_name($result,$field)); */

        // Need to be implemented
    }

    function insertID() {
        return (@mysqli_insert_id($this->conn));
    }

    function data_seek($arg1, $row = 0) { ///Move internal result pointer
        if (is_object($arg1))
            $result = $arg1;
        else
            $result = $this->result;

        if (!is_object($arg1) && !is_null($arg1))
            $row = $arg1;

        return mysqli_data_seek($result, $row);
    }

    function fetchRow($result = null) {
        if (!is_object($result))
            $result = $this->result;
        return (@mysqli_fetch_row($result));
    }

    function fetchObject($result = null) {
        if (!is_object($result))
            $result = $this->result;
        return (@mysqli_fetch_object($result));
    }

    function fetchArray($arg1 = null, $mode = MYSQLI_BOTH) {
        if (is_object($arg1))
            $result = $arg1;
        else
            $result = $this->result;

        if (!is_object($arg1) && !is_null($arg1))
            $mode = $arg1;
        return (mysqli_fetch_array($result, $mode));
    }

    function fetchAssoc($result = null) {
        if (!is_object($result))
            $result = $this->result;
        return (@mysqli_fetch_assoc($result));
    }

    function freeResult($result = null) {
        if (!is_object($result))
            $result = $this->result;
        return (@mysqli_free_result($result));
    }

    function getSingleResult($sql, $fulrow = false) {
        $result = $this->query($sql);
        if ($fulrow) {
            return $this->fetchArray($result);
        } else {
            $row = $this->fetchArray($result, MYSQLI_NUM);
            $return = $row[0];
            return $return;
        }
    }

    function addNew($table_name) {
        $this->fields = array();
        $this->addNewFlag = true;
        $this->tbname = $table_name;
    }

    function edit($table_name) {
        $this->fields = array();
        $this->check_fields = array();
        $this->addNewFlag = false;
        $this->tbname = $table_name;
    }

    function replace($table_name) {
        $this->fields = array();
        $this->addNewFlag = 2;
        $this->tbname = $table_name;
    }

    /**
     * used to clear multiple record set. use this function when you call store procedures etc.
     * */
    function clearRecordsets($result = null) {
        if (!is_object($result))
            $result = $this->result;
        if (is_object($result))
            $result->free();
        while ($this->conn->next_result()) {
            if ($l_result = $this->conn->store_result()) {
                $l_result->free();
            }
        }
    }

    function lastRecordset() {
        return $this->conn->store_result();
    }

    function update() {
        foreach ($this->fields as $field_name => $value) {
            if ($value == '--DATE--')
                $qry .= $field_name . "=now(),";
            else if (strtolower(trim($value)) == 'now()')
                $qry .= $field_name . "=now(),";
            else
                $qry .= $field_name . "=\"" . $this->sql_safe_string($value) . "\",";
        }
        $qry = substr($qry, 0, strlen($qry) - 1);

        if ($this->addNewFlag === 2)
            $qry = "REPLACE INTO " . $this->tbname . " SET " . $qry;
        else if ($this->addNewFlag)
            $qry = "INSERT INTO " . $this->tbname . " SET " . $qry;
        else {
            $qry = "UPDATE " . $this->tbname . " SET " . $qry;
            if (count($this->check_fields) > 0 && is_array($this->check_fields)) {
                $qry .= " WHERE ";
                foreach ($this->check_fields as $field_name => $value)
                    $qry .= $field_name . "=\"" . $this->sql_safe_string($value) . "\" AND ";
                $qry = substr($qry, 0, strlen($qry) - 5);
            } else if (!empty($this->check_fields)) {
                $qry .= " WHERE " . $this->check_fields . " ";
            }
        }
        return $this->query($qry);
    }

}

?>

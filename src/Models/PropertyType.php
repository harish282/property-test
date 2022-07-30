<?php
namespace App\Models;

use SohamGreens\SimpleMvc\BaseObject;

class PropertyType extends BaseObject{

    static public $idField	   = 'id'; // primary field of table	
    static public $tableName   = 'property_types'; // table name
    static public $tableFields = array('id', 'title', 'description', 'created_at', 'updated_at'); // table fields.     
 
    public function validate(Array $data = array())
    {
        return true;
    }


}
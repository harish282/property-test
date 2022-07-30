<?php
namespace App\Models;

use SohamGreens\SimpleMvc\BaseObject;

class Property extends BaseObject{

    static public $idField	   = 'uuid'; // primary field of table	
    static public $tableName   = 'properties'; // table name
    static public $tableFields = array( 'uuid', 'property_type_id', 'county', 'country', 'town', 'description', 'address', 'image_full', 'image_thumbnail', 'latitude', 'longitude', 'num_bedrooms', 'num_bathrooms', 'price', 'type', 'created_at', 'updated_at'); // table fields.     
 
    public function validate(Array $data = array())
    {
        if(empty($data['uuid'])){
            $this->raiseError('Please enter your form name.');
        }
        return true;
    }


}
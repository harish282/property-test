<?php

require_once('./lib/boot.php');

use App\Models\Property;
use App\Models\PropertyType;
use Lib\PropertyApi;

//$obj = Property::firstOrNew(['uuid' =>'d8d9c545-3832-3d85-a25a-aac181479be7']);
//dd($obj);

$property_api = new PropertyApi();
$properties = $property_api->getProperties();

if($properties == false){
    echo $property_api->getError();
}else{
    foreach($properties as $property){
        $obj = Property::firstOrNew(['uuid' => $property['uuid']]);
        $obj->fill($property);
        $obj->save();

        try{
            $property_type = $property['property_type'];
            $obj = new PropertyType($property_type['id']);
            
            if(!$obj->loaded){
                $obj->fill($property_type);
                $obj->save();
            }
            
        }catch(\Exception $e){
            throw $e;
            ;//dd($property);
        }
        
    }
    //dd($properties);
}

echo "Fetched all properties and saved into database!\n";
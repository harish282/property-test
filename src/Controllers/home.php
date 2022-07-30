<?php

use App\Models\Property;
use App\Models\PropertyType;
use SohamGreens\SimpleMvc\FailedMessage;
use SohamGreens\SimpleMvc\HtmlView;
use SohamGreens\SimpleMvc\Notification;
use SohamGreens\SimpleMvc\SuccessMessage;


try {

    switch ($__ACTION) {
      
        default:
            $view = $__ACTION;
            break;
    }
} catch (SuccessMessage $e) {
    Notification::set($e->getMessage());
} catch (FailedMessage $e) {
    Notification::set($e->getMessage(), Notification::ERROR);
}
//if($__ACTION)
//$view = $__ACTION;

switch ($view) {
    default:
        $property_types = PropertyType::getAllBy('','', 'id');
        $properties = Property::getAll();
        $view = new HtmlView('home');
        $view->addStyle('https://cdn.datatables.net/v/bs4/dt-1.12.1/datatables.min.css');
        $view->assignVariable('bodyclass', 'homepage');
        $view->assignVariable('property_types', $property_types);
        $view->assignVariable('properties', $properties);
        $view->render();
}


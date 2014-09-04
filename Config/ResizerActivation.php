<?php

class ResizerActivation {

    public function beforeActivation(&$controller) {
        return true;
    }

    public function onActivation(&$controller) {
        // TODO - add settings for default values
        $controller->Setting->write('Service.Resizer.apply_to_all', 0, array(
            'title' => 'Apply Google Resizer to all images',
            'description' => 'Be sure to set a width parameter in each HtmlHelper image() call',
            'input_type' => 'checkbox',
            'editable' => 1,
        ));
    }

    public function beforeDeactivation(&$controller) {
        return true;
    }

    public function onDeactivation(&$controller) {
    }

}

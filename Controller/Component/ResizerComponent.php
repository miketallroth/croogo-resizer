<?php

App::uses('Component','Controller');

class ResizerComponent extends Component {

    /**
     * startup
     *
     * The sole purpose of the ResizerComponent is to hook in (and alias) the helper.
     * Only hook it into non-admin actions however, since we still want the
     * CroogoHtmlHelper for the admin functions.
     */
    public function startup(Controller $controller) {
        $controller->request->addDetector('admin', array('param' => 'admin', 'value' => 1));
        if (!$controller->request->is('admin')) {
            $controller->helpers['Html'] = array(
                'className' => 'Resizer.ResizerHtml',
            );
        }
    }

}

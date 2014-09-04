<?php

App::uses('HtmlHelper', 'View/Helper');

/**
 * Google Resizer Html Helper
 *
 * Based on investigation by Carlo Zottmann
 * http://carlo.zottmann.org/2013/04/14/google-image-resizer/
 */
class ResizerHtmlHelper extends HtmlHelper {

    var $resize_app = 'https://images1-focus-opensocial.googleusercontent.com/gadgets/proxy';
    var $apply_to_all = false;
    var $default_container = 'focus';   // other options?
    var $default_refresh = 604800;      // 1 week in seconds

/**
 * Constructor
 *
 * Valid settings within $settings['resizer'] are:
 *  (string) container
 *  (int) refresh, time to cache the image in seconds
 *  (int) resize_w, target width in pixels
 *  (int) resize_h, target height in pixels
 * 
 * Providing neither resize_w/h does no resizing, but still caches the image.
 * Providing one of the two performs proportional scaling (aspect ratio locked).
 * Providing both performs scaling with a possible new aspect ratio.
 */
    public function __construct(View $View, $settings = array()) {
        $settings = Hash::merge(array(
            'resizer' => array(
                'container' => $this->default_container,
                'refresh' => $this->default_refresh,
            ),
        ), $settings);

        $this->apply_to_all = Configure::read('Service.Resizer.apply_to_all');

        parent::__construct($View, $settings);
    }

/**
 * image
 * (overrides base HtmlHelper::image)
 *
 * First, look for 'resizer' index within options, if none, then simply pass
 * through as a regular image call.
 *
 * The rescaling falls back to using width/height html params if provided so
 * that you can turn off the plugin entirely and still have properly scaled
 * images, albeit slower.
 */
    public function image($path, $options = array()) {

        // convert numeric keys to text
        for ($i=0; isset($options[$i]); $i++) {
            $stand_alone_key = $options[$i];
            $options[$stand_alone_key] = 1;
            unset($options[$i]);
        }

        // make standard image tag if resizer not requested
        if (!array_key_exists('resizer', $options) && !$this->apply_to_all) {
            return parent::image($path, $options);
        }

        // normalize the resizer index and merge the options into default settings
        if (array_key_exists('resizer', $options) && !is_array($options['resizer'])) {
            $options['resizer'] = array();
        }
        $settings = Hash::merge($this->settings, $options);

        // build the new url then send it on to parent's tag builder
        $app = $this->resize_app;
        $url = '?url=' . urlencode(html_entity_decode($this->url($path,true)));
        $container = '&container=' . $settings['resizer']['container'];
        $refresh = '&refresh=' . $settings['resizer']['refresh'];

        if (array_key_exists('resize_w', $settings['resizer'])) {
            $resize_w = '&resize_w=' . $settings['resizer']['resize_w'];
        } else if (array_key_exists('width', $settings)) {
            $resize_w = '&resize_w=' . $settings['width'];
        } else {
            $resize_w = '';
        }

        if (array_key_exists('resize_h', $settings['resizer'])) {
            $resize_h = '&resize_h=' . $settings['resizer']['resize_h'];
        } else if (array_key_exists('height', $settings['resizer'])) {
            $resize_h = '&resize_h=' . $settings['height'];
        } else {
            $resize_h = '';
        }

        // require a resize of some sort in order to use the resizer
        if ($resize_w == '' && $resize_h == '') {
            return parent::image($path, $options);
        }

        $new_url = $app . $url . $container . $refresh . $resize_w . $resize_h;

        return parent::image($new_url, $options);
    }

}

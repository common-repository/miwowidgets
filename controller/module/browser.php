<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerBrowser extends MiwoWidgetsController {

    public $type    = 'common';
    public $module  = 'browser';
    public $icon    = 'admin-site';
    public $order   = 53;

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
        $this->data['status'] = '0';

        $browsers = $this->_getBrowserList();

        $rules = $this->model->getRules($this->data, 'value');
        $this->data['browsers'] = $this->prepareItems($browsers, $rules);

        $this->tpl = 'module/'.$this->module.'.tpl';
        $this->render();
    }

    public function saveRule($delete = false)
    {
        $result = parent::saveRule(true);

        echo json_encode($result);
    }

    public function saveMultiRule($delete = false)
    {
        $result = parent::saveMultiRule(true);

        echo json_encode($result);
    }

    public function getExcludedWidgets()
    {
        $browser_id = $this->_detectBrowser();

        if(empty($browser_id)) {
            return array();
        }

        $this->data = array();
        $this->data['module'] = $this->module;
        $this->data['value'] = $browser_id;
        $this->data['status'] = '0';

        $excluded_widgets = $this->model->getRules($this->data);

        return $excluded_widgets;
    }

    public function prepareItems($_items, $rules)
    {
        $items = array();

        foreach($_items as $id => $title) {
            $item        = new stdClass();
            $item->id    = $id;
            $item->title = $title;

            if(isset($rules[$id])){
                $item->checkstate = 'unchecked';
                $item->checkstatenext = 'checked';
            }
            else{
                $item->checkstate = 'checked';
                $item->checkstatenext = 'unchecked';
            }

            $items[] = $item;
        }

        return $items;
    }

    public function _getBrowserList()
    {
        $browsers = array(
            'msie' => 'Internet Explorer',
            'msie6' => 'Internet Explorer 6',
            'chrome' => 'Chrome',
            'firefox' => 'Firefox',
            'opera' => 'Opera',
            'safari' => 'Safari',
            'mozilla' => 'Mozilla',
            'webtv' => 'WebTV',
            'omniweb' => 'OmniWeb',
            'firebird' => 'Firebird',
            'konqueror' => 'Konqueror',
            'icap' => 'iCap',
            'amaya' => 'Amaya',
            'lynx' => 'Lynx',
            'shiretoko' => 'Shiretoko',
            'icecat' => 'IceCat',

            'android' => 'Android',
            'operamini' => 'Opera Mini',
            'ipad' => 'iPad',
            'ipod' => 'iPod',
            'iphone' => 'iPhone',
            'blackberry' => 'BlackBerry',
            'msiemobile' => 'Internet Explorer Mobile',
            'nokias60' => 'Nokia S60 OSS Browser',
            'nokia' => 'Nokia Browser',

            'undef' => 'Other / Unknown / Not detected'
        );

        return $browsers;
    }

    public function  _detectBrowser()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];

        if ( stripos($agent, 'webtv') !== false ) {
            return 'webtv';
        }
        elseif ( stripos($agent, 'MSIE 6.') !== false ) {
            return 'msie6';
        }
        elseif ( stripos($agent, 'microsoft internet explorer') !== false or (stripos($agent, 'msie') !== false and stripos($agent, 'opera') === false)) {
            return 'msie';
        }
        elseif ( stripos($agent, 'mspie') !== false || stripos($agent, 'pocket') !== false ) {
            return 'msiemobile';
        }
        elseif ( stripos($agent, 'opera mini') !== false ) {
            return 'operamini';
        }
        elseif ( stripos($agent, 'opera') !== false ) {
            return 'opera';
        }
        elseif ( preg_match("/Firefox$/i", $agent, $matches) or (stripos($agent, 'safari') === false and preg_match("/Firefox[\/ \(]([^ ;\)]+)/i", $agent, $matches)) ) {
            return 'firefox';
        }
        elseif ( stripos($agent, 'Chrome') !== false ) {
            return 'chrome';
        }
        elseif ( stripos($agent, 'omniweb') !== false ) {
            return 'omniweb';
        }
        elseif ( stripos($agent,'Android') !== false ) {
            return 'android';
        }
        elseif ( stripos($agent,'iPad') !== false ) {
            return 'ipad';
        }
        elseif ( stripos($agent,'iPod') !== false ) {
            return 'ipod';
        }
        elseif ( stripos($agent,'iPhone') !== false ) {
            return 'iphone';
        }
        elseif ( stripos($agent,'blackberry') !== false ) {
            return 'blackberry';
        }
        elseif ( preg_match("/Nokia([^\/]+)\/([^ SP]+)/i",$agent,$matches) ) {
            if( stripos($agent,'Series60') !== false || strpos($agent,'S60') !== false ) {
                return 'nokias60';
            }
            else{
                return 'nokia';
            }
        }
        elseif ( stripos($agent,'Safari') !== false && stripos($agent,'iPhone') === false && stripos($agent,'iPod') === false ) {
            return 'safari';
        }
        elseif ( stripos($agent,'Firebird') !== false ) {
            return 'firebird';
        }
        elseif ( stripos($agent,'Konqueror') !== false  ) {
            return 'konqueror';
        }
        elseif ( stripos($agent,'icab') !== false  ) {
            return 'icab';
        }
        elseif ( stripos($agent,'amaya') !== false  ) {
            return 'amaya';
        }
        elseif ( stripos($agent,'lynx') !== false  ) {
            return 'lynx';
        }
        elseif ( stripos($agent,'Mozilla') !== false && preg_match('/Shiretoko\/([^ ]*)/i',$agent,$matches) ) {
            return 'shiretoko';
        }
        elseif ( stripos($agent,'Mozilla') !== false && preg_match('/IceCat\/([^ ]*)/i',$agent,$matches) ) {
            return 'icecat';
        }
        elseif ( stripos($agent,'mozilla') !== false ) {
            return 'mozilla';
        }
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');

        $list = $this->_getBrowserList();
        $rule['item_name'] = $list[$_rule->value];

        if($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}

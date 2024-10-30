<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerOs extends MiwoWidgetsController
{

    public $type    = 'common';
    public $module  = 'os';
    public $icon    = 'welcome-view-site';
    public $order   = 51;

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
        $this->data['status'] = '0';

        $oses = $this->_getOsList();
        $rules = $this->model->getRules($this->data, 'value');

        $this->data['oses'] = $this->prepareItems($oses, $rules);

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
        $os_id = $this->_detectOS();

        if (empty($os_id)) {
            return array();
        }

        $this->data = array();
        $this->data['module'] = $this->module;
        $this->data['value'] = $os_id;
        $this->data['status'] = '0';

        $excluded_widgets = $this->model->getRules($this->data);

        return $excluded_widgets;
    }

    public function prepareItems($_items, $rules)
    {
        $items = array();

        foreach ($_items as $id => $title) {
            $item        = new stdClass();
            $item->id    = $id;
            $item->title = $title;

            if (isset($rules[$id])){
                $item->checkstate = 'unchecked';
                $item->checkstatenext = 'checked';
            } else {
                $item->checkstate = 'checked';
                $item->checkstatenext = 'unchecked';
            }

            $items[] = $item;
        }

        return $items;
    }

    public function _getOsList()
    {
        $os = array(
            'windows8'     =>  'Windows 8',
            'windows7'     =>  'Windows 7',
            'windowsV'     =>  'Windows Vista',
            'windowsS'     =>  'Windows Server 2003/XP x64',
            'windowsXP'    =>  'Windows XP',
            'macintoshX'   =>  'Mac OS X',
            'macintosh9'   =>  'Mac OS 9',
            'linux'        =>  'Linux',
            'ubuntu'       =>  'Ubuntu',
            'openBSD'      =>  'openBSD',
            'sunOS'        =>  'sunOS',
            'qnx'          =>  'QNX',
            'beOS'         =>  'BeOS',
            'os2'          =>  'OS2',
            'iphone'       =>  'iPhone',
            'ipad'         =>  'iPad',
            'android'      =>  'Android',
            'blackberry'   =>  'BlackBerry',
            'webos'        =>  'Mobile',
            'undef'        =>  'Unknown OS Platform'
        );

        return $os;
    }

    public static function _detectOS()
    {
        $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
        $os_platform    =   "undef";

        $os_array       =   array(
             '/windows nt 6.2/i'     =>  'windows8',
             '/windows nt 6.1/i'     =>  'windows7',
             '/windows nt 6.0/i'     =>  'windowsV',
             '/windows nt 5.2/i'     =>  'windowsS',
             '/windows nt 5.1/i'     =>  'windowsXP',
             '/windows xp/i'         =>  'windowsXP',
             '/macintosh|mac os x/i' =>  'macintoshX',
             '/mac_powerpc/i'        =>  'macintosh9',
             '/linux/i'              =>  'linux',
             '/ubuntu/i'             =>  'ubuntu',
             '/iphone/i'             =>  'iphone',
             '/ipad/i'               =>  'ipad',
             '/android/i'            =>  'android',
             '/blackberry/i'         =>  'blackberry',
             '/webos/i'              =>  'webos'
         );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
                break;
            }

        }

        return $os_platform;
    }
    
    public static function detectOS()
    {
        $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
        $os_platform    =   "undef";

        $os_array       =   array(
             '/windows nt 6.2/i'     =>  'windows8',
             '/windows nt 6.1/i'     =>  'windows7',
             '/windows nt 6.0/i'     =>  'windowsV',
             '/windows nt 5.2/i'     =>  'windowsS',
             '/windows nt 5.1/i'     =>  'windowsXP',
             '/windows xp/i'         =>  'windowsXP',

             '/openbsd/i'            =>  'openBSD',
             '/sunos/i'              =>  'sunOS',
             '/linux/i'              =>  'linux',
             '/ubuntu/i'             =>  'ubuntu',

             '/macintosh|mac os x/i' =>  'macintoshX',
             '/mac_powerpc/i'        =>  'macintosh9',

             '/QNX/i'                => 'qnx',
             '/beos/i'               => 'beOS',
             '/os/2/i'               => 'os2',

             '/iphone/i'             =>  'iphone',
             '/ipad/i'               =>  'ipad',
             '/android/i'            =>  'android',
             '/blackberry/i'         =>  'blackberry',
             '/webos/i'              =>  'webos'
         );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
                break;
            }

        }

        return $os_platform;
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');

        $list = $this->_getOsList();
        $rule['item_name'] = $list[$_rule->value];

        if ($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}

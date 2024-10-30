<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerIp extends MiwoWidgetsController
{

    public $type    = 'common';
    public $module  = 'ip';
    public $icon    = 'networking';
    public $order   = 30;

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['text_ip_list'] = $this->language->get('text_ip_list');
        $this->data['text_show_widget'] = $this->language->get('text_show_widget');
        $this->data['text_in'] = $this->language->get('text_in');
        $this->data['text_except'] = $this->language->get('text_except');
        $this->data['text_note'] = $this->language->get('text_note');

        $this->data['button_text_save'] = $this->language->get('button_text_save');
        $this->data['button_text_reset'] = $this->language->get('button_text_reset');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');

        $this->data['ips'] = '';
        $this->data['ip_yes'] = '';
        $this->data['ip_no'] = '';

        $rule = $this->model->getRules($this->data);
        if (isset($rule[$this->data['widget_id']])){
            $this->data['ips'] = $rule[$this->data['widget_id']]->value;

            if (isset($rule[$this->data['widget_id']]) && $rule[$this->data['widget_id']]->status == 0) {
                $this->data['ip_yes'] = '';
                $this->data['ip_no'] = 'checked';
            }
            elseif (isset($rule[$this->data['widget_id']])) {
                $this->data['ip_yes'] = 'checked';
                $this->data['ip_no'] = '';
            }
        }

        $this->tpl = 'module/'.$this->module.'.tpl';
        $this->render();
    }

    public function saveRule($delete = false)
    {
        parent::deleteRule();

        $result = parent::saveRule();

        echo json_encode($result);
    }

    public function resetRule()
    {
        $result = parent::deleteRule();

        echo json_encode($result);
    }

    public function getExcludedWidgets()
    {
        $this->data = array();
        $this->data['module'] = $this->module;

        $rules = $this->model->getRules($this->data);

        $excluded_widgets = array();

        foreach ($rules as $key => $rule) {
            $ips = explode("\n",$rule->value);
            $check = $this->_checkInIps($ips);

            if ($rule->status == 0 && $check == true) {
                $excluded_widgets[$key] = $rule;
            }

            if ($rule->status == 1 && $check == false) {
                $excluded_widgets[$key] = $rule;
            }
        }

        return $excluded_widgets;
    }

    public function _checkInIps($ips)
    {
        $ip = MiwoWidgetsFactory::getClientIP();

        $check = array_search($ip, $ips);

        if ($check === false) {
            return false;
        } else {
            return true;
        }
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');

        $rule['item_name'] = str_replace("\n", "<br/>", $_rule->value);

        if ($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}

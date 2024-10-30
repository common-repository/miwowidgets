<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerRole extends MiwoWidgetsController
{

    public $type    = 'common';
    public $module  = 'role';
    public $icon    = 'groups';
    public $order   = 11;

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
        $this->data['status'] = '0';

        $roles = $this->_getRoleList();

        $rules = $this->model->getRules($this->data, 'value');
        $this->data['roles'] = $this->prepareItems($roles, $rules);

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
        $role_id = $this->_detectUserRole();

        if (empty($role_id)) {
            return array();
        }

        $this->data = array();
        $this->data['module'] = $this->module;
        $this->data['value'] = $role_id;
        $this->data['status'] = '0';

        $excluded_widgets = $this->model->getRules($this->data);

        return $excluded_widgets;
    }

    public function prepareItems($_items, $rules) 
    {
        $items = array();

        foreach ($_items as $id => $role) {
            $item        = new stdClass();
            $item->id    = $id;
            $item->title = $role['name'];

            if (isset($rules[$id])){
                $item->checkstate = 'unchecked';
                $item->checkstatenext = 'checked';
            } else{
                $item->checkstate = 'checked';
                $item->checkstatenext = 'unchecked';
            }

            $items[] = $item;
        }

        return $items;
    }

    public function _getRoleList()
    {
        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        if (empty($wp_roles->roles)){
            return array();
        }

        $roles = $wp_roles->roles;
        $roles['guest']['name'] = $this->language->get('text_guest');

        return $roles;
    }

    public function _detectUserRole()
    {
        if (!is_user_logged_in()) {
            return 'guest';
        }

        global $current_user;

        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);

        return $user_role;
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');

        if ($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        if (empty($wp_roles->roles) || !isset($wp_roles->role_names[$_rule->value])) {
            $rule['item_name'] = $_rule->value;
            return $rule;
        }

        $rule['item_name'] = $wp_roles->role_names[$_rule->value];

        return $rule;
    }
}

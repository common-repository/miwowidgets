<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerCustompost extends MiwoWidgetsController
{

    public $type    = 'single';
    public $module  = 'custompost';
    public $icon    = 'welcome-write-blog';
    public $order   = 3;

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');

        $args = array();
        $args['public'] = true;
        $args['_builtin'] = false;

        $the_query = get_post_types($args, 'objects', 'and');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
        $this->data['status'] = '0';

        $rules = $this->model->getRules($this->data, 'value');
        $this->data['customposttypes'] = $this->prepareItems($the_query, $rules);

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
        $query_object = $this->wp_query->get_queried_object();
        $custompost_id = $query_object->post_type;

        if (empty($custompost_id)) {
            return array();
        }

        $this->data = array();
        $this->data['module'] = $this->module;
        $this->data['value'] = $custompost_id;
        $this->data['status'] = '0';

        $excluded_widgets = $this->model->getRules($this->data);

        return $excluded_widgets;
    }

    public function prepareItems($_items, $rules)
    {
        $items = array();

        $filter_select = MiwoWidgetsRequest::getString('filter_select', 'all');

        foreach ($_items as $item) {
            if (isset($rules[$item->name])){
                $item->checkstate = 'unchecked';
                $item->checkstatenext = 'checked';

                if ($filter_select == 'all_selected') {
                    $item = false;
                }
            }
            else {
                $item->checkstate = 'checked';
                $item->checkstatenext = 'unchecked';

                if ($filter_select == 'all_unselected') {
                    $item = false;
                }
            }

            if (!empty($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function getOrderArgs($order, $args)
    {
        switch ($order) {
            case 'z_a':
                $args['orderby'] = 'name';
                $args['order'] = 'DESC';
                break;
            case 'a_z':
            default:
                $args['orderby'] = 'name';
                $args['order'] = 'ASC';
                break;
        }

        return $args;
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');
        $rule['item_name'] = $_rule->value;

        if ($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}

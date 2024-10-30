<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerLanguage extends MiwoWidgetsController
{

    public $type    = 'common';
    public $module  = 'language';
    public $icon    = 'translation';
    public $order   = 52;

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
        $this->data['status'] = '0';

        $active_plugin = MiwoWidgetsFactory::getActiveLanguagePlugin();

        $this->data['active_language_plugin'] = $this->language->get($active_plugin);
        $this->data['error_not_installed_language_plugin'] = $this->language->get('error_not_installed_language_plugin');
        $this->data['text_active_multilanguage_plugin'] = $this->language->get('text_active_multilanguage_plugin');

        $languages = $this->_getLanguageList($active_plugin);

        $rules = $this->model->getRules($this->data, 'value');
        $this->data['languages'] = $this->prepareItems($languages, $rules);

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
        $language_id = $this->_detectLanguage();

        if (empty($language_id)){
            return array();
        }

        $this->data = array();
        $this->data['module'] = $this->module;
        $this->data['value'] = $language_id;
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

            if (isset($rules[$id])) {
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

    public function _getLanguageList($active_plugin)
    {
        $languages = array();

        switch($active_plugin) {
            case 'wpml' :
                $wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;
                require_once($wpml_api);

                $wpml_langs = wpml_get_active_languages();

                foreach ($wpml_langs as $wpml_lang) {
                    $languages[$wpml_lang['code']] =  $wpml_lang['display_name'];
                }
                break;
            case 'qt' :
                global $q_config;
                $qt_langs = $q_config['enabled_languages'];

                foreach ($qt_langs as $code) {
                    $languages[$code] = $q_config['language_name'][$code];
                }

                break;
        }

        return $languages;
    }

    public function _detectLanguage()
    {
        $active_plugin = MiwoWidgetsFactory::getActiveLanguagePlugin();
        $code='';

        switch($active_plugin) {
            case 'wpml' :
                $wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;
                require_once($wpml_api);

                $code = wpml_get_current_language();
                break;
            case 'qt' :
                $code = qtrans_getLanguage();
                break;
        }

        return $code;
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');
        $rule['item_name'] = '';
        $rule['status'] = '';

        return $rule;
    }
}

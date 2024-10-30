<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerSetting extends MiwoWidgetsController
{

    public function __construct()
    {
        parent::__construct();

        $this->language->load('setting/setting');
    }

    public function index()
    {
        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['text_enable'] = $this->language->get('text_enable');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_item_limit'] = $this->language->get('text_item_limit');
        $this->data['text_info'] = $this->language->get('text_info');
        $this->data['text_info_content'] = $this->language->get('text_info_content');

        $this->data['btn_save_changes'] = $this->language->get('btn_save_changes');

        $settings = get_option('miwowidgets');

        $this->data['count_select_options'] = $this->getCountSelectOptions();
        $this->data['settings_item_count'] = MiwoWidgetsFactory::getOption('miwowidgets', 10, 'item_count');
        $this->data['settings_enable'] = MiwoWidgetsFactory::getOption('miwowidgets', 1, 'enable');

        $loader = MiwoWidgetsLoader::getInstance();
        $modules = $loader->loadAllModules();

        $modules = $modules['common'];
        usort($modules, array($this, '_sort'));

        foreach ($modules as $module) {
            $html = $module->getSettingHtml($settings, $module->module);

            if (!empty($html)) {
                $this->data['module_setting_html'][] = $html;
            }
        }

        $this->tpl = 'setting/setting.tpl';
        $this->render();
    }

    public function save()
    {
        $settings = MiwoWidgetsRequest::getVar('miwowidget_settings', array(), 'post', 'array');

        $_result = update_option('miwowidgets', $settings);

        if ($_result == true) {
            $result['success'] = $this->language->get('text_success');
        } else {
            $result['error'] =  $this->language->get('text_error_save');
        }

        echo json_encode($result);
    }

    private function _sort($a, $b)
    {
        return ($a->order < $b->order) ? -1 : 1;
    }
}

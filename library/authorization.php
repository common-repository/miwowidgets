<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsAuthorization
{

    protected $iamhere;
    protected $loader;

    public function __construct()
    {
        $this->loader = MiwoWidgetsLoader::getInstance();
    }

    public function authorize($sidebars)
    {
        $is_enable = MiwoWidgetsFactory::getOption('miwowidgets', 1, 'enable');

        if ($is_enable == 0) {
            return $sidebars;
        }

        $this->iamhere = MiwoWidgetsFactory::detectPage();
        $ext_widgets_single = $ext_widgets_or = array();

        $modules = $this->loader->loadAllModules();

        if (empty($modules)) {
            return $sidebars;
        }

        if ($this->iamhere != 'undef' and isset($modules['single']) and isset($modules['single'][$this->iamhere])) {
            $ext_widgets_single = $this->loader->run('getExcludedWidgets', 'module/'.$this->iamhere);
        }

        if (isset($modules['common'])) {
            $rules = MiwoWidgetsFactory::getRuleTypes();

            $settings = get_option('miwowidgets');

            foreach($modules['common'] as $module) {
                if (isset($rules[$module->module])) {
                    $_ext_widgets = $this->loader->run('getExcludedWidgets', 'module/'.$module->module);

                    if (isset($settings[$module->module.'_mode']) and $settings[$module->module.'_mode'] == 1) {//and
                        if (!isset($ext_widgets_and)){
                            $ext_widgets_and = $_ext_widgets;
                        } else{
                            $ext_widgets_and = array_intersect_key($ext_widgets_and, $_ext_widgets);
                        }
                    } else { //or
                        $ext_widgets_or = array_merge($ext_widgets_or, $_ext_widgets);
                    }
                }
            }
        }

        if (!isset($ext_widgets_and)) {
            $ext_widgets_and = array();
        }

        $ext_widgets_common = array_merge($ext_widgets_and, $ext_widgets_or);

        $ext_widgets = array_merge($ext_widgets_single, $ext_widgets_common);

        $sidebars = $this->prepareWidgets($sidebars, $ext_widgets);

        return $sidebars;
    }

    protected function prepareWidgets($sidebars, $excluded_widgets)
    {
        foreach ($excluded_widgets as $excluded_widget_id => $excluded_widget){
            foreach ($sidebars as $sidebar_id => $sidebar) {
                if (empty($sidebar)){
                    continue;
                }

                $index = array_search($excluded_widget_id, $sidebar);
                if ($index !== false){
                    unset($sidebars[$sidebar_id][$index]);
                }
            }
        }

        return $sidebars;
    }
}

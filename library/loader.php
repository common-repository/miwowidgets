<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsLoader
{

    public static $instances = array();

    public static function getInstance($class = 'MiwoWidgetsLoader', $path = '')
    {
        if (isset(self::$instances[$class])) {
            return self::$instances[$class];
        }

        $check_path = file_exists($path);
        $check_class = class_exists($class);

        if (!$check_path and !$check_class) {
            return false;
        }

        if (!$check_class) {
            require_once($path);
        }

        self::$instances[$class] = new $class();

        return self::$instances[$class];
    }


    public function controller($route)
    {
        $controller_path = MIWOWIDGETS_CONTROLLER_PATH . $route . '.php';

        $route_array = explode('/', $route);
        $controller_name = end($route_array);

        $class = 'MiwoWidgetsController'.$controller_name;

        $controller = self::getInstance($class, $controller_path);

        return $controller;
    }

    public function model($route)
    {
        $model_path = MIWOWIDGETS_MODEL_PATH . $route . '.php';

        $route_array = explode('/', $route);
        $model_name = end($route_array);

        $class = 'MiwoWidgetsModel'.$model_name;

        $model = self::getInstance($class, $model_path);

        return $model;
    }

    public function run($task = '', $route = '', $args = array())
    {
        if (empty($task)){
            $task = MiwoWidgetsRequest::getString('task', '');
        }

        if (empty($route)){
            $route = MiwoWidgetsRequest::getString('route', '');
        }

        $controller = $this->controller($route);

        if (empty($controller)) {
            exit;
        }

        if (!empty($task)){
            return $controller->{$task}($args);
        } else{
            return $controller->index();
        }
    }

    public function loadAllModules()
    {
        $modules = array();

        $files = glob(MIWOWIDGETS_CONTROLLER_PATH . 'module/*.php');

        if (empty($files)) {
            return $modules;
        }

        foreach ($files as $file) {
            $path = str_replace('.php', '', $file);
            $path_array = explode('/', $path);
            $name = end($path_array);

            $class = 'MiwoWidgetsController'.ucfirst($name);

            $module = self::getInstance($class, $file);
            $modules[$module->type][$module->module] = $module;
        }

        return $modules;
    }
}

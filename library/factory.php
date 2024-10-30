<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsFactory
{

    public static function getOption($name, $default = false, $id = null)
    {
        $data = null;
        $opt = get_option($name);

        if (is_string($opt)) {
            $data = $opt;
        } elseif (is_array($opt)) {
            if (!empty($id) and isset($opt[$id])) {
                return $opt[$id];
            }

            $found = false;

            foreach ($opt as $o) {
                if (!is_array($o)) {
                    continue;
                }

                $data = $o;
                $found = true;
                
                break;
            }

            if ($found == false) {
                $data = $opt;
            }
        }

        if (empty($data)) {
            return $default;
        }

        return $data;
    }

    public static function detectPage()
    {
        if ( is_front_page() && get_option('show_on_front') == 'posts' ) {
            $result = 'page';
        } elseif ( is_home() && get_option('show_on_front') == 'page' ) {
            $result = 'page';
        } elseif (is_attachment()) {
            $result = 'page';					// must be before is_single(), otherwise detects as 'single'
        } elseif (is_single()) {
            $post = $GLOBALS['post'];
            $post_type = get_post_type($post);
            
            switch ($post_type) {
                case 'post':
                    $result = 'post';
                    break;
                case 'product':
                    $result = 'woocommerce';
                    break;
                case 'product':
                    $result = 'woocommerce';
                    break;
                case 'forum':
                case 'topic':
                    $result = 'bbpress';
                    break;
                case 'page':
                    if (self::isBuddyPressGroup()) {
                        $result = 'buddypress';
                    } elseif (self::isBuddyPress()) {
                        $result = 'page';
                    }
                    break;
                default:
                    $result = 'custompost';
                    break;
            }
        } elseif (is_page()) {
            if (self::isMiwoShop()) {
                $result = 'miwoshop';
            } elseif (self::isBuddyPressGroup()) {
                $result = 'buddypress';
            } else {
                $result = 'page';
            }
        } elseif (is_author()) {
            $result = 'author';
        } elseif ( is_category() ) {
            $result = 'category';
        } elseif (is_tag()) {
            $result = 'page';
        }  elseif(self::isWooCategory()){
            return 'woocommerce';
        } elseif (function_exists('is_post_type_archive') && is_post_type_archive()) {
            $result = 'cp_archive';				// must be before is_archive(), otherwise detects as 'archive' in WP 3.1.0
        } elseif (function_exists('is_tax') && is_tax()) {
            $result = 'tax_archive';
        } elseif (is_archive() && ! is_category() && !is_author() && !is_tag()) {
            $result = 'page';
        } elseif (function_exists('bbp_is_single_user') && (bbp_is_single_user() || bbp_is_single_user_edit())) {	// must be before is_404(), otherwise bbPress profile page is detected as 'e404'.
            $result = 'bbp_profile';
        } elseif (is_404()) { # todo:: add the orther errors
            $result = 'page';
        } elseif (is_search()) {
            $result = 'page';
        } elseif (function_exists('is_pod_page') && is_pod_page()) {
            $result = 'pods';
        } else {
            $result = 'undef';
        }

        return $result;
    }

    public static function getRuleTypes()
    {
        $db = $GLOBALS['wpdb'];

        $query = "SELECT DISTINCT(`mod`) FROM " . $db->prefix . "miwowidgets";
        $result =  $db->get_results($query, OBJECT_K);

        return $result;
    }

    public static function getClientIP()
    {
   		$ip = self::_getClientIP();

   		if ((strstr($ip, ',') !== false) || (strstr($ip, ' ') !== false)) {
   			$ip	 = str_replace(' ', ',', $ip);
   			$ip	 = str_replace(',,', ',', $ip);
   			$ips = explode(',', $ip);
   			$ip	 = '';
   			while (empty($ip) && !empty($ips)) {
   				$ip	 = array_pop($ips);
   				$ip	 = trim($ip);
   			}
   		} else {
   			$ip = trim($ip);
   		}

   		return $ip;
   	}

    public static function getGeo()
    {
        $geo =  unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='. self::getClientIP()));
        //$geo =  unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=46.196.232.98')); // localhost test

        return $geo;
    }

   	private static function _getClientIP()
    {
   		// Normally the $_SERVER superglobal is set
   		if (isset($_SERVER))
   		{
   			// Do we have an x-forwarded-for HTTP header (e.g. NginX)?
   			if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
   			{
   				return $_SERVER['HTTP_X_FORWARDED_FOR'];
   			}

   			// Do we have a client-ip header (e.g. non-transparent proxy)?
   			if (array_key_exists('HTTP_CLIENT_IP', $_SERVER))
   			{
   				return $_SERVER['HTTP_CLIENT_IP'];
   			}

   			// Normal, non-proxied server or server behind a transparent proxy
   			return $_SERVER['REMOTE_ADDR'];
   		}

   		// This part is executed on PHP running as CGI, or on SAPIs which do
   		// not set the $_SERVER superglobal
   		// If getenv() is disabled, you're screwed
   		if (!function_exists('getenv'))
   		{
   			return '';
   		}

   		// Do we have an x-forwarded-for HTTP header?
   		if (getenv('HTTP_X_FORWARDED_FOR'))
   		{
   			return getenv('HTTP_X_FORWARDED_FOR');
   		}

   		// Do we have a client-ip header?
   		if (getenv('HTTP_CLIENT_IP'))
   		{
   			return getenv('HTTP_CLIENT_IP');
   		}

   		// Normal, non-proxied server or server behind a transparent proxy
   		if (getenv('REMOTE_ADDR'))
   		{
   			return getenv('REMOTE_ADDR');
   		}

   		// Catch-all case for broken servers, apparently
   		return '';
   	}

    public static function getCurrentUrl()
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }

        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }

        return $pageURL;
    }

    public static function getActiveLanguagePlugin()
    {
        if (defined('ICL_PLUGIN_PATH') && file_exists(ICL_PLUGIN_PATH . DW_WPML_API)) {
            return 'wpml';
        }

        if (defined('QTRANS_INIT')) {
            return 'qt';
        }

        return false;
    }

	public static function getRemoteVersion($plugin)
    {
		$version = '?.?.?';

        $components = self::_getRemoteData('http://miwisoft.com/index.php?option=com_mijoextensions&view=xml&format=xml&catid=5');

        if (!strstr($components, '<?xml version="1.0" encoding="UTF-8" ?>')) {
            return $version;
        }

        $manifest = simplexml_load_string($components, 'SimpleXMLElement');

        if (is_null($manifest)) {
            return $version;
        }

        $category = $manifest->category;
        if (!($category instanceof SimpleXMLElement) || (count($category->children()) == 0)) {
            return $version;
        }

        foreach ($category->children() as $component) {
            $option = (string)$component->attributes()->option;
            $compability = (string)$component->attributes()->compability;

            if (($option == $plugin) and $compability == 'wpall') {
                $version = trim((string)$component->attributes()->version);
                break;
            }
        }

        return $version;
    }

    public static function _getRemoteData($url)
    {
        $user_agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)";
        $data = false;

        // cURL
        if (extension_loaded('curl')) {
            $process = @curl_init($url);

            @curl_setopt($process, CURLOPT_HEADER, false);
            @curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
            @curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
            @curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            @curl_setopt($process, CURLOPT_AUTOREFERER, true);
            @curl_setopt($process, CURLOPT_FAILONERROR, true);
            @curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
            @curl_setopt($process, CURLOPT_TIMEOUT, 10);
            @curl_setopt($process, CURLOPT_CONNECTTIMEOUT, 10);
            @curl_setopt($process, CURLOPT_MAXREDIRS, 20);

            $data = @curl_exec($process);

            @curl_close($process);

            return $data;
        }

        // fsockopen
        if (function_exists('fsockopen')) {
            $errno = 0;
            $errstr = '';

            $url_info = parse_url($url);
            if($url_info['host'] == 'localhost')  {
                $url_info['host'] = '127.0.0.1';
            }

            // Open socket connection
            if ($url_info['scheme'] == 'http') {
                $fsock = @fsockopen($url_info['scheme'].'://'.$url_info['host'], 80, $errno, $errstr, 5);
            } else {
                $fsock = @fsockopen('ssl://'.$url_info['host'], 443, $errno, $errstr, 5);
            }

            if ($fsock) {
                @fputs($fsock, 'GET '.$url_info['path'].(!empty($url_info['query']) ? '?'.$url_info['query'] : '').' HTTP/1.1'."\r\n");
                @fputs($fsock, 'HOST: '.$url_info['host']."\r\n");
                @fputs($fsock, "User-Agent: ".$user_agent."\n");
                @fputs($fsock, 'Connection: close'."\r\n\r\n");

                // Set timeout
                @stream_set_blocking($fsock, 1);
                @stream_set_timeout($fsock, 5);

                $data = '';
                $passed_header = false;
                while (!@feof($fsock)) {
                    if ($passed_header) {
                        $data .= @fread($fsock, 1024);
                    } else {
                        if (@fgets($fsock, 1024) == "\r\n") {
                            $passed_header = true;
                        }
                    }
                }

                // Clean up
                @fclose($fsock);

                // Return data
                return $data;
            }
        }

        // fopen
        if (function_exists('fopen') && ini_get('allow_url_fopen')) {
            // Set timeout
            if (ini_get('default_socket_timeout') < 5) {
                ini_set('default_socket_timeout', 5);
            }

            $url = str_replace('://localhost', '://127.0.0.1', $url);

            $handle = @fopen($url, 'r');

            @stream_set_blocking($handle, 1);
            @stream_set_timeout($handle, 5);
            @ini_set('user_agent',$user_agent);

            if ($handle) {
                $data = '';
                while (!feof($handle)) {
                    $data .= @fread($handle, 8192);
                }

                // Clean up
                @fclose($handle);

                // Return data
                return $data;
            }
        }

        // file_get_contents
        if (function_exists('file_get_contents') && ini_get('allow_url_fopen')) {
            $url = str_replace('://localhost', '://127.0.0.1', $url);
            @ini_set('user_agent',$user_agent);
            $data = @file_get_contents($url);

            // Return data
            return $data;
        }

        return $data;
    }

    public static function isWooProduct()
    {
        $term = MiwoWidgetsRequest::getString('product', '');

        if (empty($term)) {
            return false;
        }

        $args = array(
          'name' => $term,
          'post_type' => 'product',
          'post_status' => 'publish',
          'numberposts' => 1
        );

        $product = get_posts($args);

        if (empty($product)) {
            return false;
        } else {
            return true;
        }
   	}

    public static function isWooCategory( $term = '' )
    {
        $term = MiwoWidgetsRequest::getString('product_cat', '');

        if (empty($term)) {
            return false;
        }

        $terms = get_terms(array('product_cat'), $term);

        if (isset($terms->errors)) {
            return false;
        } else {
            return true;
        }
    }

    public static function isMiwoShop()
    {
        $miwoshop = array('product/category','product/manufacturer/info','product/product');

        if (isset($_REQUEST['option']) and isset($_REQUEST['route']) and $_REQUEST['option'] == 'com_miwoshop' and in_array($_REQUEST['route'], $miwoshop)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isBuddyPressGroup()
    {
        if (function_exists('bp_is_group')) {
            return bp_is_group();
        }

        if (!defined('BP_VERSION')) {
            return false;
        }

        $bp = &$GLOBALS['bp'];

        $components = self::getBPcomponents();
        $bp_components = array_keys($components);

        if (!empty($bp->current_component) && in_array($bp->current_component, $bp_components)) {
            if ($bp->current_component == 'groups' && !empty($bp->current_item)) {
                return true;
            }
        }

        return false;
    }

    private static function getBPcomponents()
    {
        $bp = &$GLOBALS['bp'];
        $components = array();

        foreach ($bp->active_components as $key => $value) {
            if (version_compare(BP_VERSION, '1.5', '<')) {
                $c = &$value;
            } else {
                $c = &$key;
            }

            if ($c == 'groups') {
                $components[$c] = ucfirst($c) . ' (only main page)';
            } else {
                $components[$c] = ucfirst($c);
            }
        }

        asort($components);
        return $components;
    }

    private static function isBuddyPress()
    {
        if (function_exists('is_buddypress')) {
            return is_buddypress();
        }

        return false;
    }
}
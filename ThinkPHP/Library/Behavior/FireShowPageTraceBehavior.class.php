<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614 <weibo.com/luofei614>
// +----------------------------------------------------------------------
// $Id$

/**
 * 将Trace信息输出到火狐的firebug，从而不影响ajax效果和页面的布局。
 * 使用前，你需要先在火狐浏览器上安装firebug和firePHP两个插件。
 * 定义应用的tags.php文件，
 * <code>
 * <?php return array(
 *   'app_end'=>array(
 *       'FireShowPageTrace'
 *   )
 * );
 * </code>
 * 再将此文件放到应用的Behavior文件夹中即可
 * 如果trace信息没有正常输出，请查看您的日志。
 * firePHP，是通过http headers和firebug通讯的，所以要保证在输出trace信息之前不能有
 * headers输出，你可以在入口文件第一行加入代码 ob_start(); 或者配置output_buffering
 *
 */
namespace Behavior;

/**
 * 系统行为扩展 页面Trace显示输出
 */
use Behavior\FirePHP as FirePHP;
use Think\Exception as Exception;
class FireShowPageTraceBehavior
{
    protected $tracePagTabs = array('BASE' => '基本', 'FILE' => '文件', 'INFO' => '流程', 'ERR|NOTIC' => '错误', 'SQL' => 'SQL', 'DEBUG' => '调试');

    // 行为扩展的执行入口必须是run
    public function run(&$params)
    {
        if (C('FIRE_SHOW_PAGE_TRACE', null, true)) {
            $this->showTrace();
        }

    }

    /**
     * 显示页面Trace信息
     * @access private
     */
    private function showTrace()
    {
        // 系统默认显示信息
        $files = get_included_files();
        $info  = array();
        foreach ($files as $key => $file) {
            $info[] = $file . ' ( ' . number_format(filesize($file) / 1024, 2) . ' KB )';
        }
        $trace = array();
        $base  = array(
            '请求信息' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $_SERVER['SERVER_PROTOCOL'] . ' ' . $_SERVER['REQUEST_METHOD'] . ' : ' . __SELF__,
            '运行时间' => $this->showTime(),
            '内存开销' => MEMORY_LIMIT_ON ? number_format((memory_get_usage() - $GLOBALS['_startUseMems']) / 1024, 2) . ' kb' : '不支持',
            '查询信息' => N('db_query') . ' queries ' . N('db_write') . ' writes ',
            '文件加载' => count(get_included_files()),
            '缓存信息' => N('cache_read') . ' gets ' . N('cache_write') . ' writes ',
            '配置加载' => count(c()),
            '会话信息' => 'SESSION_ID=' . session_id(),
        );
        // 读取应用定义的Trace文件
        $traceFile = CONF_PATH . 'trace.php';
        if (is_file($traceFile)) {
            $base = array_merge($base, include $traceFile);
        }
        $debug = trace();
        $tabs  = C('TRACE_PAGE_TABS', null, $this->tracePagTabs);
        foreach ($tabs as $name => $title) {
            switch (strtoupper($name)) {
                case 'BASE': // 基本信息
                    $trace[$title] = $base;
                    break;
                case 'FILE': // 文件信息
                    $trace[$title] = $info;
                    break;
                default: // 调试信息
                    if (strpos($name, '|')) {
// 多组信息
                        $array  = explode('|', $name);
                        $result = array();
                        foreach ($array as $name) {
                            $result += isset($debug[$name]) ? $debug[$name] : array();
                        }
                        $trace[$title] = $result;
                    } else {
                        $trace[$title] = isset($debug[$name]) ? $debug[$name] : '';
                    }
            }
        }
        foreach ($trace as $key => $val) {
            if (!is_array($val) && empty($val)) {
                $val = array();
            }

            if (is_array($val)) {
                $fire = array(
                    array('', ''),
                );
                foreach ($val as $k => $v) {
                    $fire[] = array($k, $v);
                }
                fb(array($key, $fire), FirePHP::TABLE);
            } else {
                fb($val, $key);
            }
        }
        unset($files, $info, $log, $base);
    }

    /**
     * 获取运行时间
     */
    private function showTime()
    {
        // 显示运行时间
        G('beginTime', $GLOBALS['_beginTime']);
        G('viewEndTime');
        // 显示详细运行时间
        return G('beginTime', 'viewEndTime') . 's ( Load:' . G('beginTime', 'loadTime') . 's Init:' . G('loadTime', 'initTime') . 's Exec:' . G('initTime', 'viewStartTime') . 's Template:' . G('viewStartTime', 'viewEndTime') . 's )';
    }

}

function fb()
{
    $instance = FirePHP::getInstance(true);

    $args = func_get_args();
    return call_user_func_array(array($instance, 'fb'), $args);
}

class FB
{
    /**
     * Enable and disable logging to Firebug
     *
     * @see FirePHP->setEnabled()
     * @param boolean $Enabled TRUE to enable, FALSE to disable
     * @return void
     */
    public static function setEnabled($Enabled)
    {
        $instance = FirePHP::getInstance(true);
        $instance->setEnabled($Enabled);
    }

    /**
     * Check if logging is enabled
     *
     * @see FirePHP->getEnabled()
     * @return boolean TRUE if enabled
     */
    public static function getEnabled()
    {
        $instance = FirePHP::getInstance(true);
        return $instance->getEnabled();
    }

    /**
     * Specify a filter to be used when encoding an object
     *
     * Filters are used to exclude object members.
     *
     * @see FirePHP->setObjectFilter()
     * @param string $Class The class name of the object
     * @param array $Filter An array or members to exclude
     * @return void
     */
    public static function setObjectFilter($Class, $Filter)
    {
        $instance = FirePHP::getInstance(true);
        $instance->setObjectFilter($Class, $Filter);
    }

    /**
     * Set some options for the library
     *
     * @see FirePHP->setOptions()
     * @param array $Options The options to be set
     * @return void
     */
    public static function setOptions($Options)
    {
        $instance = FirePHP::getInstance(true);
        $instance->setOptions($Options);
    }

    /**
     * Get options for the library
     *
     * @see FirePHP->getOptions()
     * @return array The options
     */
    public static function getOptions()
    {
        $instance = FirePHP::getInstance(true);
        return $instance->getOptions();
    }

    /**
     * Log object to firebug
     *
     * @see http://www.firephp.org/Wiki/Reference/Fb
     * @param mixed $Object
     * @return true
     * @throws Exception
     */
    public static function send()
    {
        $instance = FirePHP::getInstance(true);
        $args     = func_get_args();
        return call_user_func_array(array($instance, 'fb'), $args);
    }

    /**
     * Start a group for following messages
     *
     * Options:
     *   Collapsed: [true|false]
     *   Color:     [#RRGGBB|ColorName]
     *
     * @param string $Name
     * @param array $Options OPTIONAL Instructions on how to log the group
     * @return true
     */
    public static function group($Name, $Options = null)
    {
        $instance = FirePHP::getInstance(true);
        return $instance->group($Name, $Options);
    }

    /**
     * Ends a group you have started before
     *
     * @return true
     * @throws Exception
     */
    public static function groupEnd()
    {
        return self::send(null, null, FirePHP::GROUP_END);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::LOG
     * @param mixes $Object
     * @param string $Label
     * @return true
     * @throws Exception
     */
    public static function log($Object, $Label = null)
    {
        return self::send($Object, $Label, FirePHP::LOG);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::INFO
     * @param mixes $Object
     * @param string $Label
     * @return true
     * @throws Exception
     */
    public static function info($Object, $Label = null)
    {
        return self::send($Object, $Label, FirePHP::INFO);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::WARN
     * @param mixes $Object
     * @param string $Label
     * @return true
     * @throws Exception
     */
    public static function warn($Object, $Label = null)
    {
        return self::send($Object, $Label, FirePHP::WARN);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::ERROR
     * @param mixes $Object
     * @param string $Label
     * @return true
     * @throws Exception
     */
    public static function error($Object, $Label = null)
    {
        return self::send($Object, $Label, FirePHP::ERROR);
    }

    /**
     * Dumps key and variable to firebug server panel
     *
     * @see FirePHP::DUMP
     * @param string $Key
     * @param mixed $Variable
     * @return true
     * @throws Exception
     */
    public static function dump($Key, $Variable)
    {
        return self::send($Variable, $Key, FirePHP::DUMP);
    }

    /**
     * Log a trace in the firebug console
     *
     * @see FirePHP::TRACE
     * @param string $Label
     * @return true
     * @throws Exception
     */
    public static function trace($Label)
    {
        return self::send($Label, FirePHP::TRACE);
    }

    /**
     * Log a table in the firebug console
     *
     * @see FirePHP::TABLE
     * @param string $Label
     * @param string $Table
     * @return true
     * @throws Exception
     */
    public static function table($Label, $Table)
    {
        return self::send($Table, $Label, FirePHP::TABLE);
    }

}

if (!defined('E_STRICT')) {
    define('E_STRICT', 2048);
}
if (!defined('E_RECOVERABLE_ERROR')) {
    define('E_RECOVERABLE_ERROR', 4096);
}
if (!defined('E_DEPRECATED')) {
    define('E_DEPRECATED', 8192);
}
if (!defined('E_USER_DEPRECATED')) {
    define('E_USER_DEPRECATED', 16384);
}
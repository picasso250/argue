<?php

function _get($key, $default = '')
{
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}
function _post($key, $default = '')
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

function render_with_layout($layout_tpl, $inner_tpl_list, $data = [])
{
    $data['_inner_tpl_list'] = $inner_tpl_list;
    extract($data);
    include $layout_tpl;
}

function redirect($url) {
    header("location:$url");
}

// $routers: [method_and_regex => func_or_Controller@Action]
// when 404, return false
function regex_router($routers)
{
    // get url
    $uri = $_SERVER["REQUEST_URI"];
    $a = explode("?", $uri);
    $url = $a[0];
    // run
    foreach ($routers as $regex => $func) {
        $a = explode(' ', $regex);
        if (count($a) == 2) {
            list($method, $regex) = $a;
            $method_match = $_SERVER["REQUEST_METHOD"] == $method;
        } else {
            $method_match = true;
        }
        $regex = "#^$regex$#";
        if ($method_match && preg_match($regex, $url, $m)) {
            // call back
            if (is_callable($func)) {
                $func($m);
                return true;
            } elseif (is_string($func)) {
                $a = explode("@", $func);
                if (count($a) != 2) {
                    return false;
                }
                list($class_name, $func_name) = $a;
                if (!class_exists($class_name))
                    return false;
                $c = new $class_name();
                if (!method_exists($c, $func_name))
                    return false;
                $c->$func_name($m);
                return true;
            }
            return false;
        }
    }
    return false;
}

function find_or_404($table, $id) {
    $v=ORM::for_table($table)->find_one($id);
    if ($v) return $v;
    die("no_resourse");
}

function get_php_input() {
    return file_get_contents('php://input');
}

function sql_timestamp($time = null) {
    if ($time === null) $time = time();
    return date('Y-m-d H:i:s', $time);
}

// service
function _($name, $value = null) {
    static $lazy;
    static $pool;
    if ($value === null) {
        // get
        if (isset($pool[$name])) return $pool[$name];
        if (isset($lazy[$name])) {
            $func = $lazy[$name];
            return $pool[$name] = $func();
        }
        return null;
    } else {
        // set
        if (is_callable($value)) {
            $lazy[$name] = $value;
        } else {
            $pool[$name] = $value;
        }
    }
}

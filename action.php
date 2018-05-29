<?php

require "logic.php";

function action_index()
{
    $debugbar = $GLOBALS['debugbar'];
    $debugbar["messages"]->addMessage("hello world!");
    render_with_layout(ROOT_VIEW.'/layout.php', ['content'=>ROOT_VIEW.'/index.php']);
}

// 登录
function action_login() {
    $msgs = [];
    $name = '';
    $id_num = '';
    if (is_post()) {
        $name = _post('name');
        $id_num = _post('id_num');
        if (!$name) die("姓名不能为空");
        if (!$id_num) die("身份证号码不能为空");
        login($name,$id_num);
        $back = _get('back');
        if ($back) {
            if (strpos($back, '/login')===0) {
                // do nothing
            } else {
                redirect($back);
            }
        }
    } else {
        render_with_layout(ROOT_VIEW.'/layout.php', ['content'=>ROOT_VIEW.'/login.php'], compact('msgs', 'name', 'id_num'));
    }
}

function action_new_argue()
{
    if (!isset($_POST['title'])) die("no title");
    $argue = ORM::for_table('argue')->create();
    $argue->title = trim($_POST['title']);
    $argue->content = "{}";
    $argue->save();
    redirect("/a/$argue->id");
}

// 查看辩题
function action_argue($params)
{
    $id = $params[1];
    $argue = ORM::for_table('argue')->find_one($id);
    $argue_content = json_decode($argue['content'],true);
    if (!isset($argue_content['summary'])) $argue_content['summary'] = [null,null];
    argue_number_to_ratio($argue_content);
    $point_list= [['stand'=>0,'content'=>['content'=>'a'],'opposite'=>['content'=>'b']]];
    $point_list = argue_get_point_list(10);
    $data = compact('argue', 'argue_content', 'point_list', 'point_list_user');
    render_with_layout(ROOT_VIEW.'/layout.php', ['content'=>ROOT_VIEW.'/argue.php'], $data);
}
function action_install()
{
    echo"<pre>";
    $sqls = [
        // "drop table argue",
        "CREATE TABLE argue(id INTEGER PRIMARY KEY ASC, title, content)",
    ];
    foreach ($sqls as $sql) {
        if (ORM::raw_execute($sql)) {
            echo "$sql ok\n";
        } else {
            echo "$sql fail\n";
        }
    }
    
    $v = ORM::for_table('user')->raw_query("SELECT name FROM sqlite_master WHERE type='table'")->find_array();
    foreach ($v as $key => $value) {
        var_dump($value);
    }
    $v = ORM::for_table('user')->raw_query("PRAGMA table_info(argue) ")->find_array();
    foreach ($v as $key => $value) {
        var_dump($value);
    }
}
// 和ajax相关的动作
function action_ajax_do()
{
    if (!isset($_GET['action'])) die("no action");
    if (!isset($_GET['id'])) die("no id");

    $action = $_GET['action'];

    // login check
    $login_check_list = [
        'choose_side',
        'edit_summary',
        'argue_add_point',
        'argue_edit_point',
    ];
    if (in_array($action, $login_check_list)) {
        $cur_user = cur_user();
        if (!$cur_user) die("need_login");
        $GLOBALS['cur_user'] = $cur_user;
    }

    require "action_argue_ajax.php";

    $func = "_action_".$_GET['action'];
    if (!function_exists($func)) die("no func");
    return $func($_GET['id']);
}

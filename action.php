<?php

require "logic.php";

function action_index()
{
    $debugbar = $GLOBALS['debugbar'];
    $debugbar["messages"]->addMessage("hello world!");
    render_with_layout(ROOT_VIEW.'/layout.php', ['content'=>ROOT_VIEW.'/index.php']);
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
function action_argue($params)
{
    $id = $params[1];
    $argue = ORM::for_table('argue')->find_one($id);
    // $argue = ['id'=>1,'title' => 'test'];
    $argue_content = [
        'begin'=>[11,100],'end'=> [22,100], 
        'summary'=> ['中文','zz'],
        'point_list'=>[['stand'=>0,'content'=>'a','opposite'=>'b']]
    ];
    render_with_layout(ROOT_VIEW.'/layout.php', ['content'=>ROOT_VIEW.'/argue.php'], compact('argue', 'argue_content'));
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
function action_ajax_do()
{
    if (!isset($_GET['action'])) die("no action");
    if (!isset($_GET['id'])) die("no id");

    $action = $_GET['action'];

    // login check
    $login_check_list = [
        'choose_side',
    ];
    if (in_array($action, $login_check_list)) {
        $cur_user = cur_user();
        if (!$cur_user) die("need_login");
        $GLOBALS['cur_user'] = $cur_user;
    }
    $func = "_action_".$_GET['action'];
    if (!function_exists($func)) die("no func");
    return $func($_GET['id']);
}
function _action_choose_side($id)
{
    $argue = find_or_404('argue', $id);
    list($positive, $negative) = argue_choose_side($argue, $GLOBALS['cur_user'], get_php_input());
    // 
    $total = $positive + $negative;
    $ratios = [round($positive/$total*100),round($negative/$total*100)];
    echo implode(',',$ratios);
}
<?php
function cur_user() {
    if (!isset($_SESSION['cur_user_id'])) return null;
    $id = $_SESSION['cur_user_id'];
    if (!$id) return null;
    return ORM::for_table($id)->find_one($id);
}
function argue_choose_side($argue, $cur_user, $_side) {
    list($time,$side) = explode(':',$_side);
    $key = "{$time}_number";
    $content = json_decode($argue['content'],true);
    if (!isset($content[$key])) $content[$key] = [0,0];
    $content[$key][$side]++;
    $argue['content'] = json_encode($content);
    $argue->save();
    user_log($cur_user->id, $argue->id, 'choose_side', $_side);
    return $content[$key];
}
function user_log($user_id,$argue_id,$action,$data) {
    $l = ORM::for_table('user_log')->create();
    $l->user_id = $user_id;
    $l->action = $action;
    $l->data = $data;
    $l->created = sql_timestamp();
    $l->save();
}
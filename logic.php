<?php

// ==== 登录/用户相关 ====

function cur_user() {
    if (!isset($_SESSION['cur_user_id'])) return null;
    $id = $_SESSION['cur_user_id'];
    if (!$id) return null;
    return ORM::for_table('user')->find_one($id);
}
function login($name,$id_num) {
    $data = compact('name','id_num');
    $user = ORM::for_table('user')->where($data)->find_one();
    if ($user) {
        // do nothing
    } else {
        $user = ORM::for_table('user')->create();
        $user->set($data);
        $user->created = sql_timestamp();
        $user->save();
    }
    $_SESSION['cur_user_id'] = $user->id;
}


/**
 * 选边站
 * @return array|string
 */
function argue_choose_side($argue, $cur_user, $_side) {
    list($time,$side) = explode(':',$_side);
    if (!in_array($time, ['begin', 'end'])) {
        die("time must be begin or end");
    }
    // 用户的初始立场不容许改变
    $data = ['user_id'=>$cur_user->id, 'argue_id'=>$argue->id, 'time'=>$time];
    $us = ORM::for_table('user_side')->where($data)->find_one();
    if ($us && 'begin'==$time) {
        return '初始态度不能改变';
    }
    $us = ORM::for_table('user_side')->where($data)->delete_many();
    $us = ORM::for_table('user_side')->create();
    $us->set($data);
    $us->side = $side;
    $us->created = sql_timestamp();
    $us->save();
    
    // 汇总
    $key = "{$time}_number";
    $content = json_decode($argue['content'],true);
    $content[$key] = [
        ORM::for_table('user_side')->where(['argue_id'=>$argue->id, 'time'=>$time, 'side'=>0])->count(),
        ORM::for_table('user_side')->where(['argue_id'=>$argue->id, 'time'=>$time, 'side'=>1])->count(),
    ];
    $argue['content'] = json_encode($content);
    $argue->save();

    // log
    user_log($cur_user->id, $argue->id, 'choose_side', $_side);
    return $content[$key];
}
function argue_number_to_ratio(&$argue_detail) {
    foreach (['begin', 'end'] as $key) {
        $k = $key.'_number';
        if (!isset($argue_detail[$k])) $argue_detail[$k] = [0,0];
        $argue_detail[$key]['ratios'] = _number_to_ratio($argue_detail[$k]);
        $argue_detail[$key]['numbers'] = $argue_detail[$key.'_number'];
    }
}
function _number_to_ratio($numbers) {
    list($positive, $negative) = $numbers;
    $total = $positive + $negative;
    if ($total == 0) return [0,0];
    $ratios = [round($positive/$total*100),round($negative/$total*100)];
    // if ($ratios[0] > 50) $ratios[0] -= 50; else $ratios[0] = 0;
    // if ($ratios[1] > 50) $ratios[1] -= 50; else $ratios[1] = 0;
    return $ratios;
}
/**
 * 编辑 综述
 * @return [cur_user_id,total_up,content]|string
 */
function argue_edit_summary($argue, $cur_user) {
    $side = _post('side');
    $content = _post('content');
    if ($side==='') die("no side");
    if ($content==='') die("no content");
    $argue_content = json_decode($argue->content, true);
    if (!isset($argue_content['summary'])) {
        $argue_content['summary'] = [[],[]];
    }
    if ($argue_content['summary'][$side]) {
        list($old_user_id, $old_total_up, $_) = $argue_content['summary'][$side];
        $old_user = ORM::for_table('user')->find_one($old_user_id);
        if ($old_user->total_up > $cur_user->total_up) {
            return "您的积分不够编辑";
        }
    }
    $argue_content['summary'][$side] = [$cur_user->id,$cur_user->total_up,$content];
    $argue->content = json_encode($argue_content);
    $argue->save();
    return $argue_content['summary'][$side];
}

function user_log($user_id,$argue_id,$action,$data) {
    $l = ORM::for_table('user_log')->create();
    $l->user_id = $user_id;
    $l->argue_id = $argue_id;
    $l->action = $action;
    $l->data = $data;
    $l->created = sql_timestamp();
    $l->save();
}
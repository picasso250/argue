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

// ==== 辩题 ====

function argue_get_point_list($n) {
    $list = ORM::for_table('argue_point')->order_by_desc('up_vote')->limit($n)->find_many();
    return array_map(function($e){
        $e['content'] = json_decode($e['content'],true);
        return $e->as_array();
    },$list);
}
function argue_get_point_list_user($point_list) {
    $ids = [];
    array_map(function($e)use(&$ids){
        if ($e['content'][0]) $ids[] = $e['content'][0][0];
        if ($e['content'][1]) $ids[] = $e['content'][1][0];
        return $ids;
    },$point_list);
    $list = ORM::for_table('user')->select(['id','nickname','name'])->where_id_in($ids)->find_array();
    $kvs = [];
    foreach ($list as $key => $value) {
        $kvs[$value['id']] = $value;
    }
    return $kvs;
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
    if (!isset($argue_content['summary']))
        $argue_content['summary'] = [null,null];
    if ($argue_content['summary'][$side]) {
        $sm = $argue_content['summary'][$side];
        $old_user = ORM::for_table('user')->find_one($sm->user_id);
        if ($old_user->total_up > $cur_user->total_up) {
            return "您的积分不够编辑";
        }
    }
    $argue_content['summary'][$side] = [
        'user_id' => $cur_user->id,
        'name' => $cur_user->nickname ? $cur_user->nickname : $cur_user->name,
        'total_up' => $cur_user->total_up,
        'content' => $content,
    ];
    $argue->content = json_encode($argue_content);
    $argue->save();

    user_log($cur_user->id, $argue->id, 'edit_summary', json_encode($argue_content['summary'][$side]));
    return $argue_content['summary'][$side];
}

// 新增观点
function argue_add_point($argue, $cur_user) {
    $side = _post('side');
    $content = _post('content');
    if ($side==='') die("no side");
    if ($content==='') die("no content");

    $c = [null,null];
    $c[$side] = _argue_point_content($content, $cur_user);
    $argue_point = ORM::for_table('argue_point')->create();
    $argue_point = _argue_point_update($argue_point,$cur_user, $argue,$c);

    $data = [$argue_point->id, $side, $c[$side]];
    user_log($cur_user->id, $argue->id, 'add_point', json_encode($data));

    // 反对观点息息相关，而且可能在激烈的辩论过程中频繁改变，所以也一起返回
    $data = $argue_point->as_array();
    $data['content'] = $c;
    return $data;
}
// 编辑观点
function argue_edit_point($argue, $cur_user) {
    $side = _post('side');
    $content = _post('content');
    $pid = _post('pid');
    if ($side==='') die("no side");
    if ($content==='') die("no content");

    $argue_point = find_or_404('argue_point', $pid);
    $c = json_decode($argue_point['content'],true);
    if ($c[$side]) {
        $old_user = ORM::for_table('user')->find_one($c[$side]['user_id']);
        if ($old_user->total_up > $cur_user->total_up) {
            return "您的积分不够编辑";
        }
    }
    $c[$side] = _argue_point_content($content, $cur_user);
    $argue_point = _argue_point_update($argue_point,$cur_user, $argue,$c);

    $data = [$argue_point->id, $side, $c[$side]];
    user_log($cur_user->id, $argue->id, 'edit_point', json_encode($data));

    // 反对观点息息相关，而且可能在激烈的辩论过程中频繁改变，所以也一起返回
    $data = $argue_point->as_array();
    $data['content'] = $c;
    return $data;
}
function _argue_point_content($content, $cur_user) {
    return [
        'user_id'=>$cur_user->id,
        'name' => $cur_user->nickname ? $cur_user->nickname : $cur_user->name,
        'total_up'=>$cur_user->total_up,
        'content'=>$content,
    ];
}
function _argue_point_update($argue_point,$cur_user,$argue,$c)
{
    $argue_point->user_id = $cur_user->id;
    $argue_point->argue_id = $argue->id;
    $argue_point->content = json_encode($c);
    $argue_point->updated = sql_timestamp();
    $argue_point->save();
    return $argue_point;
}

/**
 * 记录用户动作
 * 这个网站上的一切操作都是有据可查的（撕逼网站，不得不如此）
 */
function user_log($user_id,$argue_id,$action,$data) {
    $l = ORM::for_table('user_log')->create();
    $l->user_id = $user_id;
    $l->argue_id = $argue_id;
    $l->action = $action;
    $l->data = $data;
    $l->created = sql_timestamp();
    $l->save();
}
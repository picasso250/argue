<style>
.point-view-box{
    /* border: 1px solid; */
}
.positive-bar {
    width:100%;
    height:100%;
}
.positive-bar div {
    width:100%;
    height:100%;
    background-color:green;
    margin: auto;
    margin-right: 0;
    color: white;
    text-align: right;
}
.negative-bar {
    width:100%;
    height:100%;
}
.negative-bar div {
    width:100%;
    height:100%;
    background-color:red;
    color: white;
}
.sm {
    font-size:small;
}
.table-title {
    text-align: center;
}
</style>

<script>
var page_data = {
            me: <?= json_encode($GLOBALS['cur_user']?$GLOBALS['cur_user']->as_array():null) ?>,
            id: <?= $argue['id'] ?>,
            'begin': <?= json_encode($argue_content['begin']) ?>,
            'end': <?= json_encode($argue_content['end']) ?>,
            summary_edit_mode: [false,false],
            summary: <?= json_encode($argue_content['summary']) ?>,
            point_list: <?= json_encode($point_list) ?>,
            point_to_add: [],
        };
</script>
<script src="/argue.js"></script>

<h1><?= htmlentities($argue['title']) ?></h1>
<table class="table" id="point_table">

<colgroup>
  <col style="width:6rem; text-align:right;">
  <col style="background-color:#dbffdb">
  <col style="background-color:#ffc0c0">
</colgroup>

<tbody>

<tr>
<td></td>
<td class="table-title">正方</td>
<td class="table-title">反方</td>
</tr>

<tr class="value-bar">
<td>开始观点</td>
<td>
    <a href="javascript:void(0)" v-on:click="choose_side" class="choose-side-btn" style="float:left"  data-time="begin" data-side="0">◀</a>
    <div style="text-align: right;">({{ begin.ratios[0] }}%) {{ begin.numbers[0] }} 人</div>
</td>
<td>
    <a href="javascript:void(0)" v-on:click="choose_side" class="choose-side-btn" style="float:right" data-time="begin" data-side="1">▶</a>
    <div>{{ begin.numbers[1] }} 人 ({{ begin.ratios[1] }}%)</div>
</td>
</tr>

<tr>
<td>综述</td>
<td v-for="(_, i) in [0,1]">
    <div v-if="!(summary_edit_mode[i])" :data-side="i">
        <pre>{{ summary[i]? summary[i].content : '' }}</pre>
        <div v-if="summary[i]" class="sm" >拥有者 {{ summary[i].name }}</div>
        <a href="javascript:void(0);" class="sm"
            v-if="me!==null" v-on:click="prepare_edit_summary">编辑</a>
    </div>
    <div v-else :data-side="i">
        <div style="width:100%;"><textarea style="width:100%;" v-model="summary[i].content"></textarea></div>
        <a href="javascript:void(0);" class="btn btn-primary btn-sm" v-on:click="edit_summary" >提交</a>
        <a href="javascript:void(0);" class="btn btn-light btn-sm"   v-on:click="$set(summary_edit_mode,i,false)" >取消</a>
    </div>
</td>
</tr>

<?php /* 论点 */ ?>
<tr v-for="(point, index) in point_list">
    <td v-if="index===0" :rowspan="point_list.length+(me===null?0:1)">论点</td>
    <td v-for="(pc, i) in point.content">
        <div v-if="point_edit_mode[i][index]" :data-index="index" :data-side="i" :data-id="point.id">
            <div><textarea v-model="point.content[i].content" style="width:100%" ></textarea></div>
            <a href="javascript:void(0);" class="btn btn-primary btn-sm" v-on:click="edit_point" >提交</a>
            <a href="javascript:void(0);" class="btn btn-light btn-sm" v-on:click="edit_point_dismiss" >取消</a>
        </div>
        <div v-else :data-index="index" :data-side="i" :data-id="point.id">
            <pre class="point-view-box">{{ pc!==null? pc.content : '' }}</pre>
            <div v-if="pc!==null" class="sm" >拥有者 {{ pc.name}}</div>
            <a href="javascript:void(0);" class="sm" v-if="me!==null&&pc===null" v-on:click="prepare_edit_point" >反驳</a>
            <a href="javascript:void(0);" class="sm" v-if="me!==null&&pc!==null" v-on:click="point_up" >点赞</a>
            <span v-if="pc !== null" >{{pc.up_vote}} 人点赞</span>
            <a href="javascript:void(0);" class="sm" v-if="me!==null&&(pc===null || pc.total_up<=me.total_up)" v-on:click="prepare_edit_point" >编辑</a>
        </div>
    </td>
</tr>
<tr v-if="me!==null">
    <td v-if="point_list.length===0" :rowspan="point_list.length+1">论点</td>
    <td v-for="(_, i) in [0,1]">
        <div><textarea v-model="point_to_add[i]"></textarea></div>
        <a href="javascript:void(0);" class="btn btn-primary btn-sm" v-on:click="add_point" :data-side="i" >添加论点</a>
    </td>
</tr>

<tr class="value-bar">
<td>结束观点</td>
<td>
    <a href="javascript:void(0)" v-on:click="choose_side" class="choose-side-btn" style="float:left"  data-time="end" data-side="0">◀</a>
    <div style="text-align: right;">({{ end.ratios[0] }}%) {{ end.numbers[0] }} 人</div>
</td>
<td>
    <a href="javascript:void(0)" v-on:click="choose_side" class="choose-side-btn" style="float:right" data-time="end" data-side="1">▶</a>
    <div>{{ end.numbers[1] }} 人 ({{ end.ratios[1] }}%)</div>
</td>
</tr>

</tbody>
</table>

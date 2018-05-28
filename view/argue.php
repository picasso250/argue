<style>
.point-view-box{
    border: 1px solid;
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
            me: <?= json_encode($GLOBALS['cur_user']->as_array()) ?>,
            id: <?= $argue['id'] ?>,
            'begin': <?= json_encode($argue_content['begin']) ?>,
            'end': <?= json_encode($argue_content['end']) ?>,
            summary_edit_mode: [false,false],
            summary: <?= json_encode($argue_content['summary']) ?>,
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
<td>
    <div v-if="!(summary_edit_mode[0])" data-side="0">
        <pre>{{ summary[0]? summary[0][2] : '' }}</pre>
        <a href="javascript:void(0);" class="sm" v-on:click="prepare_edit_summary">编辑</a>
    </div>
    <div v-else data-side="0">
        <div style="width:100%;"><textarea style="width:100%;" v-model="summary[0][2]"></textarea></div>
        <a href="javascript:void(0);" class="btn btn-primary btn-sm" v-on:click="edit_summary" >提交</a>
        <a href="javascript:void(0);" class="btn btn-light btn-sm" v-on:click="$set(summary_edit_mode,0,false)" >取消</a>
    </div>
</td>
<td>
    <div v-if="!(summary_edit_mode[1])" data-side="1">
        <pre>{{ summary[1]? summary[1][2] : '' }}</pre>
        <a href="javascript:void(0);" class="sm" v-on:click="prepare_edit_summary">编辑</a>
    </div>
    <div v-else data-side="1">
        <div style="width:100%;"><textarea style="width:100%;" v-model="summary[1][2]"></textarea></div>
        <a href="javascript:void(0);" class="btn btn-primary btn-sm" v-on:click="edit_summary" >提交</a>
        <a href="javascript:void(0);" class="btn btn-light btn-sm" v-on:click="$set(summary_edit_mode,1,false)" >取消</a>
    </div>
</td>
</tr>

<?php /* 论点 */ foreach ($point_list as $key => $point): ?>
<tr>
    <?php if ($key==0):?> <td rowspan="<?= count($point_list) ?>">论点</td> <?php endif ?>
    <td>
        <p><?= $point['stand'] == 0 ? ('论点 '.($key+1)) : '反驳' ?></p>
        <div class="point-view-box"><?= htmlspecialchars($point['stand'] == 0 ? $point['content']['content'] : $point['opposite']['content']) ?></div>
    </td>
    <td>
        <p><?= $point['stand'] == 1 ? ('论点 '.($key+1)) : '反驳' ?></p>
        <div class="point-view-box"><?= htmlspecialchars($point['stand'] == 1 ? $point['content']['content'] : $point['opposite']['content']) ?></div>
    </td>
</tr>
<?php endforeach ?>

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


<?php function _point($stand) { 

}?>
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
function prepare_edit_summary(e) {
    $(e).prev().show().prev().hide();
    $(e).hide().next().show();
}
function edit_summary(e,id) {
    var data = $(e).prev().prev().find('textarea').val();
    $.post('/ajax_do?action=edit_summary&id='+id, data, function (ret) {
    });
}
$(function () {
    var app = new Vue({
        el: '#point_table',
        data: {
            id: <?= $argue['id'] ?>,
            'begin': <?= json_encode($argue_content['begin']) ?>,
            'end': <?= json_encode($argue_content['end']) ?>
        },
        methods: {
            choose_side: function (event) {
                var e = event.target,time=$(e).data('time'),side=$(e).data('side'),id=this.id;
                console.log(e,time,side,id)
                var that = this;
                $.post('/ajax_do?action=choose_side&id='+id, time+':'+side, function (ret) {
                    if (ret.code===0) {
                        that[time] = ret.data;
                    } else {
                        alert(ret.msg);
                    }
                }, 'json');
            }
        }
    });
});

</script>

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
    <div><?= htmlentities($argue_content['summary'][0]) ?></div>
    <div style="display:none;width:100%;"><textarea style="width:100%;"><?= $argue_content['summary'][0] ?></textarea></div>
    <a href="javascript:void(0);" class="sm" onclick="prepare_edit_summary(this)">编辑</a>
    <a href="javascript:void(0);" class="btn btn-light btn-sm" onclick="edit_summary(this,<?= $argue['id'] ?>)" style="display:none">提交</a>
</td>
<td>
    <div><?= htmlentities($argue_content['summary'][1]) ?></div>
    <textarea style="display:none"><?= $argue_content['summary'][1] ?></textarea>
    <a href="javascript:void(0);" class="btn btn-light btn-sm" onclick="prepare_edit_summary(this)">编辑综述</a>
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
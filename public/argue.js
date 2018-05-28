
$(function () {
    var app = new Vue({
        el: '#point_table',
        data: page_data,
        methods: {
            choose_side: function (event) {
                var e = event.target, time = $(e).data('time'), side = $(e).data('side'), id = this.id;
                console.log(e, time, side, id)
                var that = this;
                $.post('/ajax_do?action=choose_side&id=' + id, time + ':' + side, function (ret) {
                    if (ret.code === 0) {
                        that[time] = ret.data;
                    } else {
                        alert(ret.msg);
                    }
                }, 'json');
            },
            prepare_edit_summary: function (event) {
                var side = $(event.target).parent().data('side');
                console.log(side, "0", 0)
                if (this.summary[side].length === 0 || this.summary[side][1] <= this.me.total_up)
                    this.$set(this.summary_edit_mode, side, true);
                else
                    alert('您的积分不够编辑');
            },
            edit_summary: function (event) {
                var side = $(event.target).parent().data('side');
                var content = this["summary"][side][2];
                if (content.trim().length === 0) alert('综述不能为空');
                var data = {
                    side: side,
                    content: content,
                };
                var id = this.id;
                var that = this;
                $.post('/ajax_do?action=edit_summary&id=' + id, data, function (ret) {
                    if (ret.code === 0) {
                        that.summary[side] = ret.data;
                        that.$set(that.summary_edit_mode, side, false);
                    } else {
                        alert(ret.msg);
                    }
                }, 'json');
            }
        }
    });
});
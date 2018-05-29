
$(function () {
    page_data.uid = 0; // tmp
    page_data.c = []; // tmp
    page_data.point_edit_mode = fillArray(false,page_data.point_list.length); // tmp

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
                if (this.summary[side] === null || this.summary[side].total_up <= this.me.total_up) {
                    if (this.summary[side] === null) this.summary[side] = {content:''};
                    this.$set(this.summary_edit_mode, side, true);
                } else
                    alert('您的积分不够编辑');
            },
            edit_summary: function (event) {
                var side = $(event.target).parent().data('side');
                var content = this["summary"][side].content;
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
            },
            // 增加观点
            add_point: function (event) {
                var side = $(event.target).data('side');
                var id = this.id;
                var data = {
                    side: side,
                    content: this.point_to_add[side],
                };
                var that = this;
                $.post('/ajax_do?action=add_point&id=' + id, data, function (ret) {
                    if (ret.code === 0) {
                        that.point_list.push(ret.data);
                    } else {
                        alert(ret.msg);
                    }
                }, 'json');
            },
            edit_point: function (event) {
                var p = $(event.target).parent();
                var side = p.data('side');
                var index = p.data('index');
                var id = this.id;
                var data = {
                    side: side,
                    content: this.point_to_add[side],
                };
                var that = this;
                $.post('/ajax_do?action=add_point&id=' + id, data, function (ret) {
                    if (ret.code === 0) {
                        that.point_list.push(ret.data);
                    } else {
                        alert(ret.msg);
                    }
                }, 'json');
            },

        }
    });
});
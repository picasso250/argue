
$(function () {
    page_data.uid = 0; // tmp
    page_data.c = []; // tmp
    var p = fillArray(false, page_data.point_list.length);
    page_data.point_edit_mode = [p,p];

    _edit_point_mode_change = function (that, side, index, val) {
        // https://stackoverflow.com/questions/45644781/update-value-in-multidimensional-array-in-vue
        var p = that.point_edit_mode[side].slice(0);
        p[index] = val;
        that.$set(that.point_edit_mode, side, p);
    };

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
                        that.$set(that.point_to_add,side, '');
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
                    content: this.point_list[index].content[side].content,
                    pid: p.data('id'),
                };
                var that = this;
                $.post('/ajax_do?action=edit_point&id=' + id, data, function (ret) {
                    if (ret.code === 0) {
                        that.$set(that.point_list, index, ret.data);
                        _edit_point_mode_change(that, side, index, false);
                    } else {
                        alert(ret.msg);
                    }
                }, 'json');
            },
            prepare_edit_point: function (event) {
                var p = $(event.target).parent();
                var index = p.data('index');
                var side = p.data('side');
                if (this.point_list[index].content[side] === null)
                    this.point_list[index].content[side] ={ content: '' };
                _edit_point_mode_change(this, side,index,true);
            },
            edit_point_dismiss: function (event) {
                var p = $(event.target).parent();
                var index = p.data('index');
                var side = p.data('side');
                _edit_point_mode_change(this, side, index, false);
            },
            point_up: function (event) {
                var p = $(event.target).parent();
                var side = p.data('side');
                var index = p.data('index');
                var id = this.id;
                var data = {
                    id: p.data('id'),
                    side: side,
                };
                var that = this;
                $.post('/ajax_do?action=point_up&id=' + id, data, function (ret) {
                    if (ret.code === 0) {
                        // todo
                        that.$set(that.point_list, index, ret.data);
                        _edit_point_mode_change(that, side, index, false);
                    } else {
                        alert(ret.msg);
                    }
                }, 'json');
            }
        }
    });
});
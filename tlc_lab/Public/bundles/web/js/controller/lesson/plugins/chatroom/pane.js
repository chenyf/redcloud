define(function(require, exports, module) {

	var Widget = require('widget');

	var ChatRoomPane = Widget.extend({
		attrs: {
            plugin: null
        },
        events: {},
		setup: function() {},
		show: function() {
			this.get('plugin').toolbar.showPane(this.get('plugin').code);
			var pane = this,
            toolbar = pane.get('plugin').toolbar;
			pane.element.html('<p class="text-muted" style="margin: 50% 35%;">聊天室加载中....</p>');
            $.get(pane.get('plugin').api.init, {
                courseId: toolbar.get('courseId'),
                lessonId: toolbar.get('lessonId'),
                center:center
            }, function(html) {
            	pane.element.html(html);
            });

		}
	});

	module.exports = ChatRoomPane;

});
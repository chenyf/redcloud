define(function(require, exports, module) {

	var BasePlugin = require('../base-plugin');

	var ChatRoomPane = require('./pane');

	var ChatRoomPlugin = BasePlugin.extend({
		code: 'chatroom',
		name: '群聊',
		iconClass: 'glyphicon glyphicon-leaf',
		api: {
			init: '/Course/LessonChatRoomPlugin/initAction'
		},
		execute: function() {
			if (!this.pane) {
				this.pane = new ChatRoomPane({
					element: this.toolbar.createPane(this.code),
					code: this.code,
					toolbar: this.toolbar,
					plugin: this
				}).render();
			}
			this.pane.show();
		},
		onChangeLesson: function() {
			this.pane.show();
		}

	});

	module.exports = ChatRoomPlugin;

});
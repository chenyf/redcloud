define(function (require, exports, module) {

	var Widget = require('widget');
	var Templatable = require('templatable');
	var Handlebars = require('handlebars');

	var catSelect = Widget.extend({
		Implements : Templatable,
		attrs : {
			//模板
			template : '<div class="set-sort-popupcon">' + 
                '<div class="row">'+
             '       <div class="col-md-3 text-right" style="padding:0;"><span class="text-muted">全部课程分类：</span></div>'+
             '       <div class="col-md-9 cateBlock" style="padding-left:0;">'+
             '           <div class="sort-all-border">'+
                            '{{list cateTree}}' +
             '           </div>'+
             '       </div>'+
             '   </div>'+
            '</div>'+
            '<div class="row mtm">'+
             '   <div class="col-md-3 text-right mtm" style="padding:0;"><span class="text-muted">已选择课程分类：</span></div>'+
             '   <div class="col-md-9" style="padding-left:0;">'+
             '       <div class="sort-choose-ok" id="sort-choose-ok">'+
             '       </div> '+
             '   </div>'+
            '</div>',
			//已选择的分类
			'selected' : [],
			selectInput : 'input[name=categoryId]',
			//是否支持多选,默认支持
			multiple : false,
			parentNode : '#select-cate',
			model : function () {

			}
		},
		events : {
			'click a.select-cate' : 'onSelectCate',
			'click .del-selected' : 'onDeletedCate'
		},
		templateHelpers : {
			'list' : function (item) {
				var out = '';
				for (var i in item) {
					var name = item[i].name;
					var id = item[i].id;
					var courseCode = item[i].courseCode;
					out += '<a href="javascript:void(0)" class="select-cate" data-courseCode="' + courseCode + '" data-id="' + id + '">' + name + '</a>'
				}
				return new Handlebars.SafeString(out);
			}
		},

		'onSelectCate' : function (event) {
			var element = $(event.target);
			var id = element.data('id');
			var cateTree = this.get('cateTree');

			element.addClass('active').siblings('a').removeClass('active');

			this._removeNextAll(element.parents('div.cateBlock'));

			var elementObject = this._findObject(id, cateTree);

			// if ( elementObject.isLeafNode == 1 ) {
			// 	if ( $.inArray(elementObject, this.get('selected')) == -1 ) {
			// 		this.addSelected(elementObject);
			// 		this._showSelected(this.get('selected'));
			// 		this.generate_input();
			// 	}
            //
			// } else
			if ( !$.isEmptyObject(elementObject.child) ) {
				this._showChild(element, elementObject.child);
			}

			if ( $.inArray(elementObject, this.get('selected')) == -1 ) {
				this.addSelected(elementObject);
				this._showSelected(this.get('selected'));
				this.generate_input();
			}
		},

		'onDeletedCate' : function (event) {
			var element = $(event.target);
			var selected = this.get('selected');
			var id = element.data('id');
			for (var i = 0, count = selected.length; i < count; i++) {
				if ( selected[i] && selected[i].id == id ) {
					this.get('selected').splice(i, 1);
					element.parent("span").remove();
					this.generate_input();
				}
			}
		},

		'generate_input' : function () {
			var selected = this.get('selected');
			var ids = [];
			if ( selected.length > 0 ) {
				for (var i = 0, count = selected.length; i < count; i++) {
					ids.push(selected[i].id);
				}
			}
			$(this.get('selectInput')).val(ids.join(','));
		},

		'addSelected' : function (elementObject) {
			// if ( this.get('multiple') != true ) {
			// 	this.get('selected').pop();
			// }
			this.get('selected').pop();
			this.get('selected').push(elementObject);
		},

		'_findObject' : function (id, tree) {
			for (var i in tree) {
				if ( i == id ) {
					return tree[i];
				}
				var res = this._findObject(id, tree[i].child)
				if ( res ) {
					return res;
				}
			}
		},

		'_removeNextAll' : function (element) {
                        element.nextAll('div.cateBlock').remove();
		},

		'_showChild' : function (element, children) {
			var htmlString = this._buildHtml({item:children, pNode:{text:element.text() }});
			element.parents('div.cateBlock').after(htmlString);

		},
                
		'_findParentName' : function(pid,name){
			var cateTree = this.get('cateTree');
			if(pid == "0"){
				return name;
			}else{
				var tree = this._findObject(pid,cateTree);
				name = tree["name"]+"—"+name;
				return this._findParentName(tree["parentId"],name);
			}
		},
                    
		'_showSelected' : function (selected) {
			this.element.find('#sort-choose-ok').empty();
			var out = '';
			for (var i = 0, count = selected.length; i < count; i++) {
				var name = selected[i].name;
				if(selected[i].parentId != "0"){
					name = this._findParentName(selected[i].parentId,name);
				}
				out += '<span><i data-id="' + selected[i].id + '" data-coursecode="' + selected[i].courseCode + '" data-name="'+name+'" class="del-selected pull-right" >×</i><name>' + name + '</name></span>';
			}
			this.element.find('#sort-choose-ok').append(out);
		},
		'_buildHtml' : function (item) {
                    var itemObj = item.item;
                    var pText = item.pNode.text;
                    var safeStringObj = this.templateHelpers.list(itemObj);
/*			var out = '<div class="sort-all-border">';
			for (var i in item) {
				var name = item[i].name;
				var id = item[i].id;
				out += '<a href="javascript:void(0)" class="select-cate" data-id="' + id + '">' + name + '</a>'
			}
			out += '</div>';*/
                    var out = '<div class="col-md-9 col-md-offset-3 mts cateBlock" style="padding-left:0;"><div class="sort-all-border">';
                    out += '<p class="c-second-tit">'+pText+'</p>';  
                    out += safeStringObj;
                    out += '</div></div>';
                    return out;
		},
		setup : function () {
			var _this = this;
			if ( $.isEmptyObject(_this.get('model')) ) {
				throw new Error('数据初始化失败')
			}
			_this.set('cateTree', _this.get('model').cateTree);

			if ( $(this.get('parentNode')).data('multiple') == false ) {
                                this.set('multiple', false)
                        }else{
                                this.set('multiple', true)
                        }

			if ( $(_this.get('selectInput')).val() ) {
				var ids = $(_this.get('selectInput')).val().split(',');
				for (var i = 0, count = ids.length; i < count; i++) {
					var elementObject = _this._findObject(ids[i], _this.get('cateTree'));
					if ( elementObject ) {
						_this.get('selected').push(elementObject)
						_this._showSelected(_this.get('selected'));
					}

				}
			}

			_this.render();
		}
	});

	var getCateTreeByAjax = function (url, callback) {
		$.ajax({
			url : url,
			type : 'post',
			dataType : 'json',
			success : function (data) {
				callback(data);
			}
		})
	};

	exports.newCatSelect = function (param) {
		$(param.parentNode).append('<span class="load text-muted">加载中...</span>');
		getCateTreeByAjax($(param.parentNode).data('url'), function (data) {
			param.model = {
				cateTree : data,
				type : param.type || '课程分类'
			};
			new catSelect(param);
			$(param.parentNode).find('span.load').remove();
		})
	};

});
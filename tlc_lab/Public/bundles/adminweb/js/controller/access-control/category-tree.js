define(function (require, exports, module) {


	exports.run = function () {

		$('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
		$('.tree li.parent_li > span').on('click', function (e) {
			var children = $(this).parent('li.parent_li').find(' > ul > li');
			if (children.is(":visible")) {
				children.hide('fast');
				$(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
			} else {
				children.show('fast');
				$(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
			}
			e.stopPropagation();
		});

		var treeJson = {};
		$('input[name=do]').click(function(){
			var child = $(this).parent('li.parent_li').find('ul input[name=do]');
			child.prop('checked',$(this).prop('checked'))
				.siblings('label')
				.find('input[name=down]')
				.prop('checked',$(this).prop('checked'));
			generatorJson();

		});

		var generatorJson = function(){
                        var maxLevel = 0;
			treeJson = {};
			$('input[name=do]').each(function(){
				if($(this).prop('checked')){
					var id = $(this).val();
                                        var level = $(this).data('level');
                                        if(parseInt(level) >=maxLevel) maxLevel = parseInt(level);
                                        level = 'L'+level;
					if(treeJson[level] == undefined){
						treeJson[level] = []
					}
					treeJson[level].push(id);

				}
			});
                        if(maxLevel) 
                            treeJson = filterJson(maxLevel,treeJson);
			var categorys = JSON.stringify(treeJson);
			$('input[name=categorys]').val(categorys);
		}
                
                /**
                 * 处理类别
                 * @author fubaosheng 2015-05-08
                 */
                var filterJson = function(maxLevel,treeJson){
                    for(var i=0;i<maxLevel;i++){
                        if( typeof(treeJson["L"+(i+1)]) == "undefined" ){
                            for(var t in treeJson){
                                var l = parseInt(t.replace("L",""));
                                treeJson["L"+(i+1)] = [];
                                if((i+1)<l){
                                    var jsonObj = treeJson[t];
                                    delete treeJson[t];
                                    treeJson["L"+l] = jsonObj;
                                }
                            }
                        }
                    }
                    return treeJson;
                }

	};

});
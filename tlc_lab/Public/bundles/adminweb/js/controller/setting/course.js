define(function(require, exports, module) {

    require('jquery.sortable');
    var Notify = require('common/bootstrap-notify');
    var Uploader = require('upload');

    exports.run = function() {

    	$(".buy-userinfo-list").sortable({
			'distance':20
	    });
            
            var sortList = function($list) {
            var data = $list.sortable("serialize").get();
            $.post($list.data('sortUrl'), {ids:data}, function(response){
                var lessonNum = chapterNum = unitNum = 0;

                $list.find('.item-lesson, .item-chapter').each(function() {
                    var $item = $(this);
                    if ($item.hasClass('item-lesson')) {
                        lessonNum ++;
                        $item.find('.number').text(lessonNum);
                    } else if ($item.hasClass('item-chapter-unit')) {
                        unitNum ++;
                        $item.find('.number').text(unitNum);
                    } else if ($item.hasClass('item-chapter')) {
                        chapterNum ++;
                        unitNum = 0;
                        $item.find('.number').text(chapterNum);
                    }

                });
            });
        };

        var $list = $("#course-item-sort").sortable({
            distance: 20,
            onDrop: function (item, container, _super) {
                _super(item, container);
                sortList($list);
            },
        });
            

        if($("[name=buy_fill_userinfo]:checked").val()==1)$("#buy-userinfo-list").hide();
        if($("[name=buy_fill_userinfo]:checked").val()==0){
                    $("#buy-userinfo-list").hide();
                    $("#show-list").hide();
                }
        
        $("[name=buy_fill_userinfo]").on("click",function(){
            if($("[name=buy_fill_userinfo]:checked").val()==1){
                                $("#show-list").show();
                                $("#buy-userinfo-list").hide();
                            }
                      if($("[name=buy_fill_userinfo]:checked").val()==0){
                                $("#buy-userinfo-list").hide();
                                $("#show-list").hide();
                            }
    	});
    	
          $("#hide-list-btn").on("click",function(){
            $("#buy-userinfo-list").hide();
             $("#show-list").show();
        	});

        	$("#show-list-btn").on("click",function(){
            $("#buy-userinfo-list").show();
             $("#show-list").hide();
       	 });

    };

});
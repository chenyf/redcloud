define(function(require, exports, module) {

    var ulNum = $("ul.index_category li").length;

    exports.run = function() {

        $(".banner-scroll-btn span").click(function() {
            var index = $(this).index();
            $('#media-list li').hide().eq(index).show();
            $(".banner-scroll-btn span").removeClass('active').eq(index).addClass('active');
        })
        var indexNum = 0;
        var nextPev = function() {
            var count = $('#media-list li').size();

            if (indexNum >= count) {
                indexNum = 0;
            }
            $('#media-list li').hide().eq(indexNum).show();
            $(".banner-scroll-btn span").removeClass('active').eq(indexNum).addClass('active');
            indexNum = indexNum + 1;
        }
        
        

        var stv = setInterval(nextPev, 3000);
        $(".media-list").hover(
                function() {
                    clearInterval(stv);
                },
                function() {
                    stv = setInterval(nextPev, 3000);
                }
        );


        $('input:checkbox[name="coursesTypeChoices"]').on("change", function() {
            var element = $(this);
            if (element.attr("id") == "liveCourses" && element.prop('checked')) {
                $("#vipCourses").prop('checked', false);
            }
            if (element.attr("id") == "vipCourses" && element.prop('checked')) {
                $("#liveCourses").prop('checked', false);
            }
            $(this).parents("form").submit();
        });


        $("ul.index_category li").mouseenter(function() {

            var oDiv = $(this).find("div.subContent");
            var oDivMinHeight = ulNum * 56;

            if (parseInt(ulNum) >9) {
               $(".navside-arrow").hide();
                $("ul.index_category li").show();
                $("#allList").css("height", parseInt(ulNum) * 56 + "px");
                oDiv.css("min-height", oDivMinHeight + "px");
            } else {
                oDiv.css("min-height", "520px");
            }
            oDiv.show();
            $(this).addClass("active");
        }).mouseleave(function() {
            $(this).find("div.subContent").hide();
            $(this).removeClass("active");

            if (parseInt(ulNum) > 9) {
                $(".navside-arrow").show();
                for (i = 9; i < parseInt(ulNum); i++) {
                    $("ul.index_category li:eq(" + parseInt(i) + ")").hide();
                }
                $("#allList").css("height", "520px");
            }
        });

        var imageSize = $.trim($("#blocksImgsize").val());
        if (imageSize == 'big') {
            $(".categoryLayer").mouseenter(function() {
                $("#allList").show();
            }).mouseleave(function() {
                $("#allList").hide();
            })
        }

    };

    $('.p-close-btn').click(function() {
        $('.app-popupbox').fadeOut();
    })

});
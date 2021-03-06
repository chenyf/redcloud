// 
// This work by http://twitter.com/Ben_Lowe of http://www.triballabs.net is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.0 UK: England & Wales License.
// http://creativecommons.org/licenses/by-nc-sa/2.0/uk/ 
//
$(function () {
    $('.timetable').resize(function (e) {
        $(this).resizeTimetable();
    });
    $(document).ready(function (e) {
        $('.timetable').each(function (e) {
            $(this).initialiseTT();
            $(this).resizeTimetable();
        });
    });

    $.fn.initialiseTT = function () {
        var daysArr = new Array(new Array(), new Array(), new Array(), new Array(), new Array(), new Array(), new Array());

        // create array to determine and calcuate position of clashing events创建数组的确定和处理冲突事件的位置
        jQuery('.tt-event', this).each(function (i) {
            $(this).attr('rel', 'tooltip');
            //$(this).attr('title', $(this).html().replace('<br/>', '\n'));

            $(this).attr('unselectable', 'on')
               .css({
                   '-moz-user-select': 'none',
                   '-webkit-user-select': 'none',
                   'user-select': 'none',
                   '-ms-user-select': 'none'
               })
               .each(function () {
                   this.onselectstart = function () { return false; };
               });

            var placed = false;
            var dayInt = parseInt($(this).data('day'));

            for (i = 0; i < daysArr[dayInt].length; i++) {
                // if nothing in column then add it in如果没有在列中，然后将它添加到
                if (daysArr[dayInt][i].length == 0) {
                    daysArr[dayInt][i].push(new Array($(this).data('id'), $(this).data('start'), $(this).data('duration'), $(this).getHiddenDimensions(false).width));
                    $(this).data('column', i);
                    placed = true;
                }
                else {
                    // check last end time in column and see if it overlaps检查列中的最后一个结束时间，看看是否重叠
                    var lastEv = daysArr[$(this).data('day')][i].last();
                    //重叠
                    //if (lastEv[1] + lastEv[2] <= $(this).data('start')) {
                        daysArr[dayInt][i].push(new Array($(this).data('id'), $(this).data('start'), $(this).data('duration'), $(this).getHiddenDimensions(false).width));
                        $(this).data('column', i);
                        placed = true;
                   // }
                }
            }
            // if not placed then add a new column如果未放置然后添加新列
            if (!placed) {
                daysArr[dayInt].push(new Array(new Array($(this).data('id'), $(this).data('start'), $(this).data('duration'), $(this).getHiddenDimensions(false).width)));
                $(this).data('column', daysArr[dayInt].length - 1);
            }
        });

        // work out the required heights of each column如果没有在列中，然后将它添加到
        // loop through each day算出各列所需的高度
        for (i = 0; i < daysArr.length; i++) {
            //loop through each column
            for (j = 0; j < daysArr[i].length; j++) {
                var maxHeight = 0;
                // loop through each event
                for (k = 0; k < daysArr[i][j].length; k++) {
                    if (daysArr[i][j][k][3] > maxHeight) {
                        maxHeight = daysArr[i][j][k][3];
                    }
                }
                daysArr[i][j].push(maxHeight);
            }
        }

        //loop again through events to set their heights and top offset循环再次通过事件来设置它们的高度和顶部偏移量
        jQuery('.tt-event', this).each(function (i) {
            $(this).height(daysArr[$(this).data('day')][$(this).data('column')].last());

            var top = 0;
            for (i = 0; i < $(this).data('column'); i++) {
                top += daysArr[$(this).data('day')][i].last() + 10;
            }
            $(this).data('top', top);
        });

        jQuery("[rel=tooltip]", this).tooltip();
        jQuery('.timetable', this).tooltip();

        jQuery('.tt-time', this).each(function (i) {
            var height = 0;
            for (j = 0; j < daysArr[i].length; j++) {
                height += daysArr[i][j].last() + 10;
            }

            if (height > 0) {
                $(this).width(height - 5);
            }
        });
    }

    $.fn.resizeTimetable = function () {
        var tt = this;
        // reset day widths to allow for day names changing based on screen width重置日的宽度，以允许根据屏幕宽度改变一天的名称
        jQuery('.tt-day', this).width('auto');
        jQuery('.tt-day', this).css('margin-right', 0);

        var ttDaysExtras = parseInt(jQuery('.tt-days', this).css('border-left-width')) + parseInt(jQuery('.tt-days', this).css('border-right-width')) + parseInt(jQuery('.tt-days', this).css('padding-left')) + parseInt(jQuery('.tt-days', this).css('padding-right'));

        // have to float left to get width correctly in IE8 and below有左浮动得到正确的IE8及以下的宽度
        jQuery('.tt-days', this).css('float', 'left');
        var daysWidth = jQuery('.tt-days', this).width() + ttDaysExtras;  // + border
        jQuery('.tt-days', this).css('width', daysWidth + 'px');
        jQuery('.tt-days', this).css('float', '');
        var temp = jQuery('.tt-days', this).getHiddenDimensions();
        jQuery('.tt-times', this).css('padding-left', daysWidth);

        var ttTimesExtras = parseInt(jQuery('.tt-times', this).css('border-top-width')) + parseInt(jQuery('.tt-times', this).css('border-bottom-width')) + parseInt(jQuery('.tt-times', this).css('padding-top')) + parseInt(jQuery('.tt-times', this).css('padding-bottom'));
        var ttTimeExtras = parseInt(jQuery('.tt-time', this).last().css('padding-bottom')) + parseInt(jQuery('.tt-time', this).last().css('padding-top'));
        var tempWidth = jQuery('.tt-time', this).last().width();
        jQuery('.tt-time', this).last().width(0);
        var timesHeight = 30 + ttTimesExtras; // + border  高度修改 eidt tanhaitao
        //var timesHeight = jQuery('.tt-times', this).height(); // + border
        jQuery('.tt-time', this).last().width(tempWidth);
        jQuery('.tt-days', this).css('top', (timesHeight) + 'px');
        
        $(this).height((timesHeight + jQuery('.tt-days', this).height()) + 'px');

        jQuery('.tt-events', this).css('left', daysWidth);
        jQuery('.tt-events', this).css('top', timesHeight);
        /* check why different between height and width - times/days 检查高度和宽度为什么不同的时间/天*/
        var ttWidth = $(this).width();
        var width = $(this).width() - jQuery('.tt-days', this).width() - ttDaysExtras;
        var height = $(this).height();
        var hours = $(this).data('hours');
        var hourWidth = Math.floor(width / hours);

        // edit  tanhaitao 
        var allHeight =  $(this).height() - jQuery('.tt-times', this).height() - ttTimesExtras;
        var rows = $(this).data('days');
        //var avgHeight =  Math.floor(allHeight / rows) - 1;
        var avgHeight = 48;
        var timeWidth = hourWidth - parseInt(jQuery('.tt-time', this).first().css('padding-left')) - parseInt(jQuery('.tt-time', this).first().css('padding-right')) - parseInt(jQuery('.tt-time', this).first().css('border-left-width')) - parseInt(jQuery('.tt-time', this).first().css('border-right-width'));
        var widthLeft = width;
        var timeHeight = height - ttTimesExtras - ttTimeExtras;
        var timeMarginBottom = -1 * timeHeight;
        var maxDayWidth = 0;
        var dayHeights = new Array();
        var dayYOffset = new Array();

        // set sizes of the times
        jQuery('.tt-time', this).each(function (i) {
            if (i < hours - 1) {
                $(this).width(timeWidth);
                $(this).height(timeHeight);
                $(this).css('margin-bottom', timeMarginBottom);
                widthLeft -= hourWidth;
            }
            else {
                $(this).width(widthLeft - parseInt($(this).css('padding-left')) - parseInt($(this).css('padding-right')) - parseInt($(this).css('border-left-width')) - parseInt($(this).css('border-right-width')));
            }
        });

        // set day sizes
        jQuery('.tt-day', this).each(function (i) {
            var dayWidth = ttWidth - ttDaysExtras - parseInt($(this).css('padding-left')) - parseInt($(this).css('padding-right'));
            $(this).width(dayWidth);
            $(this).css('margin-right', -1 * width);
            dayHeights[i] = $(this).height() + 11;
            if (i == 0) {
                dayYOffset[i] = 0;
            }
            else {
                dayYOffset[i] = dayYOffset[i - 1] + dayHeights[i - 1];
            }
        });

        jQuery('.tt-event', this).each(function (i) {
            var eventStart = $(this).data('start') * avgHeight;
            var eventWidth = Math.max($(this).data('duration') * avgHeight, 1) - 6;
            var eventDay = $(this).data('day');
            var leftLenght = eventDay* hourWidth ;
            var lapId = $(this).data("id");
            $(this).css('left', leftLenght);
            $(this).css('top', eventStart);
            $(this).width(timeWidth);
            $(this).height(eventWidth);
            //上下居中
            $(this).css('line-height',eventWidth+'px');
            //定位重复的图标
            if($('.restLap').is('.iconlap'+lapId)){
                var lapLeftLength = leftLenght + timeWidth -22 ;
                $('.iconlap'+lapId).css('left', lapLeftLength);
                $('.iconlap'+lapId).css('top', eventStart);;
            }
            
        });

        return false;
    };
});
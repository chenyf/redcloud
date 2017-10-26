define(function(require, exports, module) {
   require('jquery-plugin/color/echarts.common.min.js');
    exports.run = function() {
        var xAxisUrl = $("#main").data("url");
        myChart(xAxisUrl);
        $(".search").click(function() {
            $(this).addClass("selected");
            $(this).siblings().removeClass("selected");
            var url = $(this).data("url");
            myChart(url);
        })
        function myChart(xAxisUrl){
            var myChart = echarts.init(document.getElementById('main'));

            // 指定图表的配置项和数据
            option = {
                backgroundColor: 'rgb(242, 242, 230)', //背景色
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['购买数', '注册数']
                },
                toolbox: {
                    show: true,
                    feature: {
                        mark: {show: true},
                        dataZoom: {show: true},
                        dataView: {show: true},
                        magicType: {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                calculable: true,
                dataZoom: {
                    show: true,
                    realtime: true,
                    start: 20,
                    end: 80
                },
                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: (function() {
                            var list = [];
                            $.ajax({
                                type: "post",
                                async: false, //同步执行
                                url: xAxisUrl,
                                data: {},
                                dataType: "json", //返回数据形式为json
                                success: function(result) {
                                    for (var i in result) {
                                        if(result[i]["days"]){
                                             list.push(result[i]["days"]);
                                        }else{
                                             list.push(result[i]["months"]);
                                        }
                                       
                                    }
                                    // return list;
                                },
                            })
                            return list;
                        })()
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '购买数',
                        type: 'line',
                        data: function() {
                            var list = [];
                            $.ajax({
                                type: "post",
                                async: false, //同步执行
                                url: xAxisUrl,
                                data: {},
                                dataType: "json", //返回数据形式为json
                                success: function(result) {
                                    for (var i in result) {
                                        list.push(result[i]["countBuy"]);
                                    }
                                    // return list;
                                },
                            })
                            return list;
                        }()
                    },
                    {
                        name: '注册数',
                        type: 'line',
                        data: function() {
                            var list = [];
                            $.ajax({
                                type: "post",
                                async: false, //同步执行
                                url: xAxisUrl,
                                data: {},
                                dataType: "json", //返回数据形式为json
                                success: function(result) {
                                    for (var i in result) {

                                        list.push(result[i]["count"]);


                                    }
                                    // return list;
                                },
                            })
                            return list;
                        }()
                    }
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
        }
       
    };




});


define(function(require, exports, module) {

    require('jquery.select2-css');
    require('jquery.select2');

    var searchUrl = "/Widget/User/searchAction";
    /**
     * 搜索用户
     * @param element
     * @param maxNum
     */
    exports.search = function(element,maxNum) {
        var InputLength = maxNum || 1;
        $(element).select2({
            placeholder : "输入用户名搜索",
            minimumInputLength : InputLength,
            separator : ",", // 分隔符
            maximumSelectionSize : 10, // 限制数量
            initSelection : function (element, callback) { // 初始化时设置默认值

            },
            createSearchChoice : function (term, data) { // 创建搜索结果（使用户可以输入匹配值以外的其它值）

            },
            formatSelection : function (item) {
                // 搜索框中的显示
                return item.nickname;
            },
            formatResult : function (item) {
                // 选择列表中的显示
                var str = item.verifiedMobile || item.email;
                return item.nickname+'&nbsp;&nbsp;&nbsp;&nbsp;'+str;
            },
            ajax : {
                url : searchUrl,
                dataType : "json", // 数据类型
                data : function (term, page) { // 请求参数（GET）
                    return {
                        keyword : term
                    };
                },
                results : function (data, page) {
                    return data;
                },
                escapeMarkup : function (m) {
                    return m;
                }
            }
        });
    }


});

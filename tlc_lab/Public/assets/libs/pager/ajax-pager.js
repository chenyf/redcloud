define(function (require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    var jQuery = require('$');

    //options必须包含：totalPage总页数，total总记录数，container存放内容的元素，el页码的最外层元素,url:ajax地址
    //可选包含：maxShowPage，otherData函数
    function ajaxPager(options){
        this.options = {
            maxShowPage:7,
            otherData:function(){
                return {};
            }
        };

        this.curPage = 1;
        this.totalPage = 1;

        this.options = $.extend(this.options,options);

        this.el = $(this.options.el);

        this.totalPage = this.options.totalPage;

        this.initPager();   //初始化页码

        this.bindEvent();   //绑定事件

        return this;
    }

    ajaxPager.prototype.initPager = function(){

        this.wrap = $("<ul></ul>").addClass('pagination ajax-pager').data('maxpage',this.totalPage);
        this.wrap.html("");

        this.wrap.append("<li><a data-page='1' href='javascript:;'>首页</a></li>");

        if(this.curPage <= 1){
            this.wrap.append("<li class='disabled prev-pager'><span>上一页</span></li>");
        }else{
            this.wrap.append("<li class='prev-pager'><a data-page='" + (this.curPage-1) + "' href='javascript:;'>上一页</a></li>");
        }

        var max = Math.min(this.totalPage,this.options.maxShowPage);
        for(var i = 1 ; i <= max ; i++){
            if(i == 1){
                this.wrap.append("<li class='pager-number active'><a data-page='" + i + "' href='javascript:;'>"+i+"</a></li>");
            }else{
                this.wrap.append("<li class='pager-number'><a data-page='" + i + "' href='javascript:;'>"+i+"</a></li>");
            }
        }

        if(this.curPage >= this.totalPage){
            this.wrap.append("<li class='disabled next-pager'><span>下一页</span></li>");
        }else{
            this.wrap.append("<li class='next-pager'><a data-page='" + (this.curPage+1) + "' href='javascript:;'>下一页</a></li>");
        }

        this.wrap.append("<li><a data-page='" + this.totalPage + "' href='javascript:;'>尾页</a></li>");

        var infoHtml = $("<ul class='pagination'></ul>")
                        .append("<li><span class='text-muted'>第"+this.curPage+"/"+this.totalPage+"页</span></li>")
                        .append("<li><span class='text-muted'>共"+this.options.total+"条记录</span></li>");

        this.el.html("").append(this.wrap).append(infoHtml);
    };

    //设置分页属性
    ajaxPager.prototype.setAttr = function(options){
        this.options = $.extend(this.options,options);

        this.totalPage = this.options.totalPage;

        this.initPager();

    };

    //分页的内容已经获取到位后调节
    ajaxPager.prototype.ajustPager = function(){
        var parent = this.el;

        this.curPage = parseInt(this.curPage);

        if(this.curPage <= 1){
            parent.find('.prev-pager').addClass('disabled').html("<span>上一页</span>");
        }else{
            parent.find('.prev-pager').removeClass('disabled').html("<a data-page='" + (this.curPage-1) + "' href='javascript:;'>上一页</a>");
        }

        if(this.curPage >= this.totalPage){
            parent.find('.next-pager').addClass('disabled').html("<span>下一页</span>");
        }else{
            parent.find('.next-pager').removeClass('disabled').html("<a data-page='" + (this.curPage+1) + "' href='javascript:;'>下一页</a>");
        }

        if(this.totalPage > this.options.maxShowPage){

            var frontSpace = this.options.maxShowPage % 2 == 0 ? this.options.maxShowPage / 2 : (this.options.maxShowPage-1) / 2 ;

            var backSpace = this.options.maxShowPage % 2 == 0 ? this.options.maxShowPage / 2 - 1 : (this.options.maxShowPage-1) / 2 ;

            if(this.curPage > frontSpace + 1 && this.curPage < this.totalPage - backSpace){
                var maxShowNumber = this.curPage + backSpace;
                var minShowNumber = this.curPage - frontSpace;
            }

            if(this.curPage <= frontSpace + 1){
                var maxShowNumber = this.options.maxShowPage;
                var minShowNumber = 1;
            }

            if(this.curPage >= this.totalPage - backSpace){
                var maxShowNumber = this.totalPage;
                var minShowNumber = this.totalPage - this.options.maxShowPage + 1;
            }

            var curFirstPage = parent.find("li.pager-number").eq(0).find("a").attr('data-page');
            var gap = minShowNumber - parseInt(curFirstPage);

            if(gap != 0) {
                parent.find("li.pager-number").each(function () {
                    var link = $(this).find('a');
                    var n = parseInt(link.attr('data-page')) + gap;
                    link.attr('data-page',n).text(n);
                });
            }
        }


        this.wrap.find("li").removeClass('active');
        $("a[data-page='"+this.curPage+"']").parent("li.pager-number").addClass('active');

        this.bindEvent();
    };

    ajaxPager.prototype.bindEvent = function(){
        var self = this;
        this.el.on('click','li a',function(){
            if($(this).attr('data-page') != undefined){
                self.fetchData($(this).attr('data-page'));
            }
        });
    };

    ajaxPager.prototype.onClick = function(callback){
        this.pageCallback = callback;
        return this;
    };

    ajaxPager.prototype.fetchData = function(page){
          var self = this;
          var otherData = this.options.otherData(); //传给服务器的其他数据

          var data = $.extend(otherData,{page:page});

          $.post(this.options.url,data,function(result){
              self.pageCallback(JSON.parse(result));
              self.curPage = parseInt(page);
              self.ajustPager();

              //滚动到内容主区域
              $("html,body").animate({scrollTop: $(self.options.container).offset().top - 200}, 600);
          });
    };

    module.exports = ajaxPager;

});
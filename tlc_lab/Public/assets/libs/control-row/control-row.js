/*
* auther : @czq
* describe : 操作行的添加和上下移动。
* date : 2016/03/05
* version : 1.0.0
*/

    ;(function($){
            $.fn.extend({

                    //主方法
                    "control_row" : function(options){
                            /*
                            * @selector box 容器选择
                            * @selector row 行选择
                            * @selector add_buttom 添加按钮
                            * @selector del_buttom 删除按钮
                            * @selector up_buttom 上移按钮
                            * @selector down_buttom 下移按钮
                            * @str add_content 添加的内容
                            */
                            options=$.extend({
                                    box :this,
                                    row:"",
                                    add_buttom      :"",
                                    del_buttom      :"",
                                    up_buttom       :"",
                                    down_buttom     :"",
                                    add_content     :"",
                                    replace         :"",
                                    textarea        :"",
                                    control         :"",
                                    max_row         :"",
                                    del_all         :false,
                                    add_append_func  :"",
                                    del_append_func  :"",
                                    up_append_func   :"",
                                    down_append_func :"",
                                    hide_append_func        :""
                            },options);

                            options.line_num = 0;
                            options.line_num = $(options.box +' '+ options.row).length;

                            this.on("click",options.add_buttom,function(event){
                                    $(options.add_buttom).add_row(options);
                                    event.preventDefault();
                            });
                            this.on("click",options.del_buttom,function(event){				
                                    $(this).del_row(options);					
                                    event.preventDefault();
                            });
                            this.on("click",options.up_buttom,function(event){				
                                    $(this).up_row(options);					
                                    event.preventDefault();
                            });
                            this.on("click",options.down_buttom,function(event){		
                                    $(this).down_row(options);					
                                    event.preventDefault();
                            });
                            this.show_hide(options);
                    },
                    //添加行
                    "add_row" : function(options){
                            var add_content = options.add_content;
                            if(options.replace){
                                add_content = add_content.replace(new RegExp(options.replace,"gm") , options.line_num);
                            }
                            if(options.textarea){
                                var add_content = add_content.replace(new RegExp(options.textarea,"gm") , "textarea");
                            }
                            if( options.max_row && $( options.box + " "+ options.row).length >= options.max_row){
                                return false;
                            }
                            $(options.box).append(add_content);
                            if(options.add_append_func){
                                options.add_append_func(options,add_content);
                            }
                            options.line_num++;
                            this.show_hide(options); 
                    },

                    //删除
                    "del_row" : function(options){
                        var current = $(this).closest(options.row);
                       if(current.siblings(options.row).length == 0 && !options.del_all){
                            return false;
                        }
                        if(options.del_append_func){
                            options.del_append_func(options,current);
                        }
                       current.remove();
                       this.show_hide(options);
                       return false;
                    },

                    //向上移动
                    "up_row" : function(options){
                            var current = $(this).closest(options.row);
                            var prev = current.prev(options.row);
                            if(prev.length == 0){
                                    return false;
                            }
                            if(options.up_append_func){
                                options.up_append_func(options,current);
                            }
                            prev.before(current);
                            options.line_num++;
                            this.show_hide(options);
                            return false;
                    },

                    //向下移动
                    "down_row" : function(options){
                            var current = $(this).closest(options.row);
                            var next = current.next(options.row);
                            if(next.length == 0){
                                    return false;
                            }
                            if(options.down_append_func){
                                options.down_append_func(options,current);
                            }
                            next.after(current);
                            options.line_num++;
                            this.show_hide(options);
                            return false;
                    },
                    "show_hide" : function(options){
                        $(options.box +' '+ options.row +' '+ options.add_buttom).show();
                        $(options.box +' '+ options.row +' '+ options.add_buttom).not(":last").hide();
                        $(options.box +' '+ options.row +' '+ options.up_buttom ).show();
                        $(options.box +' '+ options.row +' '+ options.up_buttom + ":first").hide();
                        $(options.box +' '+ options.row +' '+ options.down_buttom).show();
                        $(options.box +' '+ options.row +' '+ options.down_buttom+":last").hide();
                        if(options.hide_append_func){
                            options.hide_append_func(options);
                        }
                        return false;
                    },
            });
    })(jQuery);

    



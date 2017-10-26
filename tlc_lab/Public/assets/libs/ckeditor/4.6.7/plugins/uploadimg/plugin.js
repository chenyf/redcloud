// 添加一个插件
CKEDITOR.plugins.add('uploadimg', {
  
// 插件初始化
init: function (editor) {
  
    // 添加uploadimg按钮
    editor.ui.addButton('uploadimg', {
        // 鼠标移到按钮提示文字
        label: '图片上传',
        // 命令
        command: 'uploadimg',
        // 图标（相对于插件本身目录下）
        icon: this.path + 'logo_ckeditor.png',
        // 添加点击事件   
        click:function(){
            $('#question-stem-uploader input[type=file]').trigger('click');
            console.log($('#question-stem-uploader input[type=file]').attr('class'));
           
//            $('#sss').trigger('click');
            //$('#question-stem-uploader input[type=file]').click();
        }
    });
}
  
  
});
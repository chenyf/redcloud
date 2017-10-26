
//1. npm install gulp -g   (global环境)
//2. npm install gulp --save-dev (项目环境)
//3. npm install  gulp-uglify  del --save-dev
//4. 编写 gulpfile.js 文件
//5. gulp

var gulp = require('gulp'),
    uglify = require('gulp-uglify');


gulp.task('controller', function() {
  return gulp.src("Public/bundles/web/js/controller/*/*.js")//待压缩的文件
         .pipe(uglify({
		  mangle: false
	  })) //压缩不混淆
	  .pipe(gulp.dest('Public/bundles/web/js/minified/controller')); //压缩后的目录
});

gulp.task('index', function() {
	return gulp.src("Public/bundles/web/js/controller/*.js")
		.pipe(uglify({
			mangle: false
		})) //压缩不混淆
		.pipe(gulp.dest('Public/bundles/web/js/minified/controller'));
});

//默认执行此任务
gulp.task('default', ['controller', 'index']);
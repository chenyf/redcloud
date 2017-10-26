<?php
define('API_URL','http://bishe.cuc.edu.cn/auth/');
return array(
    'api_appid'   => 'IQPR5L'  ,
    'api_secret_key'   => '3db98fdb5d8329327ba130f8c8203e0fef09432e'  ,
    'api_url'   =>  array(
        'auth'  =>  API_URL . 'auth',
        'get_user_info'     =>  API_URL . "getInfo",
        'update_user_info'    =>  API_URL . 'updateInfo',
        'change_user_pwd'    =>  API_URL . 'changeUserPassword',
        'get_teacher_college_list'  =>  API_URL . 'getTeacherColleges',
        'get_student_college_list'  =>  API_URL . 'getStudentColleges',
        'get_department_by_collegeid'  =>  API_URL . 'getDeparmentsByCollegeId',
        'get_major_by_collegeid'  =>  API_URL . 'getMajorsByCollegeId',
        'get_major_by_departmentid'  =>  API_URL . 'getMajorsByXiId'
    )
);
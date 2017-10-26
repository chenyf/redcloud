<?php

    namespace Common\Model\System;
    use \Common\Model\Common\BaseModel;
    use \Common\Lib\WebCode;
        /**
         * 关于我们的模块
         * @author @czq 2016-3-9
         */
    class AboutUsModel extends BaseModel {
        
        protected $tableName = 'about_us';

        /**
         * 清空关于我们并保存新的内容
         */
        public function saveControl()
        {
            $about_us_post = I('post.about_us');
            $this->startTrans();//在第一个模型里启用就可以了，或者第二个也行
            
            $delSesult=$this->where(1)->delete();
            
            foreach( $about_us_post as $key => $value){
                $dataList[] = array(
                    'sequence'=>$value['sequence'],
                    'title'=>  trim($value['title']),
                    'content'=>$value['content'],
                    'webCode'=> C('WEBSITE_CODE')
                );
            }
            
            $dataList = $this -> rowNullDel($dataList,array('sequence','content'));
            
            if(count($dataList) == 0){
                $this->commit();
                return '';
            }
            
             $addResult = $this->addAll($dataList);
            
            if( ( $delSesult || $delSesult ==0 )  && $addResult){

                $this->commit();
                return '';
            }else{
                $this->rollback();
                return 'error';
            }
        }

        public function findIndex($id)
        {   
            $where["id"] = $id;
            $About_us_resource = $this->where($where)->find();
            if(!empty($About_us_resource)){
            $About_us_resource['content'] =  htmlspecialchars_decode($About_us_resource['content']) ;
            }
            return $About_us_resource;
        }

        public function findAllOther()
        {   
            $resource = $this->field(array('id','title'))->order('sequence esc,id esc')->select();
            return $resource;
        }

        public function findAll()
        {   
            $About_us_resource = $this->order('sequence desc,id asc')->select();
            
            foreach($About_us_resource as $k=>$v){
                $About_us_resource[$k]['content'] =  htmlspecialchars_decode($v['content']) ;
            }
            return $About_us_resource;
        }
        
        public function childrenUnset($resource,$array){
            foreach($resource as $key => $value){
                if(in_array($key, $array)){
                    unset($resource[$key]);
                }
            }
            return $resource;
        }
        
        public function childrenTrim($resource,$array){
            foreach($resource as $key => $value){
                if( is_array($value)){
                    $resource[$key] = $this -> childrenTrim($value,$array);
                }else{
                    if(in_array($key, $array)){
                        $resource[$key] = trim($value);
                    }
                }
            }
            return $resource;
        }
        public function rowNullDel($data,$exclude=array()){
            foreach($data as $key => $value){
                $check = 0;
                foreach($value as $k => $v){
                    if( ($v || $v ===0  || $v ==="0")&& !in_array($k,$exclude) ){
                       $check+=1; 
                    }
                }
                if(!$check){
                    unset($data[$key]);
                }
            }
            return $data;
        }
    }
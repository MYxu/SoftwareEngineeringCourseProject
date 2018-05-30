<?php

/**
 * Created by PhpStorm.
 * User: MYXuu
 * Date: 2018/5/31
 * Time: 00:22
 */

namespace Common\Controller;
use Think\Controller;
class BaseController extends Controller
{

    function __construct()
    {
        parent::__construct();
    }


    /**
     * Auth : MYXuu
     * Description :需要post的字段
     *
     * @param array|null $require_date
     * @param array|null $unnecessary_data
     * @return array
     */
   protected function reqPost(array $require_date=null,array $unnecessary_data=null)
    {
        if(!IS_POST)
            $this->ajaxReturn(json_error_request());

        $data = array();
        if($require_date)                                    //必须提交字段
        {
            foreach($require_date as $key => $value)
            {
                $field = is_int($key)? $value : $key;
                $_v    = I('post.'.$field,null);//过滤xss攻击
                if(is_null($_v))
                    $this->ajaxReturn(
                        json_error("缺少字段: " . $field)
                    );

                if(I('post.'.$field) == '')
                    $this->ajaxReturn(
                        json_error("字段".$field."的值不能为空！")
                    );
                $data[$field] = $_v;
            }
        }

        if($unnecessary_data)                               //非必须提交字段
        {
            foreach($unnecessary_data as $key => $value)
            {
                $field = is_int($key) ? $value : $key;
                $_v    = I('post.'.$field, null);

                if(!is_null($_v))
                    $data[$field] = $_v;                    //存在post该字段则加入
            }
        }
        return $data;
    }


    /**
     * Auth : MYXuu
     * Description : 需要GET的字段
     *
     * @param array|null $require_date
     * @param array|null $unnecessary_data
     * @return array
     */
    protected function reqGet(array $require_date=null,array $unnecessary_data=null)
    {
        if(!IS_GET)
            $this->ajaxReturn(json_error_request());

        $data = array();
        if($require_date)                                    //必须提交字段
        {
            foreach($require_date as $key => $value)
            {
                $field = is_int($key)? $value : $key;
                $_v    = I('get.'.$field,null);//过滤xss攻击
                if(is_null($_v))
                    $this->ajaxReturn(
                        json_error("缺少字段: " . $field)
                    );

                if(I('get.'.$field) == '')
                    $this->ajaxReturn(
                        json_error("字段".$field."的值不能为空！")
                    );
                $data[$field] = $_v;
            }
        }

        if($unnecessary_data)                               //非必须提交字段
        {
            foreach($unnecessary_data as $key => $value)
            {
                $field = is_int($key) ? $value : $key;
                $_v    = I('get.'.$field, null);

                if(!is_null($_v))
                    $data[$field] = $_v;                    //存在get该字段则加入
            }
        }
        return $data;
    }

}
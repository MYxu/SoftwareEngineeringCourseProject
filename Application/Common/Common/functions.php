<?php
/**
 * Created by PhpStorm.
 * User: MYXuu
 * Date: 2018/5/30
 * Time: 23:59
 */

/**
 * Auth : MYXuu
 * Description : 断点调试
 *
 * @param $param
 */
function dd($param)
{
    echo "<pre>";
    var_dump($param);
    echo "</pre>";
    exit(0);
}

/**
 * Auth : MYXuu
 * Description : 密码加密
 *
 * @param $password
 * @return string
 */
function encrypt_password($password)
{
    return md5(sha1($password));
}

/**
 * Auth : MYXuu
 * Description : 封装处理成功的json格式,组装处理成功返回的数据、提示信息、状态码
 *
 * @param string $msg
 * @param array|null $data
 * @param int $statusCode
 * @return array
 */
function json_success($msg = null, $data = null, $statusCode = 200)
{
    if (!$msg) $msg = "操作成功";

    return array(
        'statusCode' => $statusCode,
        'successMsg' => $msg,
        'data' => $data
    );

}


/**
 * Auth : MYXuu
 * Description : 封装处理失败的json格式,组装处理失败返回的数据、提示信息、状态码
 *
 * @param string $msg
 * @param array|null $data
 * @param int $statusCode
 * @return array
 */
function json_error($msg = null, $data = null, $statusCode = 400)
{
    if (!$msg) $msg = "操作失败";

    return array(
        'statusCode' => $statusCode,
        'errorMsg' => $msg,
        'detail' => $data
    );
}


/**
 * Auth : MYXuu
 * Description : 封装请求方式错误的json格式,组装请求方式错误的提示信息、状态码
 *
 * @param string $msg
 * @param int $statusCode
 * @return array
 */
function json_error_request($msg = null, $statusCode = 401)
{
    if (!$msg) $msg = "请求方法错误";

    return array(
        'statusCode' => $statusCode,
        'errorRequestMsg' => $msg
    );
}

/**
 * Auth : MYXuu
 * Description : 删除资源(文件、图片等)
 *
 * @param $data
 */
function deleteResources($data)
{
    if (is_array($data))
    {
        foreach ($data as $value)
        {
            $dir = ROOT_PATH . $value; //资源路径,ROOT_PATH-->项目根目录

            if (file_exists($dir)) unlink($dir);
        }

    } else {
        $dir = ROOT_PATH . $data;

        if (file_exists($dir)) unlink($dir);
    }
}


/**
 * Auth : MYXuu
 * Description :数组keys的值充当数组data的键值,获取数组data中的指定键值组合
 *
 * @param array $data
 * @param array $keys
 * @return array
 */
function getDataInArray(array $data, array $keys)
{
    $result = array();
    foreach ($keys as $key) {
        if (isset($data[$key]))
            $result[$key] = $data[$key];
    }

    return $result;
}


/**
 * Auth : MYXuu
 * Description : 资源上传
 *
 * @param $fileName
 * @param $type
 * @param bool $multiple
 * @return array
 */
function uploadPicture($fileName, $type, $multiple = false)
{
    $upload = new \Think\Upload();                        // 实例化上传类
    $upload->rootPath = './Public/uploadResources';       // 上传资源根目录
    $upload->replace = true;                             // 覆盖同名文件
    $upload->autoSub = false;                            // 不自动子目录保存文件
    $upload->hash = false;                            // 不生成hash编码,提速

    switch ($type) {                                       //定制配置

        case 'template':
            $upload->exts = array('jpg', 'png', 'jpeg');     //允许上传的资源类型
            $upload->maxSize = 2097152;                       //2M
            $upload->saveName = array('uniqid', $fileName . "-"); //对上传的资源进行命名
            $upload->savePath = "";                            //上传资源存放路径
            break;

        default :
            echo '$type错误！若需要,请自行扩展！';
            exit;
    }

    $count = 0;                                                //统计上传资源数

    foreach ($_FILES as &$file) //资源若无后缀名则强制装换成jpg格式
    {
        if (is_array($file['name'])) {
            foreach ($file['name'] as &$name) {
                ++$count;
                if (pathinfo($name, PATHINFO_EXTENSION) == '') $name .= '.jpg';
            }

        } else {
            ++$count;
            if (pathinfo($file['name'], PATHINFO_EXTENSION) == '')
                $file['name'] .= '.jpg';
        }
    }


    $info = $upload->upload();       //上传资源操作,失败返回false、成功则返回数组

    if (!$info)
        return json_error("上传失败", $upload->getError()); //上传失败返回失败信息

    if ($multiple) {                                              //多图上传成功
        $resultData = array();
        foreach ($info as $value)                                 //拼接资源url
        {
            $resultData[] = array(
                'key' => $value['key'],
                'url' => "Public/resources/" . $value['savepath']
                    . $value['savename']
            );
        }
        return json_success(
            "上传成功", array(
                'success_count' => count($info),          //上传成功资源数
                'error_count' => $count - count($info),   //上传失败资源数
                'success_array' => $resultData,           //上传成功资源信息
                'error_msg' => $upload->getError()        //获取最后一次上传失败信息
            )
        );
    } else {
        reset($info);
        $fileInfo = current($info);
        $fileInfo['url'] = 'Public/upload/' . $fileInfo['savepath']
            . $fileInfo['savename'];

        return json_success("上传成功", $fileInfo);
    }
}

/**
 * Auth : MYXuu
 * Description : 253云通讯短信服务发送短信验证码
 *
 * @param $phone
 * @param null $msg
 * @return array
 */
function sendVerifyCode($phone, $msg = null)
{
    $RemindMsg = array(
        '101' => '无此用户',
        '102' => '密码错',
        '103' => '提交过快',
        '104' => '系统忙',
        '105' => '敏感短信',
        '106' => '消息长度错',
        '107' => '错误的手机号码',
        '108' => '手机号码个数错',
        '109' => '无发送额度',
        '110' => '不在发送时间内',
        '111' => '超出该账户当月发送额度限制',
        '112' => '无此产品',
        '113' => 'extno格式错',
        '115' => '自动审核驳回',
        '116' => '签名不合法，未带签名',
        '117' => 'IP地址认证错',
        '118' => '用户没有相应的发送权限',
        '119' => '用户已过期',
        '120' => '内容不是白名单',
    );

    $verifyCode = rand(111111, 999999);// 生成6位随机验证码

    if ($msg == null) // 默认短信模板
        $msg = "【国雅】:你的验证码是" . $verifyCode . ",五分钟内有效,请不要告诉他人";

    //设置请求参数
    $postFields = http_build_query(
        array(
            'account' => 'yanqiuping_hp',  // 账号
            'pswd' => 'Hp888888',       // 密码
            'msg' => $msg,             // 短信内容
            'mobile' => $phone,           // 手机号
            'needstatus' => true,             // 需要返回状态报告
        )
    );

    $url = 'https://sapi.253.com/msg/HttpBatchSendSM'; // 创蓝发送短信接口

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    $sendResult = curl_exec($ch);
    curl_close($ch);

    $sendResult = preg_split("/[,\r\n]/", $sendResult); // 解析返回的状态

    if ($sendResult[1] == 0) {  // 验证短信发送成功
        session_start();
        session("sendTime", NOW_TIME);       // 设置验证码生效初始时间
        session("verifyCode", $verifyCode);  // 将验证码保存到session中
        return json_success("发送成功,请注意查收", $verifyCode);
    }
    return json_error("发送失败", $RemindMsg[$sendResult['1']]);
}

/**
 * Auth : MYXuu
 * Description : 验证验证码
 *
 * @param $verifyCode
 * @return array
 */
function checkVerifyCode($verifyCode)
{
    if (session("verifyCode") == $verifyCode) {
        if (NOW_TIME - session("sendTime") <= 30000)
            $checkRes = json_success("验证成功");
        else
            $checkRes = json_error("验证码已失效");

        session("sendTime", null);        // 验证成功、验证码失效后删除当前session
        session("verifyCode", null);
        return $checkRes;
    }

    if (NOW_TIME - session("sendTime") > 30000)
    {
        session("sendTime", null);// 验证码不匹配且超出验证码有效时间则删除当前session
        session("verifyCode", null);
    }
    return json_error("验证码错误");
}

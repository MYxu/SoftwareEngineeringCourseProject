<?php
/**
 * Created by PhpStorm.
 * User: MYXuu
 * Date: 2018/5/31
 * Time: 00:24
 */
namespace Common\Model;
class CURDModel extends BaseModel
{
    //资源存放路径的数据库表字段,添加、更新、删除资源的时候获取其字段值,进而处理资源
    protected $resourceFields = array();


    /**
     * Auth : MYXuu
     * Description : 添加一条数据
     *
     * @param array $data
     * @return array
     */
    public function addOneData(array $data) {

        if($this->create($data))
        {
            $result = $this->add();
            if($result)
                return json_success("",array('id' => $result));  //返回受影响行的id
        }

        //添加失败,若存在资源则删除
        if($this->resourceFields)
            deleteResources(getDataInArray($data,$this->resourceFields));

        $status = $this->getError();               //获取模型错误
        return json_error("添加失败",$status ? $status : "系统错误");
    }

    /**
     * Auth : MYXuu
     * Description : 添加多条数据
     *
     * @param array $data
     * @return array
     */
    public function addMulData(array $data) {

        $this->patchValidate = true;               //开启批处理
        if($this->create($data)) {
            if($result = $this->addAll($data))
                return json_success("",array('id' =>(int)$result));//返回受影响行的第一个id
        }

        //添加失败,存在上传资源则删除
        if($this->resourceFields) {
            foreach($data as $value)
                deleteResources(getDataInArray($value,$this->resourceFields));
        }
        $status  = $this->getError();
        return json_error("添加失败",$status ? $status : "系统错误");
    }


    /**
     * Auth : MYXuu
     * Description : 通过主键id修改指定记录的数据
     *
     * @param $id
     * @param array $data
     * @param string $key
     * @return array
     */
    public function updateData($id, array $data, $key = 'id') {

        //资源字段存在更新
        if($this->resourceFields)
            $newResources = getDataInArray($data,$this->resourceFields);

        //对需要更新的资源字段进行备份
        if(isset($newResources)&&$newResources)
            $oldResource = $this->field(array_keys($newResources))
                ->where($key.'=%d',$id)
                ->find();

        //更新数据
        if($this->create($data)) {
            $result = $this->where($key.'=%d',$id)->save();
            if($result !== false) {
                if ($result) {
                    if (isset($oldResource)) deleteResources($oldResource);//删除旧资源
                    return json_success();
                }
                return json_error("更新失败");
            }

        }

        //更新失败删除上传的资源
        if(isset($newResources)) deleteResources($newResources);

        $status = $this->getError();
        return json_error("更新失败",$status ? $status : "系统错误");
    }


    /**
     * Auth : MYXuu
     * Description : 通过主键id删除指定记录集
     *
     * @param $id
     * @param string $key
     * @return array
     */
    public function destroyById($id, $key = 'id' )
    {
        //存在资源则先备份
        if($this->resourceFields) {
            $resources = $this->field($this->resourceFields)
                ->where($key.'= %d',$id)
                ->select();
        }

        $result = $this->where($key.'=%d',$id)->delete();


        if($result) {
            if(isset($resources)) {
                foreach($resources as $value)
                    deleteResources($value);
            }
            return json_success("删除成功");
        }

        $status = $this->getError();
        return json_error("删除失败",$status ? $status : "系统错误");
    }


    /**
     * Auth : MYXuu
     * Description : 根据条件删除指定记录集
     *
     * @param $where
     * @return array
     */
    public function destroyByWhere($where)
    {
        //存在资源则备份
        if($this->resourceFields)
            $resources = $this->field($this->resourceFields)
                ->where($where)
                ->select();

        $result = $this->where($where)->delete();

        if($result) {
            if(isset($resources)) {                          //删除记录成功则删除资源
                foreach($resources as $value)
                    deleteResources($value);
            }
            return json_success("删除成功");
        }

        $status = $this->getError();
        return json_error("删除失败",$status ? $status : "系统错误");
    }

    /**
     * Auth : MYXuu
     * Description : 通过指定条件获取一条或者多条指定字段的记录集
     *
     * @param array|null $where
     * @param array|null $fields
     * @param bool $is_multi
     * @return mixed
     */
    public function getData(array $where = null,array $fields = null ,$is_multi = false)
    {
        if($fields) $this->field($fields);

        if($where)  $this->where($where);

        if($is_multi)
            $result = $this->select();//多条数据
        else
            $result = $this->find();//一条数据

        if($result === false) {
            $status = $this->getError();
            return json_error("获取数据失败",$status ? $status : "系统错误");

        }elseif(empty($result)) return json_error("记录不存在");

        return json_success("获取数据成功",$result);
    }
}
<?php

namespace app\common\model;

use think\Model;

/**
 * 区域模型类
 * Class Region
 * @package app\common\model
 */
class Region extends Base
{
    /**
     * 获取区域列表数据
     * @param array $map
     * @return \think\Paginator
     */
    public function getRegion($map = [])
    {
        if(!isset($map['level'])) { // 区域级别
            $map['level'] = 1;
        }

        $result = $this->where($map)->cache(true, 10)->select();
        return $result;
    }

    /**
     * 获取供应商区域列表数据
     * @param array $map
     * @return \think\Paginator
     */
    public function getzone($map,$listcompanyvalue)
    {
            $data=array();
            foreach ($listcompanyvalue as $value) {

                   $map['region_id']=$value;
                   $result = $this->where($map)->find();

                   if(!empty($result)){
                     array_push($data, $result);
                   }

            }           
            return $data;
    }

    /**
     * 获取上级id（包含当前id）
     * @param $id
     * @return array
     */
    public function getParentId($id)
    {
        $data_list = $this->select();
        return $this->_getParentId($data_list, $id, true);
    }

    /**
     * 递归获取上级id（包含当前id，私有方法）
     * @param $data
     * @param $id
     * @param bool $clear 清空静态数组标识
     * @return array
     */
    private function _getParentId($data, $id, $clear = false)
    {
        // 定义静态数组
        static $arr = array();
        // 清空静态数组
        if ($clear) {
            $arr = array();
        }

        foreach ($data as $key => $value) {
            if ($value['region_id'] == $id) {
                // 当前 id 即 $value['id'] 数据放入静态数组中
                $arr[] = $value['region_id'];

                // 递归：找出当前元素的父元素（当前Auth规则的pid即为父元素的id）
                $this->_getParentId($data, $value['parent_id'], false);
            }
        }

        // 排序
        asort($arr);

        // 返回存放id和上级id的数组
        return $arr;
    }
}

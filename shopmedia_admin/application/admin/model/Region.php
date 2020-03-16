<?php

namespace app\admin\model;

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


}

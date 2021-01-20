<?php

namespace app\common\model;

use think\Model;

/**
 * 分公司信息模型类
 * Class Company
 * @package app\admin\model
 */
class Company extends Base
{
    /**
     * 获取分公司列表数据
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getCompany($map = [], $size = 5)
    {
        if(!isset($map['c.is_delete'])) { // 是否删除
            $map['c.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['c.create_time' => 'desc'];

        $result = $this->alias('c')
            ->field($this->_getListField())
            ->join('__REGION__ rp', 'c.province_id = rp.region_id', 'LEFT') // 省份
            ->join('__REGION__ rc', 'c.city_id = rc.region_id', 'LEFT') // 城市
            ->where($map)->order($order)->cache(true, 10)->paginate($size);

        return $result;
    }

    /**
     * 通用化获取参数的数据字段
     * @return array
     */
    private function _getListField()
    {
        return [
            'c.company_id',
            'c.company_name',
            'c.province_id',
            'c.city_id',
            'c.person_name',
            'c.phone',
            'c.status',
            'c.create_time',
            'c.is_delete',
            'rp.region_name province',
            'rc.region_name city'
        ];
    }

    /**
     * 创建供应商
     * @param $data
     * @return mixed
     */
    public function inCompany($data)
    {
        $data['status']=1; //正常
        $data['create_time']=date('Y-m-d H:i:s');
        $list=$this->save($data);
        return $this->id;
    }

    /**
     * 插入供应商销售区域值信息
     * @param $data
     * @return $this
     */
    public function insertcompany($data)
    {
        //入库供应商基本信息表
        $list=$this->update($data);
        return $list;
    }

    /**
     * 获取供应商销售区域字段
     * @param $data
     * @return mixed
     */
    public function salearea($data)
    {
        //入库供应商基本信息表
        $list=$this->where($data)->value('salearea');
        return $list;
    }
}

<?php

namespace app\common\model;

use think\Model;

/**
 * 供应商信息模型类
 * Class Company
 * @package app\admin\model
 */
class Company extends Base
{
    /**
     * 表前缀
     */
    protected $connection = ['prefix' => 'goodshop_'];

    /**
     * 创建供应商
     * @param $data
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
     */
    public function salearea($data)
    {
        //入库供应商基本信息表

        $list=$this->where($data)->value('salearea');
        return $list;

    }


}

<?php

namespace app\common\model;

use think\Model;

/**
 * 基础模型类库
 * Class Base
 * @package app\common\model
 */
class Base extends Model
{
    protected $autoWriteTimestamp = true;
    protected $field = true; // 忽略数据表不存在的字段，如排出 $autoWriteTimestamp = true 时 update_time 字段对更新数据的干扰

    /**
     * 新增
     * @param $data
     * @param string $id 返回主键id
     * @return mixed
     */
    public function add($data, $id = 'id')
    {
        if(!is_array($data)){
            exception('传递数据不合法');
        }
        $this->allowField(true)->save($data); // allowField(true) 过滤非数据表字段的数据

        return $this->$id;
    }

    /**
     * 软删除指定资源（自定义软删除，不同于tp5软删除traits\model\SoftDelete）
     * @param $id_key
     * @param $id_value
     * @return false|int
     */
    public function softDelete($id_key, $id_value)
    {
        return $this->save(['is_delete' => config('code.is_delete')], [$id_key => $id_value]);
    }

    /**
     * 获取最大值
     * @param array $map
     * @param string $field
     * @return mixed
     */
    public function getMax($map = [], $field = 'id')
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }
        $max = $this->where($map)->max($field);
        return $max;
    }

    /**
     * 获取最小值
     * @param array $map
     * @param string $field
     * @return mixed
     */
    public function getMin($map = [], $field = 'id')
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }
        $max = $this->where($map)->min($field);
        return $max;
    }

    /**
     * 生成唯一订单编号 order_sn
     * @return string
     * @throws \think\Exception
     */
    public function getOrderSn()
    {
        // 保证不会有重复订单号存在
        while(true){
            $order_sn = date('YmdHis').rand(1000, 9999); // 订单编号
            $order_sn_count = $this->where("order_sn = '$order_sn'")->count();
            if($order_sn_count == 0)
                break;
        }
        return $order_sn;
    }
}

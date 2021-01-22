<?php

namespace app\common\model;

use think\Model;

/**
 * 商品类别模型类
 * Class TgGoodsType
 * @package app\common\model
 */
class TgGoodsType extends Base
{
    /**
     * 获取商品类别列表数据
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getGoodsType($map = [], $size = 5)
    {
        $result = $this->alias('gc')
            ->field('gc.*, pgc.type_name parent_name, pgc.parent_id grandparent_id')
            ->join('__TG_GOODS_TYPE__ pgc', 'gc.parent_id = pgc.id', 'LEFT') // 上级
            ->where($map)->cache(true, 10)->paginate($size);
        return $result;
    }

    /**
     * 获取商品类别列表树
     * @param array $map
     * @return mixed
     */
    public function getGoodsTypeTree($map = [])
    {
        $result = $this->where($map)->cache(true, 10)->select();
        return $this->sort($result);
    }



    /**
     * 无限级分类递归排序
     * @param $data
     * @param int $pid
     * @param int $level
     * @return array
     */
    public function sort($data, $pid = 0, $level = 0)
    {
        // 定义静态数组
        static $arr = array();

        foreach ($data as $key => $value) {
            // 找出第一个顶级元素（文章类别）
            if ($value['parent_id'] == $pid) {
                // 定义level字段（该字段可以不写入数据表中）
                $value['level'] = $level;

                // 定义所有上级id（包含当前id）
                $value['parent_ids'] = $this->getParentId($value['id']);

                // 定义所有下级id（不包含当前id）
                $value['child_ids'] = $this->getChildId($value['id']);

                // 数据放入静态数组中
                $arr[] = $value;

                // 递归：找出当前元素的子元素（当前文章类别的id即为子元素的parent_id）
                $this->sort($data, $value['id'], $level + 1);
            }
        }

        return $arr;
    }

    /**
     * 获取下级id（不包含当前id）
     * @param $id
     * @return array
     */
    public function getChildId($id)
    {
        $data_list = $this->select();
        return $this->_getChildId($data_list, $id, true);
    }

    /**
     * 递归获取下级id（不包含当前id）
     * @param $data
     * @param $id
     * @param bool $clear 清空静态数组标识
     * @return array
     */
    private function _getChildId($data, $id, $clear = false)
    {
        // 定义静态数组
        static $arr = array();
        // 清空静态数组
        if ($clear) {
            $arr = array();
        }

        foreach ($data as $key => $value) {
            if ($value['parent_id'] == $id) {
                // 当前 id 即 $value['id'] 数据放入静态数组中
                $arr[] = $value['id'];

                // 递归：找出当前元素的子元素（当前文章类别的id即为子元素的parent_id）
                $this->_getChildId($data, $value['id'], false);
            }
        }

        // 排序
        asort($arr);

        // 返回存放下级id的数组
        return $arr;
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
     * 递归获取上级id（包含当前id）
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
            if ($value['id'] == $id) {
                // 当前 id 即 $value['id'] 数据放入静态数组中
                $arr[] = $value['id'];

                // 递归：找出当前元素的父元素（当前文章类别的parent_id即为父元素的id）
                $this->_getParentId($data, $value['parent_id'], false);
            }
        }

        // 排序
        asort($arr);

        // 返回存放id和上级id的数组
        return $arr;
    }
}

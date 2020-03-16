<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

/**
 * admin模块区域管理控制器类
 * Class Region
 * @package app\admin\controller
 */
class Region extends Base
{
    /**
     * 显示区域资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            //$query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['region_name'])) { // 区域名称
                $map['region_name'] = ['like', '%' . trim($param['region_name']) . '%'];
            }
            if (isset($param['level'])) { // 区域级别
                $map['level'] = intval($param['level']);
            }
            if (isset($param['parent_id'])) { // 上级ID
                $map['parent_id'] = intval($param['parent_id']);
            }

            // 获取区域列表数据
            try {
                $data = model('Region')->getRegion($map);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }

            return show(config('code.success'), 'OK', $data);
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 保存新建的区域资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            $data = input('post.');

            // validate验证数据合法性
            $validate = validate('Region');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 入库操作
            try {
                $id = model('Region')->add($data, 'region_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['region_id' => $id], 201);
            } else {
                return show(config('code.error'), '新增失败', [], 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 显示指定的区域资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Region')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }

            if ($data) {
                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // 获取更新成功前的广告图片ad_pic
            $ad = model('Ad')->field('ad_pic')->find($id);

            // 判断数据是否存在
            $data = [];
            if (!empty($param['ad_name'])) { // 广告名称
                $data['ad_name'] = trim($param['ad_name']);
            }
            if (isset($param['position_id'])) { // 广告位置ID
                $data['position_id'] = input('param.position_id', null, 'intval');
            }
            if (!empty($param['start_time'])) { // 开始时间
                $data['start_time'] = strtotime($param['start_time']);
            }
            if (!empty($param['end_time'])) { // 结束时间
                if (!empty($param['start_time']) && $param['start_time'] > $param['end_time']) {
                    return show(0, '广告结束时间不能小于开始时间', [], 403);
                }

                $data['end_time'] = strtotime($param['end_time']);
            }
            if (!empty($param['ad_link'])) { // 广告链接
                $data['ad_link'] = trim($param['ad_link']);
            }
            if (!empty($param['ad_pic'])) { // 广告图片
                $data['ad_pic'] = trim($param['ad_pic']);
            }
            if (!empty($param['bgcolor'])) { // 背景颜色
                $data['bgcolor'] = $param['bgcolor'];
            }
            if (isset($param['target'])) { // 新窗口
                $data['target'] = input('param.target', null, 'intval');
            }
            if (isset($param['is_show'])) { // 是否显示
                $data['is_show'] = input('param.is_show', null, 'intval');
            }
            if (isset($param['sort'])) { // 排序
                $data['sort'] = input('param.sort', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('Ad')->save($data, ['ad_id' => $id]); // 更新
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', [], 403);
            } else {

                // 当为更新target或is_show时，直接刷新当前页面
                if ((array_key_exists('target', $param) || array_key_exists('is_show', $param)) && count($param) == 2) { // 传入2个参数，其中一个是 target或is_show
                    return $this->redirect('ad/index');
                }

                // 删除更新成功前的广告图片ad_pic文件
                if (!empty($param['ad_pic']) && trim($param['ad_pic']) != $ad['ad_pic']) {
                    // 删除文件
                    @unlink(ROOT_PATH . 'public' . DS . $ad['ad_pic']);
                }

                return show(config('code.success'), '更新成功', ['url' => 'parent'], 201);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 删除指定区域资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 获取指定的区域
            try {
                $data = model('Region')->find($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }

            // 判断数据是否存在
            if ($data['region_id'] != $id) {
                return show(config('code.error'), '数据不存在');
            }

            // 判断删除条件：判断是否存在下级区域
            $regionList = model('Region')->where(['parent_id' => $id])->select();
            if (!empty($regionList)) {
                return show(config('code.error'), '删除失败：存在下级区域', [], 403);
            }

            // 真删除
            try { // 捕获异常
                $result = model('Region')->destroy($id);
            } catch (\Exception $e) {
                return show(config('code.error'), '网络忙，请重试', [], 500); // $e->getMessage()
            }
            if (!$result) {
                return show(config('code.error'), '删除失败', [], 403);
            } else {
                return show(config('code.success'), '删除成功', []);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}

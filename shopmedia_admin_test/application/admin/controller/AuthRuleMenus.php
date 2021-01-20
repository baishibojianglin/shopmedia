<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2020/2/28
 * Time: 18:22
 */

namespace app\admin\controller;

use think\auth\Auth;
use think\Db;
use think\Session;

/**
 * admin模块Auth权限认证规则菜单控制器类
 * Class AuthRuleMenus
 * @package app\admin\controller
 */
class AuthRuleMenus extends Base
{
    /**
     * 权限规则菜单
     */
    public function authRuleMenus()
    {
        $menus = $this->getAuthRuleMenus($this->adminUser->id);
        if ($menus) {
            foreach($menus as $key => $value) {
                $menus[$key] = json_decode($value, true);
            }
            return show(config('code.success'), 'OK', $menus);
        } else {
            return show(config('code.error'), 'Not Found', '', 404);
        }
    }

    /**
     * 获得权限规则菜单（参考： /vendor/5ini99/think-auth/src/Auth.php 类 getAuthList($uid, $type) 方法）
     * @param $uid
     * @param int $type
     * @return array|mixed
     */
    protected function getAuthRuleMenus($uid, $type =2)
    {
        $auth = new Auth(); // 实例化Auth权限认证类

        static $_authList = []; //保存用户验证通过的权限列表
        $t = implode(',', (array)$type);
        if (isset($_authList[$uid . $t])) {
            return $_authList[$uid . $t];
        }
        if (2 == $type && Session::has('_auth_list_' . $uid . $t, config('admin.session_admin_auth_rule_scope'))) {
            return Session::get('_auth_list_' . $uid . $t, config('admin.session_admin_auth_rule_scope'));
        }
        //读取用户所属用户组
        $groups = $auth->getGroups($uid);
        $ids = []; //保存用户所属用户组设置的所有权限规则id
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid . $t] = [];
            return [];
        }

        $map = array(
            //'id' => ['in', $ids],
            'type' => $type,
            'status' => 1,
        );
        // 判断是否为超级管理员：不是超级管理员时查询用户组对应的规则，否则查询所有规则
        if (!in_array($uid, $this->getSuperAdmin())) {
            $map['id'] = ['in', $ids];
        }

        //读取用户组所有权限规则
        $rules = Db::name('auth_rule')->where($map)->field('condition,name,title,id,pid,level,icon')->order('sort')->select();
        //循环规则，判断结果。
        $authList = []; //
        foreach ($rules as $rule) {
            if (!empty($rule['condition'])) {
                //根据condition进行验证
                $user = $this->getUserInfo($uid); //获取用户信息,一维数组
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                //dump($command); //debug
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $authList[] = json_encode([
                        'id' => $rule['id'],
                        'name' => $rule['name'], //strtolower($rule['name'])
                        'title' => $rule['title'],
                        'pid' => $rule['pid'],
                        'level' => $rule['level'],
                        'icon' => $rule['icon'],
                    ]);
                }
            } else {
                //只要存在就记录
                $authList[] = json_encode([
                    'id' => $rule['id'],
                    'name' => $rule['name'], //strtolower($rule['name'])
                    'title' => $rule['title'],
                    'pid' => $rule['pid'],
                    'level' => $rule['level'],
                    'icon' => $rule['icon'],
                ]);
            }
        }
        $_authList[$uid . $t] = $authList;
        if (2 == $type) {
            //规则列表结果保存到session
            Session::set('_auth_list_' . $uid . $t, $authList, config('admin.session_admin_auth_rule_scope'));
        }

        return array_unique($authList);
    }

    /**
     * 获得用户资料,根据自己的情况读取数据库（参考： /vendor/5ini99/think-auth/src/Auth.php 类 getUserInfo($uid) 方法）
     * @param $uid
     * @return mixed
     */
    protected function getUserInfo($uid)
    {
        static $userinfo = [];

        $user = Db::name('admin_user');
        // 获取用户表主键
        $_pk = is_string($user->getPk()) ? $user->getPk() : 'uid';
        if (!isset($userinfo[$uid])) {
            $userinfo[$uid] = $user->where($_pk, $uid)->find();
        }

        return $userinfo[$uid];
    }
}
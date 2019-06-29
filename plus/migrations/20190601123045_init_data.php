<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Init_data extends CI_Migration {
    public function up() {
        $this->init_user();
        $this->init_module();
        $this->init_api();
    }

    private function init_user() {
        $CI =& get_instance();
        $CI->load->model("user_model");
        $id = $CI->user_model->add('admin', 'admin@cprap.com', null, '21232f297a57a5a743894a0e4a801fc3');

        $this->db->insert(CIPLUS_DB_PREFIX . 'user_info',
            array(
                'id' => $id,
                'name' => 'administrator',
            ));

        $this->db->insert(CIPLUS_DB_PREFIX . 'role',
            array(
                'key' => 'admin',
                'name' => '超级管理员',
                'description' => '系统超级管理员',
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'role',
            array(
                'key' => 'manager',
                'name' => '管理员',
                'description' => '系统管理员',
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'role_user',
            array(
                'role_key' => 'admin',
                'user_id' => $id,
            ));
    }

    private function init_module() {
        $this->db->insert(CIPLUS_DB_PREFIX . 'module',
            array(
                'key' => 'system',
                'name' => '系统模块',
                'readonly' => 1,
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'module',
            array(
                'key' => 'profile',
                'name' => '个人信息',
                'readonly' => 1,
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'module',
            array(
                'key' => 'setting',
                'name' => '设置模块',
                'readonly' => 1,
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'module',
            array(
                'key' => 'resource',
                'name' => '资源管理',
                'readonly' => 1,
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'module',
            array(
                'key' => 'role',
                'name' => '角色权限',
                'readonly' => 1,
            ));
    }

    private function init_api() {
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'login',
                'title' => '用户登录',
                'module' => 'system',
                'path' => 'passport/login',
                'required' => json_encode(array("password")),
                'optional' => json_encode(array("passport", "account", "email", "phone", "header")),
                'method' => 'request',
                'validated' => 0,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'info',
                'title' => '登录凭证',
                'module' => 'system',
                'path' => 'passport/info',
                'required' => json_encode(array("token")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 0,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'reg',
                'title' => '用户注册',
                'module' => 'system',
                'path' => 'passport/reg',
                'required' => json_encode(array("password")),
                'optional' => json_encode(array("account", "email", "phone", "repassword")),
                'method' => 'request',
                'validated' => 0,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'refresh',
                'title' => '更新会话',
                'module' => 'system',
                'path' => 'passport/refresh',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'logout',
                'title' => '注销登录',
                'module' => 'system',
                'path' => 'passport/logout',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'edit_info',
                'title' => '修改个人信息',
                'module' => 'profile',
                'path' => 'profile/edit_info',
                'required' => json_encode(array()),
                'optional' => json_encode(array('name', 'avatar', 'sex', 'area', 'city', 'province', 'country', 'introduction')),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'change_password',
                'title' => '修改密码',
                'module' => 'profile',
                'path' => 'profile/change_password',
                'required' => json_encode(array('old_password', 'new_password', 're_password')),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'change_phone',
                'title' => '修改手机',
                'module' => 'profile',
                'path' => 'profile/change_phone',
                'required' => json_encode(array('phone', 'captcha')),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'change_email',
                'title' => '修改邮箱',
                'module' => 'profile',
                'path' => 'profile/change_email',
                'required' => json_encode(array('email', 'captcha')),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'upload',
                'title' => '资源上传',
                'module' => 'resource',
                'path' => 'resource/upload',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'download',
                'title' => '资源下载',
                'module' => 'resource',
                'path' => 'resource/download',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_all',
                'title' => '全部接口',
                'module' => 'setting',
                'path' => 'setting/api_all',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_more',
                'title' => '更多接口',
                'module' => 'setting',
                'path' => 'setting/api_more',
                'required' => json_encode(array()),
                'optional' => json_encode(array("p", "n", "title")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_add',
                'title' => '添加接口',
                'module' => 'setting',
                'path' => 'setting/api_add',
                'required' => json_encode(array("title", "path")),
                'optional' => json_encode(array("required", "optional", "method", "validated")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_edit',
                'title' => '修改接口',
                'module' => 'setting',
                'path' => 'setting/api_edit',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array("title", "path", "required", "optional", "method", "validated")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_del',
                'title' => '移除接口',
                'module' => 'setting',
                'path' => 'setting/api_del',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'api_revive',
                'title' => '恢复接口',
                'module' => 'setting',
                'path' => 'setting/api_revive',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'module_all',
                'title' => '全部模块',
                'module' => 'setting',
                'path' => 'setting/module_all',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'module_more',
                'title' => '更多模块',
                'module' => 'setting',
                'path' => 'setting/module_more',
                'required' => json_encode(array()),
                'optional' => json_encode(array("p", "n")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'module_add',
                'title' => '添加模块',
                'module' => 'setting',
                'path' => 'setting/module_add',
                'required' => json_encode(array("key", "name")),
                'optional' => json_encode(array("parent_key")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'module_edit',
                'title' => '修改模块',
                'module' => 'setting',
                'path' => 'setting/module_edit',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array("name", "parent_key")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'module_del',
                'title' => '移除模块',
                'module' => 'setting',
                'path' => 'setting/module_del',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_all',
                'title' => '全部角色',
                'module' => 'role',
                'path' => 'role/all',
                'required' => json_encode(array()),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_more',
                'title' => '更多角色',
                'module' => 'role',
                'path' => 'role/more',
                'required' => json_encode(array()),
                'optional' => json_encode(array("p", "n")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_add',
                'title' => '添加角色',
                'module' => 'role',
                'path' => 'role/add',
                'required' => json_encode(array("key", "name")),
                'optional' => json_encode(array("description")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_edit',
                'title' => '编辑角色',
                'module' => 'role',
                'path' => 'role/edit',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array("name", "description")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_del',
                'title' => '删除角色',
                'module' => 'role',
                'path' => 'role/del',
                'required' => json_encode(array("id")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_user',
                'title' => '用户列表',
                'module' => 'role',
                'path' => 'role/users',
                'required' => json_encode(array()),
                'optional' => json_encode(array("p", "n", "key", "value")),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_api',
                'title' => '接口权限列表',
                'module' => 'role',
                'path' => 'role/apis',
                'required' => json_encode(array("key")),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
        $this->db->insert(CIPLUS_DB_PREFIX . 'api',
            array(
                'key' => 'role_api_edit',
                'title' => '接口权限编辑',
                'module' => 'role',
                'path' => 'role/api_edit',
                'required' => json_encode(array("dict", 'role')),
                'optional' => json_encode(array()),
                'method' => 'request',
                'validated' => 1,
                'usable' => 1,
                'readonly' => 1
            ));
    }

    public function down() {
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'api');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'module');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'role');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'role_api');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'role_user');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'user');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'user_info');
    }
}
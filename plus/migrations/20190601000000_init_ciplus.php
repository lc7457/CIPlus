<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Init_ciplus extends CI_Migration {
    public function up() {
        $this->create_api();
        $this->create_module();
        $this->create_role();
        $this->create_role_api();
        $this->create_role_user();
        $this->create_user();
        $this->create_user_info();
    }

    private function create_api() {
        $this->dbforge->add_field(array(
            // 序列ID
            'id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            // 唯一标识
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'unique' => TRUE
            ),
            // 接口标名
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            // 模块归属
            'module' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            // 接口路径
            'path' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
            ),
            // 必填参数
            'required' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '[]',
            ),
            // 选填参数
            'optional' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '[]',
            ),
            // 接口方法
            'method' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => 'request',
            ),
            // 是否验证
            'validated' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 1,
            ),
            // 是否可用
            'usable' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 1,
            ),
            // 只读
            'readonly' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 0,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('key', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'api',
            TRUE,
            $this->attribute('接口表：#####')
        );
    }

    private function create_module() {
        $this->dbforge->add_field(array(
            // 序列ID
            'id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            // 唯一标识
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'unique' => TRUE
            ),
            // 模块标名
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            // 父模块ID
            'parent_id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'default' => 0,
            ),
            // 只读
            'readonly' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 0,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('key', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'module',
            TRUE,
            $this->attribute('模块表：#####')
        );
    }

    private function create_role() {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'unique' => true
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'description' => array(
                'type' => 'VARCHAR',
                'constraint' => '140',
                'null' => TRUE,
            ),
            'readonly' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => 0,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('key', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'role',
            TRUE,
            $this->attribute('角色表：#####')
        );
    }

    private function create_role_api() {
        $this->dbforge->add_field(array(
            'role_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
            ),
            'api_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
            ),
        ));
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'role_api',
            TRUE,
            $this->attribute('角色表：接口权限')
        );
    }

    private function create_role_user() {
        $this->dbforge->add_field(array(
            'role_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
            ),
        ));
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'role_user',
            TRUE,
            $this->attribute('角色表：用户角色')
        );
    }

    private function create_user() {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'account' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
                'unique' => TRUE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
                'unique' => TRUE
            ),
            'phone' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
                'unique' => TRUE
            ),
            'password' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'create_time' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'default' => 1546315200
            ),
            'usable' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => FALSE,
                'default' => 1,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'user',
            TRUE,
            $this->attribute('用户表：#####')
        );
    }

    private function create_user_info() {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            'sex' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => FALSE,
                'default' => 0,
            ),
            'avatar' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => TRUE,
                'default' => '/'
            ),
            'area' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'city' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'province' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'country' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'introduction' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'user_info',
            TRUE,
            $this->attribute('用户表：基本信息')
        );
    }

    private function attribute($comment) {
        return array(
            'ENGINE' => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8',
            'COMMENT' => "'" . $comment . "'"
        );
    }

    public function down() {
    }
}
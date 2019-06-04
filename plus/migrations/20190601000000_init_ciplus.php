<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Init_ciplus extends CI_Migration {
    public function up() {
        $this->create_api();
        $this->create_role();
        $this->create_role_api();
        $this->create_role_user();
        $this->create_user();
        $this->create_user_info();
        $this->init_data();
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
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('key', TRUE);
        $this->dbforge->create_table(
            CIPLUS_DB_PREFIX . 'api',
            TRUE,
            $this->attribute('接口表：#####')
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
                'constraint' => '255',
                'null' => TRUE,
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
                'constraint' => '10',
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

    private function init_data() {
        $this->db->insert(CIPLUS_DB_PREFIX . 'user', array(
            'account' => 'admin',
            'email' => 'admin@cprap.com',
            'password' => 'b34e50b8b8b4b1fe831a20e37db6285b38adb5c0510afdd7dd62ac5a8412e5be4685b5a8408cf3cfc7b70058be2309a759009be6273ccc0a44f3fa57391abc80qfPIU29n4I6A0e8kEPx/RvuHex93c9Bl9sN9X333S7kspgczaGFXuC00KBoBvctArV/3yC0h8oErepMIFMFKBg==',
            'create_time' => time()
        ));
    }

    public function down() {
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'api');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'role');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'role_api');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'role_user');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'user');
        $this->dbforge->drop_table(CIPLUS_DB_PREFIX . 'user_info');
    }
}
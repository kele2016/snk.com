<?php
// +----------------------------------------------------------------------
// | VMCSHOP [V M-Commerce Shop]
// +----------------------------------------------------------------------
// | Copyright (c) vmcshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.vmcshop.com/licensed)
// +----------------------------------------------------------------------
// | Author: Shanghai ChenShang Software Technology Co., Ltd.
// +----------------------------------------------------------------------


class seller_mdl_seller_join extends dbeav_model {
    var $item = array(
        'attr_id' => '',
        'attr_name' => '',
        'attr_type' => '',
        'attr_required' => '',
        'attr_search' => '',
        'attr_option' => '',
        'attr_valtype' => '',
        'attr_tyname' => '',
        'attr_group' => '',
        'attr_show' => '',
        'attr_order' => '',
        'attr_column' => '',
        'attr_sdfpath' => '',
    );
    function __construct(&$app) {
        $this->app = $app;
        $this->columns = array(
            'attr_name' => array(
                'label' => ('注册项名称') ,
                'width' => 200
            ) ,
            'attr_type' => array(
                'label' => ('注册项类型') ,
                'width' => 100
            ) ,
            'attr_required' => array(
                'label' => ('是否必填') ,
                'type' => 'bool',
                'width' => 100
            ) ,
            'attr_display' => array(
                'label' => ('是否显示') ,
                'type' => 'bool',
                'width' => 100
            ) ,
            'attr_order' => array(
                'label' => ('排序') ,
                'width' => 50
            ) ,
        );
        $this->schema = array(
            'default_in_list' => array_keys($this->columns) ,
            'in_list' => array_keys($this->columns) ,
            'idColumn' => 'attr_id',
            'columns' => $this->columns
        );
        $this->init();
    }

    function init() {
        if (!$this->app->getConf('seller.attr')) {
            foreach (array_merge(self::get_buildin_attr() , self::get_ext_attr()) as $item) {
                $this->save($item);
            }
        }
    }

    function get_schema() {
        return $this->schema;
    }
    function getList($cols = '*', $filter = array() , $offset = 0, $limit = - 1, $orderby = null) {
        if ($data = unserialize($this->app->getConf('member.attr'))) {
            self::m_array_sort($data, 'attr_order');
            foreach ($data as $k => $val) {
                if ($val['attr_column'] == 'mobile') {
                    unset($data[$k]);
                }
            }
            return $data;
        } else {
            return array();
        }
    }
   
    function dump($id, $field = '*', $subSdf = null) {
        $data = $this->getList();
        foreach ($data as $row) {
            if ($row['attr_id'] == $id) {
                #unset($row['attr_id']);
                return $row;
            }
        }
        return false;
    }
    function save(&$data, $mustUpdate = NULL, $mustInsert = false) {
        $old = $this->getList();
        if ($data['attr_id']) { //编辑
            foreach ($old as $row) {
                if ($row['attr_id'] == $data['attr_id']) {
                    foreach ($row as $key => $value) {
                        $row = array_merge($row, self::get_real_type($row['attr_tyname']));
                        if (isset($data[$key])) {
                            $row[$key] = $data[$key];
                        }
                    }
                    if (!$this->is_system($row['attr_tyname']) && isset($row['attr_column'])) {
                        $schema = $this->gen_schema($row);
                        $col_desc = $schema[$row['attr_column']];
                        $col_name = $row['attr_column'];
                        $members = $this->app->model('members');
                        $meta_model = app::get('dbeav')->model('meta_register');
                        $aData = $meta_model->getList('*', array(
                            'tbl_name' => $members->table_name(true) ,
                            'col_name' => $col_name
                        ));
                        if (!$aData) return false;
                        $sdf = $aData[0];
                        $sdf['col_desc'] = $col_desc;
                        if (!$meta_model->save($sdf)) return false;
                    }
                }
                $new[] = $row;
            }
        } else { //新增
            $old[] = $this->prepare_add($data);
            $new = $old;
        }
        return $this->app->setConf('member.attr', serialize($new));
    }
    private function prepare_add($data) {
        $ret = array();
        $data = array_merge($data, self::get_real_type($data['attr_tyname']));
        foreach (array_keys($this->item) as $key) {
            if (in_array($key, array(
                'attr_search',
                'attr_required'
            ))) { #修正bool类型
                $ret[$key] = ($data[$key] && $data[$key] != 'false') ? 'true' : 'false';
            } elseif (in_array($key, array(
                'attr_option'
            ))) { #修正多选类型
                $ret[$key] = $data[$key] ? serialize($data[$key]) : '';
            } else {
                $ret[$key] = $data[$key];
            }
            $ret['attr_show'] = 'false';
            $ret['attr_id'] = $this->get_max_id() + 1;
            $ret['attr_order'] = $this->get_max_order() + 1;
            if (!$this->is_system($data['attr_tyname']) && isset($data['attr_column'])) {
                $schema = $this->gen_schema($data);
                $this->register_meta($schema);
            }
        }
        return $ret;
    }
    function get_max_id() {
        $ret = 0;
        foreach ($this->getList() as $row) {
            $ret = $ret > $row['attr_id'] ? $ret : $row['attr_id'];
        }
        return $ret;
    }
    function get_max_order() {
        $ret = 0;
        foreach ($this->getList() as $row) {
            $ret = $ret > $row['attr_order'] ? $ret : $row['attr_order'];
        }
        return $ret;
    }
    function is_system($tyname) {
        if ($tyname == ('系统默认')) {
            return true;
        }
        return false;
    }
    function gen_schema($data) {
        $schema = array(
            $data['attr_column'] => array(
                'type' => $data['attr_valtype'],
                'required' => false,
                'label' => $data['attr_name'],
                'width' => 110,

                'in_list' => true,
                'orderby' => false,
            ) ,
        );
        if (isset($data['attr_sdfpath'])) {
            $schema[$data['attr_column']]['sdfpath'] = $data['attr_sdfpath'];
        }
        return $schema;
    }
    function register_meta($schema) {
        $mem_model = $this->app->model('members');
        $mem_model->meta_register($schema);
    }
    function delete($attr_id, $subSdf = false) {
        $data = $this->getList();
        foreach ($data as $key => $row) {
            if ($row['attr_id'] == $attr_id) break;
        }
        $row = $data[$key];
        unset($data[$key]);
        $col_name = $row['attr_column'];
        $members = $this->app->model('members');
        $meta_model = app::get('dbeav')->model('meta_register');
        if (!($meta_model->delete(array(
            'tbl_name' => $members->table_name(true) ,
            'col_name' => $col_name
        )))) {
            return false;
        }
        return $this->app->setConf('member.attr', serialize($data));
    }
    function set_visibility($attr_id, $status) {
        $data = $this->getList();
        foreach ($data as $key => $row) {
            if ($row['attr_id'] == $attr_id) break;
        }
        $data[$key]['attr_show'] = $status ? 'true' : 'false';
        return $this->app->setConf('member.attr', serialize($data));
    }
    function update_order($orders) {
        $orders = array_flip($orders);
        $data = $this->getList();
        foreach ($data as $key => $row) {
            $row['attr_order'] = $orders[$row['attr_id']];
            $data[$key] = $row;
        }
        return $this->app->setConf('member.attr', serialize($data));
    }
    static function get_buildin_attr() {
        return array(
            array(
                'attr_name' => ('姓名') ,
                'attr_column' => 'name',
                'attr_type' => 'text',
                'attr_required' => 'false',
                'attr_search' => 'false',
                'attr_option' => '',
                'attr_show' => 'true',
                'attr_valtype' => '',
                'attr_tyname' => ('系统默认') ,
                'attr_order' => '1',
                'attr_group' => 'defalut'
            ) ,
            array(
                'attr_name' => ('地区') ,
                'attr_column' => 'area',
                'attr_type' => 'region',
                'attr_required' => 'false',
                'attr_search' => 'false',
                'attr_option' => '',
                'attr_show' => 'true',
                'attr_valtype' => '',
                'attr_tyname' => ('系统默认') ,
                'attr_order' => '4',
                'attr_group' => 'defalut'
            ) ,
            array(
                'attr_name' => ('联系地址') ,
                'attr_column' => 'addr',
                'attr_type' => 'text',
                'attr_required' => 'false',
                'attr_search' => 'false',
                'attr_option' => '',
                'attr_show' => 'true',
                'attr_valtype' => '',
                'attr_tyname' => ('系统默认') ,
                'attr_order' => '5',
                'attr_group' => 'defalut'
            ) ,
            array(
                'attr_name' => ('性别') ,
                'attr_column' => 'sex',
                'attr_type' => 'gender',
                'attr_required' => 'false',
                'attr_search' => 'false',
                'attr_option' => '',
                'attr_show' => 'true',
                'attr_valtype' => '',
                'attr_tyname' => ('系统默认') ,
                'attr_order' => '2',
                'attr_group' => 'defalut'
            ) ,
            array(
                'attr_name' => ('手机号码') ,
                'attr_column' => 'mobile',
                'attr_type' => 'text',
                'attr_required' => 'false',
                'attr_search' => 'false',
                'attr_option' => '',
                'attr_show' => 'true',
                'attr_valtype' => '',
                'attr_tyname' => ('系统默认') ,
                'attr_order' => '8',
                'attr_group' => 'defalut'
            ) ,
            array(
                'attr_name' => ('固定电话') ,
                'attr_column' => 'tel',
                'attr_type' => 'text',
                'attr_required' => 'false',
                'attr_search' => 'false',
                'attr_option' => '',
                'attr_show' => 'true',
                'attr_valtype' => '',
                'attr_tyname' => ('系统默认') ,
                'attr_order' => '8',
                'attr_group' => 'defalut'
            ) ,
            array(
                'attr_name' => ('Email') ,
                'attr_column' => 'tel',
                'attr_type' => 'email',
                'attr_required' => 'false',
                'attr_search' => 'false',
                'attr_option' => '',
                'attr_show' => 'true',
                'attr_valtype' => '',
                'attr_tyname' => ('系统默认') ,
                'attr_order' => '8',
                'attr_group' => 'defalut'
            ) ,
            array(
                'attr_name' => ('生日') ,
                'attr_column' => 'birthday',
                'attr_type' => 'date',
                'attr_required' => 'false',
                'attr_search' => 'false',
                'attr_option' => '',
                'attr_show' => 'true',
                'attr_valtype' => '',
                'attr_tyname' => ('系统默认') ,
                'attr_order' => '3',
                'attr_group' => 'defalut'
            ) ,
        );
    }
    static function get_ext_attr() {
        return array(
            array(
                'attr_name' => 'QQ',
                'attr_column' => 'qq',
                'attr_sdfpath' => 'contact/qq',
                'attr_type' => 'text',
                'attr_required' => 'false',
                'attr_search' => 'true',
                'attr_option' => '',
                'attr_show' => 'false',
                'attr_valtype' => 'number',
                'attr_tyname' => 'QQ',
                'attr_order' => '11',
                'attr_group' => 'contact'
            ) ,
            array(
                'attr_name' => ('微博weibo') ,
                'attr_column' => 'weibo',
                'attr_sdfpath' => 'contact/weibo',
                'attr_type' => 'text',
                'attr_required' => 'false',
                'attr_search' => 'false',
                'attr_option' => '',
                'attr_show' => 'false',
                'attr_tyname' => ('微博weibo') ,
                'attr_order' => '14',
                'attr_group' => 'contact'
            ) ,
            array(
                'attr_name' => ('微信wechat') ,
                'attr_column' => 'wechat',
                'attr_sdfpath' => 'contact/wechat',
                'attr_type' => 'text',
                'attr_required' => 'false',
                'attr_search' => 'false',
                'attr_option' => '',
                'attr_show' => 'false',
                'attr_tyname' => ('微博weibo') ,
                'attr_order' => '24',
                'attr_group' => 'contact'
            ) ,
        );
    }
    static function m_array_sort(&$array, $sortkey) {
        foreach ($array as $key => $row) {
            $keyvalue[$key] = $row[$sortkey];
        }
        asort($keyvalue);
        foreach ($keyvalue as $key => $value) {
            $ret[$key] = $array[$key];
        }
        $array = $ret;
    }
    static function get_real_type($vtype) {
        if ($vtype == ('系统默认')) {
            return array();
        }
        switch ($vtype) {
            case ('输入项'):
                $ret['attr_valtype'] = '';
                $ret['attr_type'] = 'text';
                $ret['attr_group'] = 'input';
            break;
            case ('单选项'):
                $ret['attr_valtype'] = '';
                $ret['attr_type'] = 'select';
                $ret['attr_group'] = 'select';
            break;
            case ('多选项'):
                $ret['attr_valtype'] = '';
                $ret['attr_type'] = 'checkbox';
                $ret['attr_group'] = 'select';
            break;
            case ('日期选择器'):
                $ret['attr_valtype'] = '';
                $ret['attr_type'] = 'date';
                $ret['attr_group'] = 'contact';
            break;
            case 'QQ':
            case '微博weibo':
            case '微信wechat':
                $ret['attr_valtype'] = 'number';
                $ret['attr_type'] = 'text';
                $ret['attr_group'] = 'contact';
            break;

        }
        return $ret;
    }
    function gen_form() {
        $mem_schema = $this->app->model('members')->_columns();
        $ui = new base_component_ui($this);
        foreach ($this->getList() as $item) {
            $item_schema = $mem_schema[$item['attr_column']];
            if (isset($item_schema)) {
                $title = $item_schema['label'] ? $item_schema['label'] : $item['attr_name'];
                if ($item_schema['sdfpath']) {
                    $a_temp = explode("/", $item_schema['sdfpath']);
                    if (count($a_temp) > 1) {
                        $name = array_shift($a_temp);
                        if (count($a_temp)) foreach ($a_temp as $value) {
                            $name.= '[' . $value . ']';
                        }
                    }
                } else {
                    $name = $item['attr_column'];
                }
            } else {
                $title = $item['attr_name'];
                $name = $item['attr_column'];
            }
            $type = $item['attr_type'];
            #地区组件需要传入app
            if ($item['attr_column'] == 'area') {
                $input['app'] = 'ectools';
            }
            if ($item['attr_type'] == 'select' || $item['attr_type'] == 'checkbox') {
                $input['options'] = unserialize($item['attr_option']);
            }
            $input['required'] = $item['attr_required'] == 'true' ? true : false;
            $input['name'] = $name;
            $input['title'] = $title;
            $input['type'] = $type;
            $html.= $ui->form_input($input);
            unset($input);
        }
        return $html;
    }
}

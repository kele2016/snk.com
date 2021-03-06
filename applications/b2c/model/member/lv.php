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


class b2c_mdl_member_lv extends dbeav_model
{
    public $defaultOrder = array('point', ' ASC');


    public function save(&$aData, $mustUpdate = null, $mustInsert = false)
    {
        $default_lv_id = $this->get_default_lv();

        if (!$default_lv_id) {
            $aData['default_lv'] = 1;
        }
    
        if (isset($aData['point'])) {
            $aData['experience'] = $aData['point'];
        }
        if (isset($aData['experience'])) {
            $aData['point'] = $aData['experience'];
        }

        return parent::save($aData);
    }

    /**
     * @获取会员等级列表信息
     *
     * @param $cols 查询字段
     * @param $filter 查询过滤条件
     */
    public function getMLevel($cols = '*', $filter = array())
    {
        $rows = $this->getList($cols, $filter);

        return  $rows ? $rows : array();
    }

    //获取所有的会员等级信息
    public function getListAll()
    {
        if (!cachemgr::get('member_lv_info_all', $data)) {
            cachemgr::co_start();
            $memberLvData = $this->getList('*');
            foreach ($memberLvData as $row) {
                $data[$row['member_lv_id']] = $row;
            }
            cachemgr::set('member_lv_info_all', $data, cachemgr::co_end());
        }

        return $data;
    }

    public function get_default_lv()
    {
        $ret = $this->getList('member_lv_id', array('default_lv' => 1));

        return $ret[0]['member_lv_id'];
    }

    public function unset_default_lv($default_lv_id)
    {
        $sdf['member_lv_id'] = $default_lv_id;
        $sdf['default_lv'] = 0;
        $this->save($sdf);
    }

    public function validate(&$data, &$msg)
    {
        $fag = 1;
        if ($data['name'] == '') {
            $msg = ('等级名称不能为空！');
            $fag = 0;
        }
        $ret = $this->getList('member_lv_id', array('name' => $data['name']));
        $member_lv_id = $ret[0]['member_lv_id'];
        $lv = $this->getList('*', array('default_lv' => 1));
        if (isset($data['point'])) {
            $data['point'] = intval($data['point']);
            $filter = array('point' => $data['point']);
            $levelSwitch = ('积分');
            $exist = $this->getList('*', $filter);
            $default_lv = $lv[0]['name'];
            if ($exist && ($exist[0]['member_lv_id'] != $data['member_lv_id'])) {
                $msg = ('已存在').$levelSwitch.('相同的会员等级');
                $fag = 0;
            }
        }
        if (isset($data['experience'])) {
            $data['experience'] = intval($data['experience']);
            $filter = array('experience' => $data['experience']);
            $levelSwitch = ('经验值');
            $exist = $this->getList('*', $filter);
            $default_lv = $lv[0]['name'];
            if ($exist && ($exist[0]['member_lv_id'] != $data['member_lv_id'])) {
                $msg = ('已存在').$levelSwitch.('相同的会员等级');
                $fag = 0;
            }
        }

        if ($member_lv_id && $member_lv_id != $data['member_lv_id']) {
            $msg = ('同名会员等级存在！');
            $fag = 0;
        }
        if (($data['default_lv'] == 1 && $default_lv) && $data['member_lv_id'] != $lv[0]['member_lv_id']) {
            $msg = $default_lv.('  已是默认等级，请先取消！！');
            $fag = 0;
        }
        if ($data['dis_count'] < 0 or $data['dis_count'] > 1) {
            $msg = ('会员折扣率不是有效值！');
            $fag = 0;
        }
        if ($data['point'] < 0 || $data['experience'] < 0) {
            $msg = $levelSwitch.('不能为负！');
            $fag = 0;
        }
        if ($data['dis_count'] == 0) {
            $data['dis_count'] = '0.0';
        }

        // validate service
        $site_point_expired = $this->app->getConf('site.point_expired');
        $site_point_expried_method = $this->app->getConf('site.point_expried_method');
        foreach (vmc::servicelist('member_lv_extends_validate') as $k => $o) {
            if (method_exists($o, 'validate') && $fag) {
                $fag = $o->validate($site_point_expired, $site_point_expried_method, $data, $msg);
            }
        }

        return $fag;
    }

    public function pre_recycle($data)
    {
        $members = $this->app->model('members');
        foreach ($data as $val) {
            $aData = $members->getList('member_id', array('member_lv_id' => $val['member_lv_id']));
            if ($aData) {
                $this->recycle_msg = ('该等级下存在会员,不能删除');

                return false;
            }

            if ($val['default_lv']) {
                $this->recycle_msg = ('该等级是默认会员等级，不能删除');

                return false;
            }
        }

        if ($this->count() == count($data)) {
            $this->recycle_msg = ('至少需要有一个会员等级存在，并且需为默认');

            return false;
        }

        return true;
    }

    public function pre_restore(&$data, $restore_type = 'add')
    {
        if (!($this->is_exists($data['name']))) {
            $data['need_delete'] = true;

            return true;
        } else {
            if ($restore_type == 'add') {
                $new_name = $data['name'].'_1';
                while ($this->is_exists($new_name)) {
                    $new_name = $new_name.'_1';
                }
                $data['name'] = $new_name;
                $data['need_delete'] = true;

                return true;
            }
            if ($restore_type == 'none') {
                $data['need_delete'] = false;

                return true;
            }
        }
    }

    public function is_exists($name)
    {
        $row = $this->getList('member_lv_id', array('name' => $name));
        if (!$row) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @获取某一会员等级详细信息
     *
     * @param $cols 查询字段
     * @param $sLv 会员等级id
     */
    public function getMLevelById($cols = '*', $sLv)
    {
        $aTemp = $this->getList($cols, array('member_lv_id' => $sLv));

        return $aTemp;
    }

    /**
     * @获取会员某一等级的折扣率
     *
     * @param $lvid 会员等级id
     *
     * @return data 会员等级折扣
     */
    public function get_dis_count($lvid)
    {
        $list = $this->getList('dis_count', array('member_lv_id' => $lvid));

        return $list[0]['dis_count'];
    }
}

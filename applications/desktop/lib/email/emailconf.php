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


  
class desktop_email_emailconf{

    function get_emailConfig(){
        $app = app::get('desktop');
        $aTmp['usermail'] = $app->getConf('email.config.usermail');
        $aTmp['smtpport'] = $app->getConf('email.config.smtpport');
        $aTmp['smtpserver'] = $app->getConf('email.config.smtpserver');
        $aTmp['smtpuname'] = $app->getConf('email.config.smtpuname');
        $aTmp['smtppasswd'] = $app->getConf('email.config.smtppasswd');
        $aTmp['sendway'] = $app->getConf('email.config.sendway') ? $app->getConf('email.config.sendway') : 'mail';
        return $aTmp;
    }
}
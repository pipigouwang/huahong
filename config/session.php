<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 会话设置
// +----------------------------------------------------------------------

return [
     'id'             => '',
    // SESSION_ID���ύ����,���flash�ϴ�����
    'var_session_id' => '',
    // SESSION ǰ׺
    'prefix'         => 'think',
    // ������ʽ ֧��redis memcache memcached
    'type'           => '',
    // �Ƿ��Զ����� SESSION
    'auto_start'     => true
];
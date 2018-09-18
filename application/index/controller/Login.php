<?php

/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2018/8/1

 * Time: 16:46

 */



namespace app\index\controller;





use app\index\model\Member;

use think\Controller;

use wxlib\wxtool;



class Login extends Controller

{

    public function index()

    {
echo 123;die;

        //获取用户code 通过code 获取accesstoken  通过token 获取openid并判断是否注册或是否绑定

        $code = $_GET['code'];

        if(!$code){

            echo '404页面';die;

        }

        //获取accesstoken

        $tool = (new wxtool())->getAccessTokenByCode($code);

    }





}
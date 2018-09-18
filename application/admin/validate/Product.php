<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 11:37
 */
namespace app\admin\validate;
class Product {
    public $rule = [
        'name'=>'require|max:80'
        ,'stock'=>'require|number',
        'company'=>'requireWith:company|max:40',
        'specifications'=>'require|max:40',
        'price'=>'require|number'
        ,'sn'=>'require',
        'is_on_sale'=>'require|in:0,1',
        'status'=>'require|in:0,1',
        'sta_gods'=>'require|in:1,2',
        'period'=>'require|number',
        'enddate'=>'require',
        'imgurl'=>'require',
        'remarks'=>'require'
    ];

    public $msg = [

    ];
}
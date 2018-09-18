<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/8
 * Time: 17:07
 */

namespace app\index\controller;

class Search extends BaseOfUser
{
    //快速搜索
    public function product()
    {
        $this->con['lng2'];
        $this->con['lat2'];
        list($msg,$err,$datas) = (new \app\index\model\Product())
            ->fast_find($this->page,$this->limit,$this->con);
        return json(['message'=>$msg,'err'=>$err,'data'=>$datas]);
    }
    public function shop()
    {
        list($msg,$err,$datas) = (new \app\index\model\Member())
            ->shopdetail($this->con);
        return json(['message'=>$msg,'err'=>$err,'data'=>$datas]);
    }


}
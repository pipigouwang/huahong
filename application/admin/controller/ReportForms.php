<?php
/*
 * Author:Shane
 * */

namespace app\admin\controller;


use think\Controller;


class ReportForms extends Controller
{
    /*获取所有的总代理*/
    public function get_general_agency(){
        $model=new \app\admin\model\ReportForms();
        $res=$model->get_general_agency();
        return json_encode($res);
    }
    /*分享统计，抽奖统计，每日签到统计*/
    public function statistics(){
        $model=new \app\admin\model\ReportForms();
        $res=$model->statistics_all($this->request->post());
        return json_encode($res);
    }
    /*商品以及代理商的数据统计*/
    public function statistics_goods_amount(){
        /*安年月日查询全部商品的销售数量和金额*/
        $model=new \app\admin\model\ReportForms();
        $res=$model->statistics_goods_amount($this->request->post());
        return json_encode($res);
    }
    /*获取单个总代理下的所有销售*/
    public function get_salesman_all(){
        $model=new \app\admin\model\ReportForms();
        $res=$model->get_salesman_all($this->request->post('agency_id'));//总代理id
        return json_encode($res);
    }
    /*获取单个总代理下的所有诊所*/
    public function get_clinic_all(){
        $model=new \app\admin\model\ReportForms();
        $res=$model->get_clinic_all($this->request->post('agency_id'));//总代理id
        return json_encode($res);
    }
    /*获取单个总代理下的所有子代理*/
    public function get_agency_all(){
        $model=new \app\admin\model\ReportForms();
        $res=$model->get_agency_all($this->request->post('agency_id'));//总代理id
        return json_encode($res);
    }
    /*获取所有商品*/
    public function get_goods(){
        $model=new \app\admin\model\ReportForms();
        $res=$model->get_goods();
        return json_encode($res);
    }
    /*获取总代理下所有子代理的商品或单个子代理下的所有商品*/
    public function get_agency_all_goods(){
        $model=new \app\admin\model\ReportForms();
        $res=$model->get_agency_all_goods($this->request->post('agency_id'),$this->request->post('agency_one_id'));//总代理id和子代理id
        return json_encode($res);
    }
    /*获取所有销售或单个销售所代理的商品*/
    public function get_salesman_goods(){
        $model=new \app\admin\model\ReportForms();
        $res=$model->get_salesman_goods($this->request->post('agency_id'),$this->request->post('salesman_id'));//总代理id和销售id
        return json_encode($res);
    }
    /*获取所有诊所和单个诊所代理的商品*/
    public function get_clinic_goods(){
        $model=new \app\admin\model\ReportForms();
        $res=$model->get_clinic_goods($this->request->post('agency_id'),$this->request->post('clinic_id'));//总代理id和诊所id
        return json_encode($res);
    }

}
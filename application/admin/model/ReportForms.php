<?php
/*
 * Author:Shane
 * */

namespace app\admin\model;


use think\Db;
use think\Model;

class ReportForms extends Model
{
    /*获取所有的总代理*/
    public function get_general_agency(){
       $res=Db::name('member')->where(['type'=>4,'fatherid'=>-2,'status'=>1])->field('id,uname')->select();
       return ['code'=>200,'data'=>$res,'Message'=>"Success"];
    }
    /*分享统计,签到统计,抽奖统计*/
    public function statistics_all($content){
        $time_way=$content['time_way'];//按年月日方式
        $time=$content['date_time'];//时间
        function statistics($table,$filed,$time_way,$time,$filed1,$where){
            $query=Db::name($table);
            if($where){
              $query->where($where);
            }
            if($time_way=="year"){/*统计年*/
                return $res=$query->group("year($filed)")->field("$filed1,year($filed) as times")->order("$filed asc")->select();
            }elseif($time_way=="month"){/*统计月*/
                if($time){
                    $query->where($filed,'egt',$time);
                    $time1=date("Y-1-1",strtotime("$time+1 year"));
                    $query->where($filed,'lt',$time1);
                }else{
                    $query->where($filed,'egt',date('Y-1-1'));
                    $time1=date("Y-1-1",strtotime("+1 year"));
                    $query->where($filed,'lt',$time1);
                }
                return $res=$query->group("month($filed)")->field("$filed1,month($filed) as times")->order("$filed asc")->select();
            }else{/*统计天*/
                if($time){
                    $query->where($filed,'egt',$time);
                    $time1=date("Y-m-1",strtotime("$time+1 month"));
                    $query->where($filed,'lt',$time1);
                }else{
                    $query->where($filed,'egt',date('Y-m-1'));
                    $time1=date("Y-m-1",strtotime("+1 month"));
                    $query->where($filed,'lt',$time1);
                }
                return $res=$query->group("day($filed)")->field("$filed1,day($filed) as times")->order("$filed asc")->select();
            }
        }
        $sign_in=statistics('sign_count','date',$time_way,$time,"count(*) as sum",null);//每日签到统计
        $share=statistics('share_count','date',$time_way,$time,"count(*) as sum",null);//每日分享统计
        $integral=statistics('member_point_history','date',$time_way,$time,"sum(pointsum) as sum",['type'=>3]);//积分兑换统计

        $sign_in=$this->year_month_day($time_way,$time,$sign_in);
        $share=$this->year_month_day($time_way,$time,$share);
        $integral=$this->year_month_day($time_way,$time,$integral);

        return ['code'=>200,'data'=>['sign_in'=>$sign_in,'share'=>$share,'integral'=>$integral],'msg'=>"Success"];
    }
    /*年月日数据处理*/
    public function year_month_day($time_way,$time,$data){
        $res=[];
        if($time_way=='year'){
            $num=date("Y");//年暂时不处理
            for($i=2018;$i<=$num;$i++){
                $res[]=0;
            }
            foreach($data as $d){
                $res[$num-$d['times']]=$d['sum'];
            }
            return $res;
        }elseif($time_way=="month"){
            $num=12;//12个月
        }else{
            if($time){
                $num=date('t', strtotime($time));
            }else{
                $num=date('t', strtotime(date('Y-m-1')));//获取当前月的天数
            }
        }
        for($i=1;$i<=$num;$i++){
            $res[]=0;
        }
        foreach($data as $d){
            $res[$d['times']-1]=$d['sum'];
        }
        return $res;
    }
    /*按年月日分别查询出销售商品的金额和数量*/
    public function statistics_goods_amount($content){
        $query=Db::name('order')->alias('o')->join('order_goods og','o.order_id=og.order_id');
        $filed="o.create_date";//时间字段
        /*判断是查询数量是还金额*/
        if($content['sum_amount']=='amount'){
            $sum_amount="sum(o.total_fee) as sum
            ";//查询商品销售总金额
        }else{
            $sum_amount="sum(og.num) as sum";//查询商品销售总数量
        }
        /*判断是否选择了总代理*/
        if($content['agency_id']){
            /*再判断选择的是代理还是销售员还是诊所*/
            if($content['type']=='agency'){//总代理下的子代理
                if($content['agency_one_id']){
                    $ids=[$content['agency_one_id']];
                }else{
                    $agency_goods=$this->get_agency_all($content['agency_id']);
                    if($agency_goods['data']){
                        $ids=array_column($agency_goods['data'],'id');
                    }else{
                        $ids=[0];
                    }
                }
            }elseif($content['type']=='sale'){//总代理下的所有销售人员
                if($content['salesman_id']){
                    $ids=[$content['salesman_id']];
                }else{
                    $salesman_goods=$this->get_salesman_all($content['agency_id']);
                    if($salesman_goods['data']){
                        $ids=array_column($salesman_goods['data'],'id');
                    }else{
                        $ids=[0];
                    }
                }
            }else{//type==clinic总代理下的所有诊所
                if($content['clinic_id']){
                    $ids=[$content['clinic_id']];
                }else{
                    $clinic_goods=$this->get_clinic_all($content['agency_id']);
                    if($clinic_goods['data']){
                        $ids=array_column($clinic_goods['data'],'id');
                    }else{
                        $ids=[0];
                    }
                }
            }
            $query->where('o.member_id','in',$ids);
        }
        /*查询单个商品销售条件没有此条件默认为查询所有商品*/
        if($content['goods_id']){
            $query->where(['og.goods_id'=>$content['goods_id']]);
        }
        if($content['time_way']=="year"){/*统计年*/
            $res=$query->group("year($filed)")->field("$sum_amount,year($filed) as times")->order("$filed asc")->select();
        }elseif($content['time_way']=="month"){/*统计月*/
            $time=$content['date_time'];
            if($time){
                $query->where($filed,'egt',$time);
                $time1=date("Y-1-1",strtotime("$time+1 year"));
                $query->where($filed,'lt',$time1);
            }else{
                $query->where($filed,'egt',date('Y-1-1'));
                $time1=date("Y-1-1",strtotime("+1 year"));
                $query->where($filed,'lt',$time1);
            }
            $res=$query->group("month($filed)")->field("$sum_amount,month($filed) as times")->order("$filed asc")->select();
        }else{/*统计天*/
            $time=$content['date_time'];
            if($time){
                $query->where($filed,'egt',$time);
                $time1=date("Y-m-1",strtotime("$time+1 month"));
                $query->where($filed,'lt',$time1);
            }else{
                $query->where($filed,'egt',date('Y-m-1'));
                $time1=date("Y-m-1",strtotime("+1 month"));
                $query->where($filed,'lt',$time1);
            }
            $res=$query->group("day($filed)")->field("$sum_amount,day($filed) as times")->order("$filed asc")->select();
        }
        $res=$this->year_month_day($content['time_way'],$content['date_time'],$res);
        return ['code'=>200,'data'=>$res,'msg'=>"Success"];
    }
    /*获取所有的总代理*/
    public function general_agency(){
        $res=Db::name('member')->where(['fatherid'=>-2,'type'=>4])->field('id,uname')->order('id desc')->select();
        return ['code'=>200,'data'=>$res,'msg'=>"Success"];
    }
    /*获取单个总代理下的所有子代理*/
    public function get_agency_all($agency_id){
        /*先根据所有总代理的id获取下面所有的代理的id*/
        function test($agency_id){
            $res=Db::name('member')->where(['fatherid'=>$agency_id,'type'=>4])->order('id asc')->field('id,account')->select();
            if($res){
                foreach ($res as $re){
                    $members=Db::name('member')->where(['fatherid'=>$re['id'],'type'=>4])->order('id asc')->field('id,account')->select();
                    if($members){
                        foreach ($members as $member){
                            $res[]=['id'=>$member['id'],'account'=>$member['account']];
                            $result=test($member['id']);
                            if($result){
                                $res=array_merge($res,$result);
                            }
                        }
                    }
                }
            }
            return $res;
        }
        $res=test($agency_id);
        return ['code'=>200,'data'=>$res,'msg'=>"Success"];
    }
    /*获取单个总代理下的所有销售*/
    public function get_salesman_all($agency_id){
        $agency=$this->get_agency_all($agency_id);
        if($agency['data']){
            $res=Db::name('member')->where('fatherid','in',array_column($agency['data'],'id'))->where(['type'=>3])->order('id asc')->field('id,uname')->select();
        }else{
            $res=[];
        }
        return ['code'=>200,'data'=>$res,'msg'=>"Success"];
    }
    /*获取单个总代理下的所有诊所*/
    public function get_clinic_all($agency_id){
        $salesman=$this->get_salesman_all($agency_id);
        $query=Db::name('member');
        if($salesman['data']){
            $ids=array_column($salesman['data'],'id');
            $query->where('fatherid','in',$ids);
        }
        $res=$query->where(['type'=>2])->order('id desc')->field('id,uname')->select();
        return ['code'=>200,'data'=>$res,'msg'=>"Success"];
    }
    /*获取所有商品*/
    public function get_goods(){
        $agency_goods=Db::name('product')->where('status','neq',0)->field('id,name')->select();
        return ['code'=>200,'data'=>$agency_goods,'msg'=>"Success"];
    }
    /*获取总代理下所有子代理的商品*/
    public function get_agency_all_goods($agency_id,$agency_one_id){
        $query=Db::name('member_product')->alias('mp')->join('product p','mp.pid=p.id')->where('p.status','neq',0);
        if($agency_one_id){
            $query->where(['mp.mid'=>$agency_one_id]);
        }else{
            $ids=$this->get_agency_all($agency_id);//获取所有的子代理
            if(!$ids['data']) $ids=[0];
            $query->where('mp.mid','in',array_column($ids['data'],'id'));
        }
        $agency_goods=$query->field('p.id,p.name')->select();
        return ['code'=>200,'data'=>$agency_goods,'msg'=>"Success"];
    }
    /*获取所有销售或单个销售所代理的商品*/
    public function get_salesman_goods($agency_id,$salesman_id){
//        $agency_id=1;//总代理id获取所有销售的地理商品时必传
        $query=Db::name('member_product')->alias('mp')->join('product p','mp.pid=p.id')->where('p.status','neq',0);
        if($salesman_id){
            /*获取单个销售的代理药品*/
//            $salesman_id=1;//销售id
            $res=$query->where(['mp.mid'=>$salesman_id])->field('p.id,p.name')->select();
        }else{
            /*
             * 获取该总代理下面的所有的销售的id
             * 根据所有销售的id来获取每个销售代理药品
             * 然后将所有销售代理的商品保存到一个数组中，然后去重
             *
             * */
            $salesman_all=$this->get_salesman_all($agency_id);//获取该总代理下的所有销售
            $salesman_goods_all=[];
            if($salesman_all['data']){
                foreach ( $salesman_all['data'] as $salesman){
                    $salesman_goods=$query->where(['mp.mid'=>$salesman['id']])->field('p.id,p.name')->select();
                    if($salesman_goods){
                        $salesman_goods_all=array_merge($salesman_goods_all,$salesman_goods);//合并数组
                    }
                }
            }
            /*去重*/
            $res=[];
            if($salesman_goods_all){
                foreach ($salesman_goods_all as $s=>$g){
                    $v=join(',',$g); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
                    $temp[$s]=$v;
                }
                $temp=array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
                foreach ($temp as $k => $v){
                    $array=explode(',',$v); //再将拆开的数组重新组装
                    //下面的索引根据自己的情况进行修改即可
                    $res[$k]['id'] =$array[0];
                    $res[$k]['name'] =$array[1];
                }
            }
        }
        return ['code'=>200,'data'=>$res,'msg'=>"Success"];
    }
    /*获取所有诊所和单个诊所代理的商品*/
    public function get_clinic_goods($agency_id,$clinic_id){
//        $agency_id=1;//总代理id
        $query=Db::name('member_product')->alias('mp')->join('product p','mp.pid=p.id')->where('p.status','neq',0);
        /*获取所有诊所的代理项目*/
        $clinic_all=$this->get_clinic_all($agency_id);//获取所有诊所
        /*获取总代理所代理的商品*/
        $agency_goods=$query->where(['mp.mid'=>$agency_id])->field('p.id,p.name')->select();
        if($clinic_id){
            /*获取单个诊所代理的药品*/
//            $clinic_id=1;//诊所id
            $res=$query->where(['mp.mid'=>$clinic_id])->field('p.id,p.name')->select();
        }else{
            $clinic_goods_all=[];//保存所有的诊所代理药品
            if($clinic_all['data']){
                foreach ( $clinic_all['data'] as $clinic){
                    $clinic_goods=$query->where(['mp.mid'=>$clinic['id']])->field('p.id,p.name')->select();
                    if($clinic_goods){
                        $clinic_goods_all=array_merge($clinic_goods,$clinic_goods_all);//合并数组
                    }
                }
            }
            /*去重并判断该诊所代理的药品是否在该总代理的药品之内*/
            $res=[];
            if($clinic_goods_all){
                foreach ($clinic_goods_all as $c=>$g){
                    $v=join(',',$g); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
                    $temp[$c]=$v;
                }
                $temp=array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
                foreach ($temp as $k => $v){
                    $array=explode(',',$v); //再将拆开的数组重新组装
                    //下面的索引根据自己的情况进行修改即可
                    $res[$k]['id'] =$array[0];
                    $res[$k]['name'] =$array[1];
                }
            }
        }
        /*判断该诊所代理的药品是否是在该总代理所代理的药品之内并去除不在总代理代理的药品之内的药品*/
        if($res){
            if($res){
                $ids=array_column($agency_goods,'id');
                foreach($res as $c=>$o){
                    if(!in_array($o['id'],$ids)){
                        unset($res[$c]);
                    }
                }
            }
        }
        sort($res);
        return ['code'=>200,'data'=>$res,'msg'=>"Success"];
    }
}
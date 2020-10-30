<?php

namespace app\index\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class ThawOrder extends Command
{
    protected function configure ()
    {
        $this->setName('ThawOrder')->setDescription('计划任务 Date');
    }

    protected function execute (Input $input, Output $output)
    {
        $output->writeln("Date ThawOrder Crontab job start...");
        /**计划任务列表集 start**/
        $this->runOrder();
        /**计划任务列表集 end**/
        $output->writeln("Date ThawOrder Crontab job end...");
    }

    /**
    //佣金解冻
     */
    protected function runOrder ()
    {
        //启动事务
        Db::startTrans();
        try {
            //佣金解冻
            $order_money_data = Db::name('order')->field('take_time,order_id')->where('order_status',3)->select();

            foreach ($order_money_data as $k=>$v){
                $end_time = $v['take_time'] +  86400*1;
                if (($end_time - time()) < 0) {
                    //冻结的
                    $freezeinfo=  Db::name('user_freeze_log')->where('status',0)->where('order_id',$v['order_id'])->select();


                    //记录用户的余额变动
                       $list=Db::name('user_freeze_log')->where('status',0)->where('order_id', $v['order_id'])->select();
                       foreach ( $list as $v2){
                                    $data=[
                                        'order_id'=>$v2['order_id'],
                                        'user_id'=>$v2['user_id'],
                                        'money'=>$v2['freezemoney'],
                                        'sign'=>$v2['freezesign'],
                                        'create_time'=>time(),
                                        'info'=>$v2['freezeinfo'],
                                    ];
                                    $res2=Db::name('user_money')->insert($data);
                        }
                    Db::name('user_freeze_log')->where('order_id', $v['order_id'])->update(['status'=>1,'unfreeze_time'=>time()]);
                    //需要给用户增加可用余额
                    foreach ( $freezeinfo as $v1){
                        $res=  Db::name('user')->where('user_id',$v1['user_id'])->setInc('money',$v1['freezemoney']);
                   //  var_dump(  $res);
                    }
                    echo "freeze_id:" . $res. "-status change ." . "\r\n";
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            echo $e->getMessage() . "\r\n";
            Db::rollback();
        }
    }
}
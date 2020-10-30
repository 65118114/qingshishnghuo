<?php

namespace app\index\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class CheckOrder extends Command
{
	protected function configure ()
	{
		$this->setName('CheckOrder')->setDescription('计划任务 Date');
	}

	protected function execute (Input $input, Output $output)
	{
		$output->writeln("Date Crontab job start...");
		/**计划任务列表集 start**/
		$this->runOrder();
		/**计划任务列表集 end**/
		$output->writeln("Date Crontab job end...");
	}

	/**
	 * 自动确认收货
	 * 超时关闭
	 */
	protected function runOrder ()
	{
		//启动事务
		Db::startTrans();
		try {
			$where = array();
            $where[] = array('order_status', 'IN', array(0, 2));
			$order_data = Db::name('order')->where($where)->field('trade_no,order_status,order_id,send_time,create_time')->select();
			if (empty($order_data)) {
				echo "No processing order." . date('Y-m-d H:i:s') . "\r\n";
				exit;
			}
			if (!empty($order_data)) {

				foreach ($order_data as $k => $v) {
					switch ($v['order_status']) {
						case 2:
							$ten_day = $v['send_time'] + 86400*10;
							if (($ten_day - time()) < 0) {
								Db::name('order')->where(array('order_id' => $v['order_id']))->update(array('order_status' =>3, 'take_time' => time()));
								echo "order_trade_no:" . $v['trade_no'] . "-The timeout automatically confims receipt ." . "\r\n";
							}
							break;
						case 0:
							$end_time = $v['create_time'] +  86400;
							if (($end_time - time()) < 0) {
								Db::name('order')->where(array('order_id' => $v['order_id']))->update(array('order_status' =>5, 'over_pay_time' => time()));
								echo "order_trade_no:" . $v['trade_no'] . "-Overdue payment ." . "\r\n";
							}
							break;
					}
				}
			}

			Db::commit();
		} catch (\Exception $e) {
			echo $e->getMessage() . "\r\n";
			Db::rollback();
		}
	}
}
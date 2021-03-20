<?php

namespace App\Jobs;

use App\Exceptions\CustomException;
use App\Models\Order;
use DB;
use Log;

class PayOrderJob extends Job
{
    protected $order;
    protected $payment_ref_num;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $payment_ref_num)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $this->order->pay($this->payment_ref_num);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof CustomException && $e->getErrCode() == 1504) {
                // Push failed order message to front-end because of insufficient stock using FCM or other cloud messaging service
            } else {
                // Push failed order message to front-end because of other system error using FCM or other cloud messaging service
            }
        }
    }
}

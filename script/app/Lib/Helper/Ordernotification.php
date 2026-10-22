<?php 
namespace App\Lib\Helper;
use App\Models\Devicetoken;
use App\Models\User;
class Ordernotification
{

	public static function makeNotifyToAdmin($order)
	{
		if ($order->notify_driver == 'fmc') {
			if (tenant('push_notification') != 'on') {
				 return redirect('/thanks');
			}
			$tokens=Devicetoken::whereHas('user',function($q){
				return $q->where([['status',1],['role_id',3]]);
			})->pluck('token')->all();
			
			\App\Lib\NotifyToUser::fmc($order->invoice_no,'You have received a new order',url('/seller/order/'.$order->id),asset('uploads/'.tenant('uid').'/notification.png'),$tokens);

			return redirect('/thanks');
		}

		$notifyDriver = $order->notify_driver ?? 'mail';
		if ($notifyDriver == 'mail') {
			$admin_details = User::where('role_id', 3)->first();
			$to = $admin_details->email ?? null;
			if (empty($to)) {
				$clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
				$to = $clubInfo['club_email'] ?? (get_option('invoice_data', true)->store_legal_email ?? null);
			}
			if (!empty($to)) {
				if ($order instanceof \Illuminate\Database\Eloquent\Model) {
					$order->loadMissing(['ordermeta', 'user', 'orderlasttrans', 'orderstatus']);
				}
				\App\Lib\NotifyToUser::sendEmail($order, $to, 'admin');
			}
			// return redirect('/thanks');
		}

		if ($order->notify_driver == 'whatsapp') {
			 
	        $message = view('whatsapp.notification', ['info' => $order])->render();
	        $message=str_replace('&#039;',"'",$message);
	        $message= urlencode($message);
	        
	        $whatsapp_no=get_option('whatsapp_no');
	        $url='https://api.whatsapp.com/send?phone=+'.$whatsapp_no.'&text='.$message;

	        return redirect($url);
		}
	}

}
<?php
namespace App\Lib;
use App\Models\Option;
use App\Models\User;
use App\Models\Devicetoken;
use App\Jobs\Sellermailjob;
use Auth;
use App\Mail\Orderstatusmail;
use App\Mail\Adminsendmail;
use Mail;
use Config;
use App\Jobs\TenantMailJob;
class NotifyToUser 
{
	
	public static function makeNotifyToUser($info)
	{
		if($info->user_id != null){
			if ($info->notify_driver == 'mail') {
			 $ordermeta=json_decode($info->ordermeta->value ?? '');
			 if (!empty($ordermeta)) {
			 	$mail_to=$ordermeta->email ?? '';
			 }
			 else{
			 	$mail_to=$info->user->email ?? '';
			 }

			 $mail_from=Auth::user()->email;
			 NotifyToUser::customermail($info,$mail_to);
			}
			elseif($info->notify_driver == 'fmc'){
				$seo=get_option('seo',true);
				$token=Devicetoken::where('user_id',$info->user_id)->where('type','firebase')->latest()->first();

				if (!empty($token)) {
					NotifyToUser::fmc(ucwords($seo->title).' ('.$info->invoice_no.')','Your order is now in '.$info->orderstatus->name ?? '',url('/customer/order/'.$info->id),asset('uploads/'.tenant('uid').'/notification.png'),[$token->token]);
				}
				
			}
		}
		else{
			if ($info->notify_driver == 'mail') {
				$ordermeta=json_decode($info->ordermeta->value ?? '');
				if (!empty($ordermeta)) {
					$mail_to=$ordermeta->email ?? '';
				}
				else{
					$mail_to=$info->user->email ?? '';
				}

				$mail_from=Auth::user()->email;
				NotifyToUser::customermail($info,$mail_to,$mail_from);
			}
		}
		
		return true;
	}

	public static function sendEmail($info, $to=null, $email_identity=null, array $receiptViewOptions = []){
		    if($email_identity == 'admin'){
				$currency=get_option('currency_info');
				$invoice_info=get_option('invoice_data',true);
				if (is_array($info)) {
					$info['invoice_data'] = $invoice_info;
				} elseif ($info instanceof \Illuminate\Database\Eloquent\Model && $info->offsetExists('invoice_data')) {
					$info->offsetUnset('invoice_data');
				}
				$invoiceNo = $info->invoice_no ?? ($info['invoice_no'] ?? '');	
				
				if (env('QUEUE_MAIL') == 'on') {
					\Config::set('queue.connections', 'central');
					dispatch(new TenantMailJob($data));
				}
				else{
					$status_id = (int) ($info['status_id'] ?? 0);
					$payment_status = (int) ($info['payment_status'] ?? 0);

					$clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
					$storeName = trim((string) ($clubInfo['club_name'] ?? ''));
					if ($storeName === '') {
						$storeName = trim((string) ($invoice_info->store_legal_name ?? ''));
					}
					if ($storeName === '') {
						$storeName = trim((string) (tenant('store_name') ?? 'Booostr'));
					}
					if ($storeName === '') {
						$storeName = 'Booostr';
					}

					if ($status_id === 2 || $payment_status === 5) {
						$mailType = 'cancelled';
						$subject = 'Cancelled Order for ' . $storeName . ' - Order #' . $invoiceNo . ' - Cancelled & Refunded';
					} elseif ($payment_status === 4) {
						$mailType = 'authorized_not_captured';
						$subject = 'New Order for ' . $storeName . ' - Order #' . $invoiceNo . ' - AUTHORIZED, NOT CAPTURED';
					} else {
						$mailType = 'captured';
						$subject = 'New Order for ' . $storeName . ' - Order #' . $invoiceNo . ' - Captured & Ready to Fulfill';
					}

					if (empty($to)) {
						$admin_details = User::where('role_id', 3)->first();
						$to = $admin_details->email ?? null;
						if (empty($to)) {
							$to = $clubInfo['club_email'] ?? ($invoice_info->store_legal_email ?? null);
						}
					}

					try {
						$mail = new Adminsendmail($info, $subject, $mailType);
						if (!empty($to)) {
							Mail::to($to)->send($mail);
						}
					} catch (\Throwable $th) {
						\Log::error('NotifyToUser sendEmail admin error: ' . $th->getMessage());
					}
				}

			}else {
				$data['to']= $to;			
				$data['from']= '';
				$data['from_name']= null;
				$data['reply_to']= null;
				$data['type']='tenant_order_notification';
				$currency=get_option('currency_info');
				$invoice_info=get_option('invoice_data',true);
				$data['currency_info']=$currency;
				$data['invoice_data']=$invoice_info;
				$data['data']=$info;
				if (!empty($receiptViewOptions)) {
					$data['receipt_view_options'] = $receiptViewOptions;
				}

				// All customer receipt emails (order placed / shipped / cancelled, etc.)
				// use the same formatted From + Reply-To as the thank-you email.
				// If it returns empty values, Orderstatusmail keeps the global MAIL_FROM_* fallback.
				if (function_exists('store_receipt_mail_from')) {
					$receiptFrom = store_receipt_mail_from();
					$data['from'] = $receiptFrom['address'] ?? '';
					$data['from_name'] = $receiptFrom['name'] ?? null;
					$data['reply_to'] = $receiptFrom['reply_to'] ?? null;
				}
				
				$status_id = (int) ($info->status_id ?? ($info['status_id'] ?? 0));
				$payment_status = (int) ($info->payment_status ?? ($info['payment_status'] ?? 0));
				$invoiceNo = $info->invoice_no ?? ($info['invoice_no'] ?? '');

				$clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
				$storeName = trim((string) ($clubInfo['club_name'] ?? ''));
				if ($storeName === '') {
					$storeName = trim((string) ($invoice_info->store_legal_name ?? ''));
				}
				if ($storeName === '') {
					$storeName = trim((string) (tenant('store_name') ?? 'Booostr'));
				}
				if ($storeName === '') {
					$storeName = 'Booostr';
				}

				$subject = '';
				if ($status_id === 2 || $payment_status === 5) {
					$subject = 'Cancelled & Refunded! - Your ' . $storeName . ' Store order: #' . $invoiceNo . ' has been cancelled.';
				} elseif ($status_id === 1) {
					$subject = "Shipped! - Your " . $storeName . " Store order: #" . $invoiceNo . " is on it's way";
				} elseif ($payment_status === 4) {
					$subject = 'We have received your ' . $storeName . ' Store order: #' . $invoiceNo;
				} elseif ($payment_status === 1 || $status_id === 3) {
					$subject = 'Thank you for your ' . $storeName . ' Store order: #' . $invoiceNo;
				} else {
					$subject = 'Order #' . $invoiceNo . ' from ' . $storeName . ' Store';
				}

				$data['subject'] = $subject;
				
				if (env('QUEUE_MAIL') == 'on') {
					\Config::set('queue.connections', 'central');
					dispatch(new TenantMailJob($data));
				}
				else{
					try {
						$mail = new Orderstatusmail($data, $subject);
						Mail::to($to)->send($mail);
					} catch (\Throwable $th) {
						\Log::error('NotifyToUser sendEmail customer error: ' . $th->getMessage());
					}
				}	
			}
		
	}

	public static function makeNotifyToAdmin($info,$mail_to,$mail_from=null,$type='tenant_order_notification',$order_status='')
	{

		$new_array = json_decode($info, true);

		$invoiceNo = $new_array['invoice_no'];
		$order_id = $new_array['id'];
		
		$data['type']='order_recived';
		
		if ($type == 'tenant_order_notification') {
			$currency=get_option('currency_info');
			$invoice_info=get_option('invoice_data',true);
			$data['currency_info']=$currency;
			$data['invoice_data']=$invoice_info;
			$data['data']=$info;
		}
		
		$data['orderno']=$invoiceNo;
		$data['tenantid']=tenant('id');
		if(!empty($order_status)){
			$data['message']=$order_status;
		}else{
			$data['message']='Your order status';
		}
		$data['link']=url('/seller/order',$order_id);		
		
		if (env('QUEUE_MAIL') == 'on') {
			\Config::set('queue.connections', 'central');
			
		    dispatch(new TenantMailJob($data));
		}
		else{
			

			$status_id = (int) ($new_array['status_id'] ?? 0);
			$payment_status = (int) ($new_array['payment_status'] ?? 0);

			$clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
			$storeName = trim((string) ($clubInfo['club_name'] ?? ''));
			if ($storeName === '') {
				$storeName = trim((string) ($invoice_info->store_legal_name ?? ''));
			}
			if ($storeName === '') {
				$storeName = trim((string) (tenant('store_name') ?? 'Booostr'));
			}
			if ($storeName === '') {
				$storeName = 'Booostr';
			}

			if ($status_id === 2 || $payment_status === 5) {
				$mailType = 'cancelled';
				$subject = 'Cancelled Order for ' . $storeName . ' - Order #' . $invoiceNo . ' - Cancelled & Refunded';
			} elseif ($payment_status === 4) {
				$mailType = 'authorized_not_captured';
				$subject = 'New Order for ' . $storeName . ' - Order #' . $invoiceNo . ' - AUTHORIZED, NOT CAPTURED';
			} else {
				$mailType = 'captured';
				$subject = 'New Order for ' . $storeName . ' - Order #' . $invoiceNo . ' - Captured & Ready to Fulfill';
			}

			if (empty($mail_to)) {
				$admin_details = User::where('role_id', 3)->first();
				$mail_to = $admin_details->email ?? null;
				if (empty($mail_to)) {
					$mail_to = $clubInfo['club_email'] ?? ($invoice_info->store_legal_email ?? null);
				}
			}

			$mail = new Adminsendmail($data, $subject, $mailType);
			if (!empty($mail_to)) {
				Mail::to($mail_to)->send($mail);
			}
		}
	

	}

	

	public static function customermail($info,$mail_to,$mail_from=null,$type='tenant_order_notification')
	{		
		$data['to']=$mail_to;
		$data['from']=$mail_from;
		$data['from_name']= null;
		$data['reply_to']= null;
		
		$data['type']=$type;
		
		if ($type == 'tenant_order_notification') {
			$currency=get_option('currency_info');
			$invoice_info=get_option('invoice_data',true);
			$data['currency_info']=$currency;
			$data['invoice_data']=$invoice_info;
			$data['data']=$info;

			// Same formatted From + Reply-To as the thank-you email for all customer receipts.
			// Empty values keep the existing global MAIL_FROM_* fallback in Orderstatusmail.
			if (function_exists('store_receipt_mail_from')) {
				$receiptFrom = store_receipt_mail_from();
				$data['from'] = $receiptFrom['address'] ?? '';
				$data['from_name'] = $receiptFrom['name'] ?? null;
				$data['reply_to'] = $receiptFrom['reply_to'] ?? null;
			}
		}

		if ($type == 'order_recived') {
			$orderinfo['orderno']=$info->invoice_no;
			$orderinfo['tenantid']=tenant('id');
			$orderinfo['message']='You have received a new order';
			$orderinfo['link']=url('/seller/order',$info->id);
			$data['data']=$orderinfo;
			
		}
		
		
		$status_id = (int) ($info->status_id ?? ($info['status_id'] ?? 0));
		$payment_status = (int) ($info->payment_status ?? ($info['payment_status'] ?? 0));
		$invoiceNo = $info->invoice_no ?? ($info['invoice_no'] ?? '');

		$clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
		$storeName = trim((string) ($clubInfo['club_name'] ?? ''));
		if ($storeName === '') {
			$storeName = trim((string) ($invoice_info->store_legal_name ?? ''));
		}
		if ($storeName === '') {
			$storeName = trim((string) (tenant('store_name') ?? 'Booostr'));
		}
		if ($storeName === '') {
			$storeName = 'Booostr';
		}

		$subject = '';
		if ($status_id === 2 || $payment_status === 5) {
			$subject = 'Cancelled & Refunded! - Your ' . $storeName . ' Store order: #' . $invoiceNo . ' has been cancelled.';
		} elseif ($status_id === 1) {
			$subject = "Shipped! - Your " . $storeName . " Store order: #" . $invoiceNo . " is on it's way";
		} elseif ($payment_status === 4) {
			$subject = 'We have received your ' . $storeName . ' Store order: #' . $invoiceNo;
		} elseif ($payment_status === 1 || $status_id === 3) {
			$subject = 'Thank you for your ' . $storeName . ' Store order: #' . $invoiceNo;
		} else {
			$subject = 'Order #' . $invoiceNo . ' from ' . $storeName . ' Store';
		}

		$data['subject'] = $subject;
		
		if (env('QUEUE_MAIL') == 'on') {
			\Config::set('queue.connections', 'central');
			dispatch(new TenantMailJob($data));
		}
		else{
			$mail = new Orderstatusmail($data, $subject);
			Mail::to($mail_to)->send($mail);
		}
		
		
	}


	public static function fmc($title,$description,$link,$icon,$user_tokens)
	{
		
        try {
        	
        	$data = [
        		"registration_ids" => $user_tokens,
        		"notification" => [
        			"title" => $title,
        			"body" => $description,
        			"icon" => $icon,
        			"click_action"=> $link

        		],

        	];


        	$dataString = json_encode($data);

        	$headers = [
        		'Authorization: key=' . env('FMC_SERVER_API_KEY'),
        		'Content-Type: application/json',
        	];

        	$ch = curl_init();

        	curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        	curl_setopt($ch, CURLOPT_POST, true);
        	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        	curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        	$response = curl_exec($ch);
        	
        	return true;

        } catch (Exception $e) {
        	return false;
        }


	}
}

?>
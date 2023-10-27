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

	public static function sendEmail($info, $to=null, $email_identity){
	    try{		
		    if($email_identity == 'admin'){
				$currency=get_option('currency_info');
				$invoice_info=get_option('invoice_data',true);
				$info['invoice_data'] = $invoice_info;
				$invoiceNo = $info['invoice_no'];	
				
				if (env('QUEUE_MAIL') == 'on') {
					\Config::set('queue.connections', 'central');
					dispatch(new TenantMailJob($data));
				}
				else{
					
					$status_id = $info['status_id'];
					$payment_status = $info['payment_status'];
					$subject = '';

					if ($status_id == 3 && $payment_status == 4) {
						$subject = $invoice_info->store_legal_name . ' You have received a new order' . ' #' . $invoiceNo . ' via your Booostr Online Store';
					} else if ($status_id == 1) {
						$subject = $invoice_info->store_legal_name . ' Order' . ' #' . $invoiceNo . ' - Completed & Shipped';
					} else if ($status_id == 2) {
						$subject = $invoice_info->store_legal_name . ' Order' . ' #' . $invoiceNo . ' - Cancelled & Refunded';
					} else if ($status_id == 4) {
						$subject = $invoice_info->store_legal_name . ' Order' . ' #' . $invoiceNo . ' - Payment Captured';
					} else {
						$subject = $invoice_info->store_legal_name . ' #' . $invoiceNo;
					}
					$mail = new Adminsendmail($info,$subject);
					Mail::to($to)->send($mail);
				}

			}else {
				$data['to']= $to;			
				$data['from']= '';			
				$data['type']='tenant_order_notification';
				$currency=get_option('currency_info');
				$invoice_info=get_option('invoice_data',true);
				$data['currency_info']=$currency;
				$data['invoice_data']=$invoice_info;
				$data['data']=$info;
				
				if (env('QUEUE_MAIL') == 'on') {
					\Config::set('queue.connections', 'central');
					dispatch(new TenantMailJob($data));
				}
				else{
					$status_id = $info->status_id;
					
					$subject = '';
					if($status_id == 3){
					$subject = 'Thank you for your '.$invoice_info->store_legal_name.' Store order: #'.$info->invoice_no;
					}else if($status_id == 1){
						$subject = "Shipped! - Your " . $invoice_info->store_legal_name . " Store order: #" . $info->invoice_no . " is on it's way";
					}else if($status_id == 2){
						$subject = 'Cancelled & Refunded! - Your ' .$invoice_info->store_legal_name . ' Store order: #'. $info->invoice_no. ' has been cancelled.';
					}else{
						$subject = 'Order Info';
					}
		
					$mail = new Orderstatusmail($data,$subject);
					Mail::to($to)->send($mail);
				}	
			}
		} catch (\Throwable $th) {
			dd($th);
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
			

			$status_id = $new_array['status_id'];
            $payment_status = $new_array['payment_status'];
            $subject = '';

			if ($status_id == 3 && $payment_status == 4) {
				$subject = $invoice_info->store_legal_name . ' You have received a new order' . ' #' . $invoiceNo . ' via your Booostr Online Store';
				//if you need payment status when shipped order then you can add this && $payment_status == 1
			} else if ($status_id == 1) {
				$subject = $invoice_info->store_legal_name . ' Order' . ' #' . $invoiceNo . ' - Completed & Shipped';
			   //if you need payment status when cancel order then you can add this && $payment_status == 5
			} else if ($status_id == 2) {
				$subject = $invoice_info->store_legal_name . ' Order' . ' #' . $invoiceNo . ' - Cancelled & Refunded';
			} else if ($status_id == 4) {
				$subject = $invoice_info->store_legal_name . ' Order' . ' #' . $invoiceNo . ' - Payment Captured';
			} else {
				$subject = $invoice_info->store_legal_name . ' #' . $invoiceNo;
			}
			
			 $mail = new Adminsendmail($data,$subject);
		
			

            Mail::to($mail_to)->send($mail);
		}
	

	}

	

	public static function customermail($info,$mail_to,$mail_from=null,$type='tenant_order_notification')
	{		
		$data['to']=$mail_to;
		$data['from']=$mail_from;
		
		$data['type']=$type;
		
		if ($type == 'tenant_order_notification') {
			$currency=get_option('currency_info');
			$invoice_info=get_option('invoice_data',true);
			$data['currency_info']=$currency;
			$data['invoice_data']=$invoice_info;
			$data['data']=$info;
		}

		if ($type == 'order_recived') {
			$orderinfo['orderno']=$info->invoice_no;
			$orderinfo['tenantid']=tenant('id');
			$orderinfo['message']='You have received a new order';
			$orderinfo['link']=url('/seller/order',$info->id);
			$data['data']=$orderinfo;
			
		}
		
		
		if (env('QUEUE_MAIL') == 'on') {
			\Config::set('queue.connections', 'central');
			
		    dispatch(new TenantMailJob($data));
		}
		else{

			$status_id = $info->status_id;
			
            $subject = '';
            if($status_id == 3){
             $subject = 'Thank you for your '.$invoice_info->store_legal_name.' Store order: #'.$info->invoice_no;
            }else if($status_id == 1){
				$subject = "Shipped! - Your " . $invoice_info->store_legal_name . " Store order: #" . $info->invoice_no . " is on it's way";
            }else if($status_id == 2){
                $subject = 'Cancelled & Refunded! - Your ' .$invoice_info->store_legal_name . ' Store order: #'. $info->invoice_no. ' has been cancelled.';
            }else{
				$subject = 'Order Info';
			}

			$mail = new Orderstatusmail($data,$subject);
		
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
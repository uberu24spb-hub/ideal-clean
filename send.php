<?php
ob_start();

//ini_set('display_errors', 'On');
//require_once('swiftmailer/swift_required.php');
//require_once('idn.php');

$res = array();
$res['results'] = array();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	try {

		$name = trim($_REQUEST['Name']);
		$phone = trim($_REQUEST['Phone']);
		$email = trim($_REQUEST['Email']);
		$msg = trim($_REQUEST['Textarea']);
		$products = json_decode($_REQUEST['tildapayment']);
		$products = $products->products;
		
		$site = $_SERVER['HTTP_HOST'];
		
		$from = array('noreply@' . $site);
		$to = file(__DIR__ . '/email.cnf');
		$to = array_map('trim', $to);
		$subject = 'Новый заказ с сайта ' . $site;
		$message = 'Поступил новый заказ с сайта ' . $site . ':
			<table>
				<tr>
					<td><b>Дата:</b></td>
					<td>' . date('d.m.Y H:i') . '</td>
				</tr>
				<tr>
					<td><b>IP:</b></td>
					<td>' . $_SERVER['REMOTE_ADDR'] . '</td>
				</tr>';
		if($name != '') {
			$message .= '<tr>
				<td><b>Имя:</b></td>
				<td>' . $name . '</td>
			</tr>';
		}
		if($phone != '') {
			$message .= '<tr>
				<td><b>Телефон:</b></td>
				<td>' . $phone . '</td>
			</tr>';
		}
		if($email != '') {
			$message .= '<tr>
				<td><b>E-mail:</b></td>
				<td>' . $email . '</td>
			</tr>';
		}
		if($msg != '') {
			$message .= '<tr>
				<td><b>Сообщение:</b></td>
				<td>' . $msg . '</td>
			</tr>';
		}
		
		if(is_array($products) && count($products) > 0) {
			$message .= '<tr>
					<td colspan="2"><b>Товары:</b></td>
				</tr>';
			foreach($products as $product) {
				$message .= '<tr>
					<td colspan="2">' . $product->name . ' - ' . $product->quantity . 'шт. - ' . $product->amount . ' р.</td>
				</tr>';
			}
		}
		
		$message .= '</table>';	

		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		$headers .= "From: " . implode(',', $from) . "\r\n";
		
		$result = mail(implode(',', $to), $subject, $message, $headers);
		
		if($result) {
			$res['message'] = 'OK';
		}
		else {
			$res['message'] = 'ERROR';
		}
	}
	catch(Exception $e) {
		$res['message'] = 'ERROR';
	}
}

ob_end_clean();

echo json_encode($res);
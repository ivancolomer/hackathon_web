<?php

namespace App\Models;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

class Mailer extends \Core\Model {

    public static function getPHPMailer($email) {

		if(Config::MAIL_AUTH_TYPE != 'XOAUTH2') 
		{
			$mailer = new PHPMailer;

			$mailer->isSMTP();

			$mailer->Host = Config::MAIL_HOST;
			$mailer->SMTPAuth = true;
			$mailer->Username = Config::MAIL_USERNAME;
			$mailer->Password = Config::MAIL_PASSWORD;
			$mailer->SMTPSecure = 'tls';
			$mailer->Port = Config::MAIL_PORT;
			$mailer->From = Config::MAIL_USERNAME;
			$mailer->FromName = Config::APP_NAME . " Team";
			$mailer->CharSet = 'UTF-8'; 

			$mailer->addAddress($email);
			$mailer->addReplyTo(Config::MAIL_USERNAME, Config::APP_NAME . " Team");
			$mailer->addBCC(Config::MAIL_USERNAME);
			$mailer->isHTML(true);
			 
			return $mailer;
		}
		
		$mail = new PHPMailer(TRUE);
		$mail->setFrom(Config::MAIL_USERNAME, Config::APP_NAME . " Team");
		$mail->addAddress($email);
		$mail->isSMTP();
		$mail->Port = 587;
		$mail->SMTPAuth = TRUE;
		$mail->SMTPSecure = 'tls';
		$mail->Host = 'smtp.gmail.com';
		$mail->AuthType = 'XOAUTH2';

		/* Pass the OAuth provider instance to PHPMailer. */
		$mail->setOAuth(new OAuth([
			'provider' => new Google([
				'clientId' => Config::MAIL_CLIENT_ID,
				'clientSecret' => Config::MAIL_CLIENT_SECRET
			]),
			'clientId' => Config::MAIL_CLIENT_ID,
			'clientSecret' => Config::MAIL_CLIENT_SECRET,
			'refreshToken' => Config::MAIL_REFRESH_TOKEN,
			'userName' => Config::MAIL_USERNAME
		]));
		
		return $mail;
    }
}
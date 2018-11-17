<?php

namespace App;

class Config {
    
	const APP_NAME = 'HACKEPS';
    const DNS_NAME = '192.168.4.1';
    const USE_HTTPS = false;

    const DB_HOST = 'localhost';
    const DB_NAME = 'hackeps';
    const DB_USER = 'eps';
    const DB_PASSWORD = 'HackEPS_2018';

    const MAIL_HOST = 'smtp.gmail.com';
    const MAIL_USERNAME = 'username@gmail.com';
    const MAIL_PASSWORD = 'password';
    const MAIL_PORT = 587;
	
	const MAIL_AUTH_TYPE = 'XOAUTH2';
	const MAIL_CLIENT_ID = '';
	const MAIL_CLIENT_SECRET = '';
	const MAIL_REFRESH_TOKEN = '';

    const CAPTCHA_SITE_KEY = 'google_captcha_invisible_site_key';
    const CAPTCHA_SECRET = 'google_captcha_invisible_secret_key';

    const SHOW_ERRORS = false;
}
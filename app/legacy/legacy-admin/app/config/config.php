<?php

global $sysConfig;

$sysConfig = [
    'name' => 'Gi&Ba',
    'name_full' => 'Gi&Ba di Shipping Dog SNC',
    'desc_it' => 'Abbigliamento e accessori personalizzati',
    'desc_shr_it' => 'Abbigliamento e accessori personalizzati',
    'desc_en' => '',
    'desc_shr_en' => '',
    'desc_pt' => '',
    'desc_shr_pt' => '',

    // language
    'languages' => [
        'it' => 'Italiano',
        // 'en' => 'English',
        // 'de' => 'Deutsch',
        // 'pt' => 'Português',
        // 'es' => 'Español',
    ],
    'default-language' => 'it',

    'logo' => '/app/resources/logo/logo.jpg',
    'VAT' => '02397230224',

    'cont_fb' => 'https://www.facebook.com/GieBaPromotion',
    'cont_fb_link' => 'https://www.facebook.com/GieBaPromotion',
    'cont_inst' => '',
    'cont_inst_link' => '',
    'cont_yt' => '',
    'cont_sp' => '',
    'cont_twit' => '',
    'cont_twit_link' => '',
    'cont_mail' => 'giebapromotion@gmail.com',
    'cont_mail_link' => 'mailto:giebapromotion@gmail.com',
    'cont_tel' => '0462 230218',
    'cont_tel_link' => 'tel:0462230218',
    'cont_whats' => '3517419263',
    'cont_whats_link' => 'https://wa.me/3517419263',
    'cont_tele' => '',
    'cont_pin' => '',
    'cont_pin_link' => '',
    'cont_tiktok' => '',
    'cont_tiktok_link' => '',
    'cont_web_link' => '',

    'google_analytics' => false,

    'timezone' => 'Europe/Rome',

    'theme-color' => 'dark',
];

if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    $sysConfig['hostname'] = 'localhost';
    $sysConfig['database'] = 'hostinger_gieba';
    $sysConfig['username'] = 'root';
    $sysConfig['password'] = '';
} else {
    $sysConfig['hostname'] = 'localhost';
    $sysConfig['database'] = 'u944138369_gieba';
    $sysConfig['username'] = 'u944138369_gieba';
    $sysConfig['password'] = 'O3a=H3lvJ';
}

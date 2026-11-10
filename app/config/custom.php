<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Description
    |--------------------------------------------------------------------------
    |
    | The description used for the snippet displayed in search engines.
    |
    */

    'description' => 'Abbigliamento e accessori personalizzati',

    /*
    |--------------------------------------------------------------------------
    | Application Short Description
    |--------------------------------------------------------------------------
    |
    | The description used in the title of the landing page.
    |
    */

    'short_description' => 'Abbigliamento e accessori personalizzati',

    /*
    |--------------------------------------------------------------------------
    | Default Color Scheme
    |--------------------------------------------------------------------------
    |
    | The default color scheme for the application.
    | Can be 'light', 'dark', or 'auto'.
    | I can be overridden by the user in the settings.
    |
    */

    'default_color_scheme' => 'light',

    /*
    |--------------------------------------------------------------------------
    | Header navigation Links
    |--------------------------------------------------------------------------
    |
    |
    */

    'navigation' => [
        'header' => [
            ['label' => 'Home', 'route' => 'home', 'main_route' => 'home'],
        ],

        'footer' => [
            ['label' => 'Home', 'route' => 'home', 'main_route' => 'home'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Contacts
    |--------------------------------------------------------------------------
    */

    'contacts' => [
        'instagram' => ['url' => 'https://www.instagram.com/tag/', 'label' => '@tag'],
        'facebook' => ['url' => 'https://www.facebook.com/GieBaPromotion', 'label' => '@GieBaPromotion'],
        'email' => ['url' => 'mailto:giebapromotion@gmail.com', 'label' => 'giebapromotion@gmail.com'],
        'whatsapp' => ['url' => 'https://wa.me/3517419263', 'label' => '351 7419263'],
        'phone' => ['url' => 'tel:3517419263', 'label' => '351 7419263'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Emails
    |--------------------------------------------------------------------------
    |
    | The email addresses to be notified on certain events.
    | E.g., when a new user registers, a new newsletter subscription is made, etc.
    |
    */
    'notification_emails' => config('app.env') === 'production'
        ? ['giebapromotion@gmail.com'] : ['ismaele.tuffanelli@gmail.com'],
];

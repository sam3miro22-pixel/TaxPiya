<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Organization details
    |--------------------------------------------------------------------------
    |
    | Specify your Organization details for branding.
    |
    */

    'org' => [
        'name' => 'MyCompany',
        'email' => 'info@mycompany.com',
        'phone' => '+1234567890',
        'tagline' => 'My Awesome App',
    ],

    /*
    |--------------------------------------------------------------------------
    | Customize App Footer
    |--------------------------------------------------------------------------
    |
    | Customize the App Footer. Leave footer_links array empty if you don't want
    | any links in your footer, like so: 'footer_links' => [],
    |
    */
    'myapp' => [
        'footer_copyright' => '© {APPNAME}. All rights reserved. Year {YEAR}.',
        'footer_links' => [
            'About' => 'info/about',
            'FAQ' => 'info/faq',
            'Contact' => 'info/contact',
            'Privacy Policy' => 'info/privacypolicy',
            'Terms and Conditions' => 'info/termsandconditions',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Customize Reports
    |--------------------------------------------------------------------------
    |
    | Customize the look and feel of the Report (Print and PDF).
    |
    */
    'report' => [

        'include_header' => 'yes',
        'include_footer' => 'yes',
        'style' => [
            'font_name' => '"Courier New", Courier, monospace',
            'font_size' => '13px',
        ],
        'footer_content' => '© MyCompany All rights reserved. {YEAR}',
    ],

    /*
    |--------------------------------------------------------------------------
    | JS and CSS
    |--------------------------------------------------------------------------
    |
    | Specify the JS and CSS libraries that are to be included in the application.
    | jslib and cslib are array. So, please maintain the syntax.
    |
    */

    'jslib' => [
        //'jquery' => 'https://code.jquery.com/jquery-3.6.0.min.js',
    ],

    'csslib' => [
        //'bootstrap' => 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css',
    ],
];

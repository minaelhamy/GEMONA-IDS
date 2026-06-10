<?php


return [
    'title'       => 'ShopKing ইনস্টলার',
    'next'        => 'পরবর্তী ধাপ',
    'welcome'     => [
        'templateTitle' => 'স্বাগতম',
        'title'         => 'ShopKing ইনস্টলার',
        'message'       => 'সহজ ইনস্টলেশন এবং সেটআপ উইজার্ড।',
        'next'          => 'পরিশীলন প্রয়োজনীয়তা',
    ],
    'requirement' => [
        'templateTitle' => 'ধাপ ১ | সার্ভারের প্রয়োজনীয়তা',
        'title'         => 'সার্ভার প্রয়োজনীয়তা',
        'next'          => 'অনুমতিসমূহ পরীক্ষা করুন',
        'version'       => 'সংস্করণ',
        'required'      => 'প্রয়োজনীয়'
    ],
    'permission'  => [
        'templateTitle'       => 'ধাপ ২ | অনুমতি',
        'title'               => 'অনুমতি',
        'next'                => 'সাইট সেটআপ',
        'permission_checking' => 'অনুমতি পরীক্ষা'
    ],
    'license' => [
        'templateTitle'       => 'ধাপ ৩ | লাইসেন্স',
        'title'               => 'লাইসেন্স সেটআপ',
        'next'                => 'সাইট সেটআপ',
        'active_process'      => 'সক্রিয় প্রক্রিয়া',
        'label'               => [
            'license_code' => 'লাইসেন্স কোড'
        ]
    ],

    'site'        => [
        'templateTitle' => 'ধাপ ৪ | সাইট সেটআপ',
        'title'         => 'সাইট সেটআপ',
        'next'          => 'ডাটাবেস সেটআপ',
        'label'         => [
            'app_name' => 'অ্যাপ নাম',
            'app_url'  => 'অ্যাপ URL',
        ]
    ],
    'database'    => [
        'templateTitle'            => 'ধাপ ৫ | ডাটাবেস সেটআপ',
        'title'                    => 'ডাটাবেস সেটআপ',
        'next'                     => 'অবশ্যই সেটআপ',
        'fail_message'             => 'ডাটাবেসে সংযোগ নেই।',
        'fail_mysql_version'       => 'MySQL সংস্করণ 8.0 বা তার পরের ব্যবহার করুন।',
        'fail_mariadb_version'     => 'MySQL সংস্করণ 10.2 বা তার পরের ব্যবহার করুন।',
        'fail_postgresql_version'  => 'MySQL সংস্করণ 9.4 বা তার পরের ব্যবহার করুন।',
        'fail_sqlserver_version'   => 'MySQL সংস্করণ 2008 বা তার পরের ব্যবহার করুন।',
        'fail_singlestore_version' => 'MySQL সংস্করণ 8.1 বা তার পরের ব্যবহার করুন।',
        'label'                    => [
            'database_connection' => 'ডাটাবেস সংযোগ',
            'database_host'       => 'ডাটাবেস হোস্ট',
            'database_port'       => 'ডাটাবেস পোর্ট',
            'database_name'       => 'ডাটাবেস নাম',
            'database_username'   => 'ডাটাবেস ব্যবহারকারীর নাম',
            'database_password'   => 'ডাটাবেস পাসওয়ার্ড',
        ]
    ],
    'final'       => [
        'templateTitle'   => 'ধাপ ৬ | চেষ্টা সেটআপ',
        'title'           => 'চেষ্টা সেটআপ',
        'success_message' => 'অ্যাপ্লিকেশনটি সফলভাবে ইনস্টল হয়েছে।',
        'login_info'      => 'লগইন তথ্য',
        'email'           => 'ইমেল',
        'password'        => 'পাসওয়ার্ড',
        'email_info'      => 'admin@example.com',
        'password_info'   => '123456',
        'next'            => 'শেষ',
    ],
    'installed'   => [
        'success_log_message' => 'ShopKing ইনস্টলার সফলভাবে ইনস্টল করা হয়েছে ',
        'update_log_message'  => 'ShopKing ইনস্টলার সফলভাবে আপডেট হয়েছে ',
    ],
];
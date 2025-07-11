<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'GPCA Registration'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://127.0.0.1:8000'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Dubai',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */
        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
    ])->toArray(),

    'countries' => [
        'Afghanistan',
        'Albania',
        'Algeria',
        'Andorra',
        'Angola',
        'Antigua and Barbuda',
        'Argentina',
        'Armenia',
        'Australia',
        'Austria',
        'Azerbaijan',

        'The Bahamas',
        'Bahrain',
        'Bangladesh',
        'Barbados',
        'Belarus',
        'Belgium',
        'Belize',
        'Benin',
        'Bhutan',
        'Bolivia',
        'Bosnia and Herzegovina',
        'Botswana',
        'Brazil',
        'Brunei',
        'Bulgaria',
        'Burkina Faso',
        'Burundi',

        'Cabo Verde',
        'Cambodia',
        'Cameroon',
        'Canada',
        'Central African Republic',
        'Chad',
        'Chile',
        'China',
        'Colombia',
        'Comoros',
        'Congo, Democratic Republic of the',
        'Congo, Republic of the',
        'Costa Rica',
        'Côte d’Ivoire',
        'Croatia',
        'Cuba',
        'Cyprus',
        'Czech Republic',


        'Denmark',
        'Djibouti',
        'Dominica',
        'Dominican Republic',

        'East Timor (Timor-Leste)',
        'Ecuador',
        'Egypt',
        'El Salvador',
        'Equatorial Guinea',
        'Eritrea',
        'Estonia',
        'Eswatini',
        'Ethiopia',

        'Fiji',
        'Finland',
        'France',

        'Gabon',
        'The Gambia',
        'Georgia',
        'Germany',
        'Ghana',
        'Greece',
        'Grenada',
        'Guatemala',
        'Guinea',
        'Guinea-Bissau',
        'Guyana',

        'Haiti',
        'Honduras',
        'Hungary',

        'Iceland',
        'India',
        'Indonesia',
        'Iran',
        'Iraq',
        'Ireland',
        'Israel',
        'Italy',

        'Jamaica',
        'Japan',
        'Jordan',

        'Kazakhstan',
        'Kenya',
        'Kiribati',
        'Korea, North',
        'Korea, South',
        'Kosovo',
        'Kuwait',
        'Kyrgyzstan',

        'Laos',
        'Latvia',
        'Lebanon',
        'Lesotho',
        'Liberia',
        'Libya',
        'Liechtenstein',
        'Lithuania',
        'Luxembourg',

        'Madagascar',
        'Malawi',
        'Malaysia',
        'Maldives',
        'Mali',
        'Malta',
        'Marshall Islands',
        'Mauritania',
        'Mauritius',
        'Mexico',
        'Micronesia, Federated States of',
        'Moldova',
        'Monaco',
        'Mongolia',
        'Montenegro',
        'Morocco',
        'Mozambique',
        'Myanmar (Burma)',

        'Namibia',
        'Nauru',
        'Nepal',
        'Netherlands',
        'New Zealand',
        'Nicaragua',
        'Niger',
        'Nigeria',
        'North Macedonia',
        'Norway',


        'Oman',

        'Pakistan',
        'Palau',
        'Panama',
        'Papua New Guinea',
        'Paraguay',
        'Peru',
        'Philippines',
        'Poland',
        'Portugal',

        'Qatar',

        'Romania',
        'Russia',
        'Rwanda',

        'Saint Kitts and Nevis',
        'Saint Lucia',
        'Saint Vincent and the Grenadines',
        'Samoa',
        'San Marino',
        'Sao Tome and Principe',
        'Saudi Arabia',
        'Senegal',
        'Serbia',
        'Seychelles',
        'Sierra Leone',
        'Singapore',
        'Slovakia',
        'Slovenia',
        'Solomon Islands',
        'Somalia',
        'South Africa',
        'Spain',
        'Sri Lanka',
        'Sudan',
        'Sudan, South',
        'Suriname',
        'Sweden',
        'Switzerland',
        'Syria',

        'Taiwan',
        'Tajikistan',
        'Tanzania',
        'Thailand',
        'Togo',
        'Tonga',
        'Trinidad and Tobago',
        'Tunisia',
        'Turkey',
        'Turkmenistan',
        'Tuvalu',

        'Uganda',
        'Ukraine',
        'United Arab Emirates',
        'United Kingdom',
        'United States',
        'Uruguay',
        'Uzbekistan',

        'Vanuatu',
        'Vatican City',
        'Venezuela',
        'Vietnam',

        'Yemen',

        'Zambia',
        'Zimbabwe',
    ],

    'companySectors' => [
        'Academia / Educational & Research Institutes / Universities',
        'Brand owners',
        'Catalyst or Additive Manufacturers ',
        'Chemical / Petrochemical Producers    ',
        'Chemical Traders / Distributors ',
        'Engineering Company / EPC Contractors',
        'Equipment Manufacturers',
        'Governments & Regulators',
        'Industry Associations',
        'Investment / Financial / Audit / Insurance Firms',
        'Legal firms',
        'Logistics Service Providers',
        'NGOs',
        'Oil & Gas (Upstream) ',
        'Petroleum Producers / Refineries / Gas processing plants',
        'Plastics Convertors',
        'Power & Utilities',
        'Press/media ',
        'Retailers',
        'Shipping Lines',
        'Strategy Consultancies ',
        'Technology Consultancies',
        'Technology Services Providers',
        'Terminal Operators',
        'Venture Capitalists ',
        'Waste Management & Recycling',
        'Others',
    ],

    'salutations' => [
        'Mr.',
        'Mrs.',
        'Ms.',
        'Dr.',
        'Eng.',
        'Prof.'
    ],

    'eventCategories' => [
        'SCC' => "01",
        'PC' => "02",
        'ANC' => "03",
        'RIC' => "04",
        'RCC' => "05",
        'AF' => "06",
        'GLF' => "07",
        'PSC' => "08",

        'SCEA' => "11",
        'RCCA' => "51",
        'RCCW1' => "52",
        'RCW' => "53",
        'AFS' => "61",
        'AFV' => "62",
        'IPAW' => "20",
        'PSW' => "21",
        'DAW' => "22",
        'DFCLW1' => "23",
        'CAIPW1' => "24",
        'PSTW' => "25",
    ],

    'bankDetails' => [
        'AF' => [
            'accountNumber' => "0190-00-05007-7",
            'ibanNumber' => 'AE360330000019000050077',
        ],
        'DEFAULT' => [
            'accountNumber' => "0104-48-47064-5",
            'ibanNumber' => 'AE290330000010448470645',
        ],
    ],

    'ccEmailNotif' => [
        'default' => ['analee@gpca.org.ae', 'jovelyn@gpca.org.ae', 'yousif@gpca.org.ae', 'forumregistration@gpca.org.ae'],
        'test' => ['aj@gpca.org.ae'],
        'daw' => ['aastha@gpca.org.ae', 'analee@gpca.org.ae', 'jovelyn@gpca.org.ae', 'forumregistration@gpca.org.ae'],
        'scea' => ['analee@gpca.org.ae', 'zaman@gpca.org.ae'],
        'error' => ['forumregistration@gpca.org.ae'],
        'glf' => ['jovelyn@gpca.org.ae'],
    ],

    // 'ccEmailNotif' => [
    //     'default' => ['aj@gpca.org.ae'],
    //     'test' => ['aj@gpca.org.ae'],
    //     'daw' => ['aj@gpca.org.ae'],
    //     'scea' => ['aj@gpca.org.ae'],
    //     'error' => ['aj@gpca.org.ae'],
    //     'glf' => ['aj@gpca.org.ae'],
    // ],

    'rccAwardsCategories' => [
        '2023' => [
            'Process Safety' => null,
            'Sustainable Environmental Protection' => [
                'Sustainable Energy',
                'Green Technology',
                'Conservation and Biodiversity',
                'Waste Reduction and Management',
            ],
            'Rising Star Award' => null,
            'Community Awareness' => [
                'Environmental Stewardship',
                'Community Engagement and Partnerships',
                'Health and Safety',
            ],
            'Responsible Partner' => null,
        ],
        '2025' => [
            'Process Safety' => null,
            'Sustainable Environmental Protection' => [
                'Sustainable Energy',
                'Green Technology',
                'Conservation and Biodiversity',
                'Waste Reduction and Management',
            ],
            'Rising Star Award' => null,
            'Community Awareness' => [
                'Environmental Stewardship',
                'Community Engagement and Partnerships',
                'Health and Safety',
            ],
            'Best Contractor Award' => null,
        ],
    ],

    'sccAwardsCategories' => [
        'Supply Chain Innovation Award',
        'Best LSP of the Year',
        'Excellence in Sustainability Award',
        'Women in Supply Chain Award',
    ],

    'scanTimings' => [
        '2024' => [
            'ANC' => [
                'Pre-Conference - September 10, 2024' => [
                    'Morning' => [
                        'start_time' => "08:00:00",
                        'end_time' => "10:35:00",
                        'date' => '2024-09-10', //Year-Month-Day
                    ],
                    'After Networking break' => [
                        'start_time' => "11:25:00",
                        'end_time' => "12:00:00",
                        'date' => '2024-09-10',
                    ],
                    'After Lunch break' => [
                        'start_time' => "13:00:00",
                        'end_time' => "15:00:00",
                        'date' => '2024-09-10',
                    ],
                ],
                'Day 1 - September 11, 2024' => [
                    'Morning' => [
                        'start_time' => "08:00:00",
                        'end_time' => "11:00:00",
                        'date' => '2024-09-11',
                    ],
                    'After Networking break' => [
                        'start_time' => "11:45:00",
                        'end_time' => "13:20:00",
                        'date' => '2024-09-11',
                    ],
                    'After Lunch break' => [
                        'start_time' => "14:30:00",
                        'end_time' => "15:55:00",
                        'date' => '2024-09-11',
                    ],
                ],
                'Day 2 - September 12, 2024' => [
                    'Morning' => [
                        'start_time' => "08:00:00",
                        'end_time' => "11:10:00",
                        'date' => '2024-09-12',
                    ],
                    'After Networking break' => [
                        'start_time' => "11:20:00",
                        'end_time' => "15:00:00",
                        'date' => '2024-09-12',
                    ],
                ],
            ],
            'PSC' => [
                'Pre-Conference - October 7, 2024' => [
                    'Whole day' => [
                        'start_time' => "07:30:00",
                        'end_time' => "16:00:00",
                        'date' => '2024-10-07',
                    ],
                ],
                'Day 1 - October 8, 2024' => [
                    'Morning' => [
                        'start_time' => "07:30:00",
                        'end_time' => "10:45:00",
                        'date' => '2024-10-08',
                    ],
                    'After Networking break - Technical presentations' => [
                        'start_time' => "11:20:00",
                        'end_time' => "12:45:00",
                        'date' => '2024-10-08',
                    ],
                    'After Lunch break - Technical presentations' => [
                        'start_time' => "13:30:00",
                        'end_time' => "15:30:00",
                        'date' => '2024-10-08',
                    ],
                ],
                'Day 2 - October 9, 2024' => [
                    'Morning' => [
                        'start_time' => "07:30:00",
                        'end_time' => "09:55:00",
                        'date' => '2024-10-09',
                    ],
                    'After Networking break - Technical presentations' => [
                        'start_time' => "10:30:00",
                        'end_time' => "12:35:00",
                        'date' => '2024-10-09',
                    ],
                    'After Lunch break - Technical presentations' => [
                        'start_time' => "13:25:00",
                        'end_time' => "15:45:00",
                        'date' => '2024-10-09',
                    ],
                ],
                'Day 3 - October 10, 2024' => [
                    'Morning' => [
                        'start_time' => "07:30:00",
                        'end_time' => "09:45:00",
                        'date' => '2024-10-09',
                    ],
                    'After Networking break - Technical presentations' => [
                        'start_time' => "10:05:00",
                        'end_time' => "12:20:00",
                        'date' => '2024-10-09',
                    ],
                ],
            ],
        ],
    ],
];

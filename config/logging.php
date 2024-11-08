<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

         //  login Logs channels
         'super_admin_log' => [
            'driver' => 'single',
            'path' => storage_path('logs/login_Logs/super_admin_login.log'),
            'level' => 'info',
        ],
        'login_log_companymanager' => [
            'driver' => 'single',
            'path' => storage_path('logs/login_Logs/company_manager_login.log'),
            'level' => 'info',
        ],
        'login_log_basicagent' => [
            'driver' => 'single',
            'path' => storage_path('logs/login_Logs/basic_agent_login.log'),
            'level' => 'info',
        ],
        'login_log_agent' => [
            'driver' => 'single',
            'path' => storage_path('logs/login_Logs/agent_login.log'),
            'level' => 'info',
        ],
        'login_log_superagentL' => [
            'driver' => 'single',
            'path' => storage_path('logs/login_Logs/super_agent_L_login.log'),
            'level' => 'info',
        ],
        'login_log_superagentInterested' => [
            'driver' => 'single',
            'path' => storage_path('logs/login_Logs/super_agent_interested_login.log'),
            'level' => 'info',
        ],
        'login_log_superagent' => [
            'driver' => 'single',
            'path' => storage_path('logs/login_Logs/super_agent_login.log'),
            'level' => 'info',
        ],
          // End login Logs channels
           //  Api Logs channels
           'recusive_charging_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/recusive_charging.log'),
            'level' => 'info',
        ],
        'recusive_charging_api_job' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/recusive_charging_api_job.log'),
            'level' => 'info',
        ],
        'interested_customer_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/interested_customer_api.log'),
            'level' => 'info',
        ],
        'auto_debit_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/auto_debit_api.log'),
            'level' => 'info',
        ],
        'ivr_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/ivr_api.log'),
            'level' => 'info',
        ],
        'landing_page_subscription_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/landing_page_subscription_api.log'),
            'level' => 'info',
        ],
        'net_entrollment_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/net_entrollment_api.log'),
            'level' => 'info',
        ],
        'products_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/products_api.log'),
            'level' => 'info',
        ],
        'ussd_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/ussd_api.log'),
            'level' => 'info',
        ],
        'auto_debit_reversal_inquiryi' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/auto_debit_reversal_inquiryi.log'),
            'level' => 'info',
        ],
        'auto_debit_reversal_payment_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/auto_debit_reversal_payment_api.log'),
            'level' => 'info',
        ],
        'consent_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/consent_api.log'),
            'level' => 'info',
        ],

        'bulk_sub_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/bulk_sub_api.log'),
            'level' => 'info',
        ],

        'consent_number_api' => [
            'driver' => 'single',
            'path' => storage_path('logs/Api/consent_number_api.log'),
            'level' => 'info',
        ],



           // End Api Logs channels

    ],

];

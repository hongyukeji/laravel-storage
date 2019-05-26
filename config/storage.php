<?php

return [
    'disks' => [
        'aliyun-oss' => [
            'driver' => 'aliyun-oss',
            'key' => env('ALIYUN_OSS_ACCESS_KEY_ID'),
            'secret' => env('ALIYUN_OSS_ACCESS_KEY_SECRET'),
            'region' => env('ALIYUN_OSS_DEFAULT_REGION'),
            'bucket' => env('ALIYUN_OSS_BUCKET'),
            'endpoint' => env('ALIYUN_OSS_ENDPOINT'),
        ],

        'qiniu' => [
            'driver' => 'qiniu',
            'key' => env('QINIU_ACCESS_KEY'),
            'secret' => env('QINIU_SECRET_KEY'),
            'region' => env('QINIU_DEFAULT_REGION'),
            'bucket' => env('QINIU_BUCKET'),
            'url' => env('QINIU_URL')
        ],

        'qcloud-cos' => [
            /* 驱动名称 */
            'driver' => 'qcloud-cos',
            /* 地域 */
            'region' => env('QCLOUD_COS_REGION', 'ap-shanghai'),
            /* 认证信息 */
            'credentials' => [
                'app_id' => env('QCLOUD_COS_APP_ID'),
                'secret_id' => env('QCLOUD_COS_SECRET_ID'),
                'secret_key' => env('QCLOUD_COS_SECRET_KEY'),
                'token' => env('QCLOUD_COS_TOKEN', null)
            ],
            /* 默认存储桶 */
            'default_bucket' => env('QCLOUD_COS_DEFAULT_BUCKET'),
            'timeout' => env('QCLOUD_COS_TIMEOUT', 3600),
            'connect_timeout' => env('QCLOUD_COS_CONNECT_TIMEOUT', 3600)
        ],
    ]
];

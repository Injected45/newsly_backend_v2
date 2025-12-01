<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RSS Fetcher Configuration
    |--------------------------------------------------------------------------
    */
    'rss' => [
        'default_fetch_interval' => env('RSS_FETCH_INTERVAL_SECONDS', 300),
        'max_concurrent_fetches' => env('RSS_MAX_CONCURRENT_FETCHES', 10),
        'request_timeout' => env('RSS_REQUEST_TIMEOUT', 30),
        'user_agent' => env('RSS_USER_AGENT', 'Newsly/1.0 (+https://newsly.app)'),
        
        // Retry configuration
        'max_retries' => 3,
        'retry_delay' => 60, // seconds
        'backoff_multiplier' => 2,
        
        // Rate limiting per source
        'min_fetch_interval' => 60, // minimum seconds between fetches
    ],

    /*
    |--------------------------------------------------------------------------
    | Breaking News Configuration
    |--------------------------------------------------------------------------
    */
    'breaking' => [
        'keywords' => [
            'ar' => ['عاجل', 'خبر عاجل', 'الآن', 'breaking'],
            'en' => ['breaking', 'urgent', 'just in', 'alert'],
        ],
        'auto_detect' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'batch_size' => env('FCM_BATCH_SIZE', 500),
        'retry_count' => env('FCM_RETRY_COUNT', 3),
        'queue' => env('NOTIFICATION_QUEUE', 'notifications'),
        
        // Topics naming convention
        'topics' => [
            'country_prefix' => 'country_',
            'category_prefix' => 'category_',
            'source_prefix' => 'source_',
            'breaking' => 'breaking_news',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Processing
    |--------------------------------------------------------------------------
    */
    'content' => [
        'max_title_length' => 500,
        'max_summary_length' => 1000,
        'strip_html' => true,
        'allowed_tags' => '<p><br><b><strong><i><em><ul><ol><li>',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'countries_ttl' => 3600, // 1 hour
        'categories_ttl' => 3600,
        'sources_ttl' => 1800, // 30 minutes
        'articles_ttl' => 300, // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Defaults
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'default_per_page' => 20,
        'max_per_page' => 100,
    ],
];



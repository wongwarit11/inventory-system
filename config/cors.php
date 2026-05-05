<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // อนุญาตทุกโดเมน (สำหรับทดสอบ)
    // หากต้องการระบุเฉพาะโดเมน เช่น ['http://localhost:4200']

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];

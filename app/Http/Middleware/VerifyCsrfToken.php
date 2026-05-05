<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        // คุณสามารถเพิ่ม URL ที่ต้องการยกเว้น CSRF ได้ที่นี่ เช่น:
        // 'api/*'
    ];
}

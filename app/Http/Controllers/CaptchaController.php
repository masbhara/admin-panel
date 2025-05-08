<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mews\Captcha\Facades\Captcha;

class CaptchaController extends Controller
{
    /**
     * Get captcha image as base64
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCaptcha()
    {
        $captcha = Captcha::create('default', true);
        
        return response()->json([
            'image' => $captcha['img'],
            'key' => $captcha['key']
        ]);
    }

    /**
     * Refresh captcha image
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshCaptcha() 
    {
        return $this->getCaptcha();
    }
} 
<?php

namespace App\Services\Auth\Otp\Stub;

use App\Services\Auth\Otp\OtpAuth;

/**
 * Class StubOtpAuth
 *
 * This class is just a Stub for phone otp
 *
 * @package App\Helpers
 */
class StubOtpAuth implements OtpAuth
{
    /**
     * @param string $phoneNumber
     *
     * @return string
     */
    public function generateCode(string $phoneNumber): void
    {
        return;
    }

    public function validateCode(string $phoneNumber, string $code): string
    {
        return $code === substr($phoneNumber, -6);
    }
}

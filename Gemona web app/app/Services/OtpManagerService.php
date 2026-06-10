<?php

namespace App\Services;

use App\Enums\Ask;
use Exception;
use Carbon\Carbon;
use App\Models\Otp;
use App\Enums\OtpType;
use App\Events\SendSmsCode;
use Illuminate\Http\Request;
use App\Events\SendEmailCode;
use Illuminate\Support\Facades\DB;
use App\Events\SendVerifyEmailCode;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\VerifyPhoneRequest;
use App\Libraries\QueryExceptionLibrary;

class OtpManagerService
{

    /**
     * @throws Exception
     */
    public function otpPhone(Request $request): bool
    {
        try {
            if (env('DEMO') == "True" || env('DEMO') == "TRUE" || env('DEMO') == "true" || env('DEMO') == 1) {
                return true;
            }
            $otp = DB::table('otps')->where([
                ['phone', $request->post('phone')],
                ['code', $request->post('country_code')],
            ]);

            if ($otp->exists()) {
                $otp->delete();
            }

            if (
                OtpType::SMS == Settings::group('otp')->get('otp_type') || OtpType::BOTH == Settings::group('otp')->get(
                    'otp_type'
                )
            ) {
                $token = rand(
                    pow(10, (int) Settings::group('otp')->get('otp_digit_limit') - 1),
                    pow(10, (int) Settings::group('otp')->get('otp_digit_limit')) - 1
                );
            } else {
                $token = rand(pow(10, 4 - 1), pow(10, 4) - 1);
            }

            $otp = Otp::create([
                'phone' => $request->phone,
                'code' => $request->country_code,
                'token' => $token,
                'created_at' => now(),
            ]);

            if (!blank($otp)) {
                SendSmsCode::dispatch(
                    ['phone' => $request->post('phone'), 'code' => $request->post('country_code'), 'token' => $token]
                );
            }

            return true;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function resetOtpEmail(Request $request): bool
    {
        try {
            if (env('DEMO') == "True" || env('DEMO') == "TRUE" || env('DEMO') == "true" || env('DEMO') == 1) {
                return true;
            }
            $otp = DB::table('password_reset_tokens')->where([
                ['email', $request->post('email')]
            ]);

            if ($otp->exists()) {
                $otp->delete();
            }

            if (
                OtpType::EMAIL == Settings::group('otp')->get('otp_type') || OtpType::BOTH == Settings::group('otp')->get(
                    'otp_type'
                )
            ) {
                $token = rand(
                    pow(10, (int) Settings::group('otp')->get('otp_digit_limit') - 1),
                    pow(10, (int) Settings::group('otp')->get('otp_digit_limit')) - 1
                );
            } else {
                $token = rand(pow(10, 4 - 1), pow(10, 4) - 1);
            }

            $password_reset = DB::table('password_reset_tokens')->insert([
                'email' => $request->post('email'),
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            if (!blank($password_reset)) {
                SendEmailCode::dispatch(['email' => $request->post('email'), 'pin' => $token]);
            }

            return true;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function otpEmail(Request $request): bool
    {
        try {
            if (env('DEMO') == "True" || env('DEMO') == "TRUE" || env('DEMO') == "true" || env('DEMO') == 1) {
                return true;
            }
            $otp = DB::table('password_reset_tokens')->where([
                ['email', $request->post('email')]
            ]);

            if ($otp->exists()) {
                $otp->delete();
            }

            if (
                OtpType::EMAIL == Settings::group('otp')->get('otp_type') || OtpType::BOTH == Settings::group('otp')->get(
                    'otp_type'
                )
            ) {
                $token = rand(
                    pow(10, (int) Settings::group('otp')->get('otp_digit_limit') - 1),
                    pow(10, (int) Settings::group('otp')->get('otp_digit_limit')) - 1
                );
            } else {
                $token = rand(pow(10, 4 - 1), pow(10, 4) - 1);
            }

            $password_reset = DB::table('password_reset_tokens')->insert([
                'email' => $request->post('email'),
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            if (!blank($password_reset)) {
                SendVerifyEmailCode::dispatch(['email' => $request->post('email'), 'pin' => $token]);
            }

            return true;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function verifyPhone(VerifyPhoneRequest $request): bool
    {
        try {
            if (env('DEMO') == "True" || env('DEMO') == "TRUE" || env('DEMO') == "true" || env('DEMO') == 1) {
                return true;
            }

            $otp = DB::table('otps')->where([
                ['phone', $request->post('phone')],
                ['token', $request->post('token')],
            ]);
            if ($otp->exists()) {
                $difference = (int) Carbon::now()->diffInSeconds($otp->first()->created_at, true);
                if ($difference > (int) Settings::group('otp')->get('otp_expire_time') * 60) {
                    throw new Exception(trans('all.message.code_is_expired'), 422);
                } else {
                    DB::table('otps')->where([
                        ['phone', $request->post('phone')],
                        ['token', $request->post('token')],
                    ])->update(['is_verified' => Ask::YES]);

                    return true;
                }
            } else {
                throw new Exception(trans('all.message.code_is_invalid'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function verifyEmail(VerifyEmailRequest $request): bool
    {
        try {
            if (env('DEMO') == "True" || env('DEMO') == "TRUE" || env('DEMO') == "true" || env('DEMO') == 1) {
                return true;
            }

            $verify = DB::table('password_reset_tokens')->where([
                ['email', $request->post('email')],
                ['token', $request->post('token')],
            ]);
            if ($verify->exists()) {
                DB::table('password_reset_tokens')->where([
                    ['email', $request->post('email')],
                    ['token', $request->post('token')],
                ])->update(['is_verified' => Ask::YES]);

                return true;
            } else {
                throw new Exception(trans('all.message.code_is_invalid'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}

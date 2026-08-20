<?php

namespace App\Http\Controllers\Parent\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ParentLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Login Page
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('parent.auth.login');
    }


    /*
    |--------------------------------------------------------------------------
    | Check Parent Login Details
    |--------------------------------------------------------------------------
    */

    public function checkLogin(Request $request)
    {
        $request->validate(
            [
                'login' => [
                    'required',
                    'string',
                ],

                'date_of_birth' => [
                    'required',
                    'date',
                ],
            ],
            [
                'login.required' =>
                    'Please enter your email or mobile number.',

                'date_of_birth.required' =>
                    'Please enter the student date of birth.',
            ]
        );


        /*
         * Detect whether parent entered
         * email or mobile number.
         */
        $login =
            trim($request->login);


        $isEmail =
            filter_var(
                $login,
                FILTER_VALIDATE_EMAIL
            );


        /*
         * Start Guardian query.
         */
        $guardianQuery =
            Guardian::query()
                ->where(
                    'is_active',
                    true
                );


        /*
         * Search by email.
         */
        if ($isEmail) {

            $normalizedEmail =
                Guardian::normalizeEmail(
                    $login
                );


            $guardianQuery->where(
                'normalized_email',
                $normalizedEmail
            );
        }


        /*
         * Search by mobile.
         */ else {

            $normalizedPhone =
                Guardian::normalizePhone(
                    $login
                );


            $guardianQuery->where(
                'normalized_phone',
                $normalizedPhone
            );
        }


        /*
         * Guardian must be linked
         * to a student with matching DOB.
         */
        $guardian =
            $guardianQuery
                ->whereHas(
                    'students',
                    function ($query) use ($request) {

                        $query->whereDate(
                            'date_of_birth',
                            $request->date_of_birth
                        );
                    }
                )
                ->first();


        /*
         * No valid match.
         */
        if (!$guardian) {

            return back()
                ->withInput()
                ->withErrors([
                    'login' =>
                        'The provided details could not be verified.',
                ]);
        }


        /*
         * Guardian needs an email
         * because email OTP is used first.
         */
        if (!$guardian->email) {

            return back()
                ->withInput()
                ->withErrors([
                    'login' =>
                        'No email address is registered for this guardian. Please contact the centre.',
                ]);
        }


        /*
         * Generate 6-digit OTP.
         */
        $otp =
            random_int(
                100000,
                999999
            );


        /*
         * Save OTP details temporarily
         * in Laravel session.
         */
        session([
            'parent_login_guardian_id' =>
                $guardian->id,

            'parent_login_otp_hash' =>
                Hash::make(
                    (string) $otp
                ),

            'parent_login_otp_expires_at' =>
                now()
                    ->addMinutes(5)
                    ->timestamp,

            'parent_login_otp_attempts' =>
                0,

            'parent_login_otp_destination' =>
                $guardian->email,
        ]);


        /*
         * Send OTP email.
         */
        Mail::raw(
            "Your Kumon Parent Portal verification code is {$otp}. This code will expire in 5 minutes.",
            function ($message) use ($guardian) {

                $message
                    ->to(
                        $guardian->email
                    )
                    ->subject(
                        'Kumon Parent Portal Verification Code'
                    );
            }
        );


        /*
         * Open OTP page.
         */
        return redirect()
            ->route(
                'parent.otp'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Show OTP Page
    |--------------------------------------------------------------------------
    */

    public function showOtp()
    {
        /*
         * Parent must first complete
         * the login form.
         */
        if (
            !session(
                'parent_login_guardian_id'
            )
        ) {

            return redirect()
                ->route(
                    'parent.login'
                );
        }


        $destination =
            session(
                'parent_login_otp_destination'
            );


        return view(
            'parent.auth.otp',
            compact(
                'destination'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Verify OTP
    |--------------------------------------------------------------------------
    */

    public function verifyOtp(
        Request $request
    ) {
        $request->validate(
            [
                'otp' => [
                    'required',
                    'digits:6',
                ],
            ],
            [
                'otp.required' =>
                    'Please enter the verification code.',

                'otp.digits' =>
                    'The verification code must contain 6 digits.',
            ]
        );


        /*
         * Make sure login session exists.
         */
        $guardianId =
            session(
                'parent_login_guardian_id'
            );


        $otpHash =
            session(
                'parent_login_otp_hash'
            );


        $expiresAt =
            session(
                'parent_login_otp_expires_at'
            );


        if (
            !$guardianId
            ||
            !$otpHash
            ||
            !$expiresAt
        ) {

            return redirect()
                ->route(
                    'parent.login'
                )
                ->withErrors([
                    'login' =>
                        'Your verification session has expired. Please try again.',
                ]);
        }


        /*
         * Check OTP expiry.
         */
        if (
            now()->timestamp
            >
            $expiresAt
        ) {

            $this->clearOtpSession();


            return redirect()
                ->route(
                    'parent.login'
                )
                ->withErrors([
                    'login' =>
                        'Your verification code has expired. Please sign in again.',
                ]);
        }


        /*
         * Check number of attempts.
         */
        $attempts =
            session(
                'parent_login_otp_attempts',
                0
            );


        if ($attempts >= 5) {

            $this->clearOtpSession();


            return redirect()
                ->route(
                    'parent.login'
                )
                ->withErrors([
                    'login' =>
                        'Too many incorrect verification attempts. Please sign in again.',
                ]);
        }


        /*
         * Check OTP.
         */
        if (
            !Hash::check(
                $request->otp,
                $otpHash
            )
        ) {

            session([
                'parent_login_otp_attempts'
                =>
                    $attempts + 1,
            ]);


            return back()
                ->withErrors([
                    'otp' =>
                        'The verification code is incorrect.',
                ]);
        }


        $guardian =
            Guardian::where(
                'id',
                $guardianId
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        if (!$guardian) {

            $this->clearOtpSession();


            return redirect()
                ->route(
                    'parent.login'
                )
                ->withErrors([
                    'login' =>
                        'Guardian account is not available.',
                ]);
        }


        Auth::guard(
            'parent'
        )->login(
                $guardian
            );


        $request->session()
            ->regenerate();

        $this->clearOtpSession();

        return redirect()
            ->route(
                'parent.welcome'
            );
    }


    private function clearOtpSession()
    {
        session()->forget([
            'parent_login_guardian_id',
            'parent_login_otp_hash',
            'parent_login_otp_expires_at',
            'parent_login_otp_attempts',
            'parent_login_otp_destination',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('parent')->logout();

        $request->session()
            ->forget('parent_student_id');

        $request->session()
            ->invalidate();

        $request->session()
            ->regenerateToken();


        return redirect()
            ->route('parent.login');
    }
}

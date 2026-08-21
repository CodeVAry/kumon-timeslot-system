<?php

namespace App\Http\Controllers\Parent\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin\Guardian;
use App\Models\Admin\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ParentLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Parent Login
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view(
            'parent.auth.login'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Check Email + Student DOB
    |--------------------------------------------------------------------------
    */

    public function checkLogin(
        Request $request
    ) {
        /*
         * Validate form.
         */
        $request->validate(
            [
                'login' => [
                    'required',
                    'email',
                ],

                'date_of_birth' => [
                    'required',
                    'date',
                ],
            ],
            [
                'login.required' =>
                    'Please enter your registered email address.',

                'login.email' =>
                    'Please enter a valid email address.',

                'date_of_birth.required' =>
                    'Please enter the student date of birth.',
            ]
        );


        /*
         * Normalize entered email.
         */
        $normalizedEmail =
            Guardian::normalizeEmail(
                $request->login
            );


        /*
        |--------------------------------------------------------------------------
        | Find ALL Guardian Records With Same Email
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | Guardian ID 5
        | email = parent@gmail.com
        | Student A
        |
        | Guardian ID 10
        | email = parent@gmail.com
        | Student B
        |
        | Both are treated as the same parent identity.
        |
        */

        $guardianIds =
            Guardian::where(
                'is_active',
                true
            )
                ->where(
                    'normalized_email',
                    $normalizedEmail
                )
                ->pluck('id');


        /*
         * Email does not exist.
         *
         * Keep error generic.
         */
        if ($guardianIds->isEmpty()) {

            return back()
                ->withInput()
                ->withErrors([
                    'login' =>
                        'The provided email and date of birth could not be verified.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Verify Student DOB
        |--------------------------------------------------------------------------
        |
        | The entered DOB only needs to belong to ONE student
        | connected to ANY guardian record with this email.
        |
        */

        $studentExists =
            Student::where(
                'is_active',
                true
            )
                ->whereDate(
                    'date_of_birth',
                    $request->date_of_birth
                )
                ->whereHas(
                    'guardians',
                    function ($query) use ($guardianIds) {

                        $query->whereIn(
                            'guardians.id',
                            $guardianIds
                        );
                    }
                )
                ->exists();


        /*
         * DOB does not match any linked child.
         */
        if (!$studentExists) {

            return back()
                ->withInput()
                ->withErrors([
                    'login' =>
                        'The provided email and date of birth could not be verified.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Select Guardian Record For Authentication
        |--------------------------------------------------------------------------
        |
        | We need one Guardian model for Laravel's parent guard.
        |
        | The email remains the real parent identity.
        |
        */

        $guardian =
            Guardian::whereIn(
                'id',
                $guardianIds
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        if (!$guardian) {

            return back()
                ->withInput()
                ->withErrors([
                    'login' =>
                        'The guardian account is not available.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Generate OTP
        |--------------------------------------------------------------------------
        */

        $otp =
            random_int(
                100000,
                999999
            );


        /*
        |--------------------------------------------------------------------------
        | Store OTP Temporarily In Session
        |--------------------------------------------------------------------------
        */

        session([
            /*
             * Guardian used for Laravel login.
             */
            'parent_login_guardian_id' =>
                $guardian->id,


            /*
             * Parent identity.
             */
            'parent_login_email' =>
                $normalizedEmail,


            /*
             * Secure OTP hash.
             */
            'parent_login_otp_hash' =>
                Hash::make(
                    (string) $otp
                ),


            /*
             * OTP expires after 5 minutes.
             */
            'parent_login_otp_expires_at' =>
                now()
                    ->addMinutes(5)
                    ->timestamp,


            /*
             * Incorrect attempt counter.
             */
            'parent_login_otp_attempts' =>
                0,


            /*
             * Used on OTP page.
             */
            'parent_login_otp_destination' =>
                $normalizedEmail,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Send OTP
        |--------------------------------------------------------------------------
        */

        Mail::raw(
            "Your Kumon Parent Portal verification code is {$otp}. This code will expire in 5 minutes.",
            function ($message) use ($normalizedEmail) {

                $message
                    ->to(
                        $normalizedEmail
                    )
                    ->subject(
                        'Kumon Parent Portal Verification Code'
                    );
            }
        );


        /*
         * Go to OTP page.
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
        if (
            !session(
                'parent_login_guardian_id'
            )
            ||
            !session(
                'parent_login_email'
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


        /*
         * Mask email.
         *
         * Example:
         * ariyan@gmail.com
         * becomes
         * ar***@gmail.com
         */
        $maskedDestination =
            $this->maskEmail(
                $destination
            );


        return view(
            'parent.auth.otp',
            compact(
                'maskedDestination'
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


        $guardianId =
            session(
                'parent_login_guardian_id'
            );


        $parentEmail =
            session(
                'parent_login_email'
            );


        $otpHash =
            session(
                'parent_login_otp_hash'
            );


        $expiresAt =
            session(
                'parent_login_otp_expires_at'
            );


        /*
         * Verification session missing.
         */
        if (
            !$guardianId
            ||
            !$parentEmail
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
                        'Your verification session has expired. Please sign in again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Check OTP Expiry
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | Check Attempts
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | Check OTP
        |--------------------------------------------------------------------------
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


        /*
        |--------------------------------------------------------------------------
        | Get Guardian For Laravel Authentication
        |--------------------------------------------------------------------------
        */

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
                        'The guardian account is not available.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Authenticate Parent
        |--------------------------------------------------------------------------
        */

        Auth::guard(
            'parent'
        )->login(
            $guardian
        );


        /*
         * Regenerate session ID.
         */
        $request
            ->session()
            ->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Keep Parent Email Identity
        |--------------------------------------------------------------------------
        |
        | This is the important value used by the Parent Portal.
        |
        | Every guardian record with this normalized email
        | belongs to the current parent identity.
        |
        */

        session([
            'parent_auth_email' =>
                $parentEmail,
        ]);


        /*
         * Clear temporary OTP values.
         */
        $this->clearOtpSession();


        /*
         * Remove previously selected child.
         */
        session()->forget(
            'parent_student_id'
        );


        return redirect()
            ->route(
                'parent.welcome'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(
        Request $request
    ) {
        Auth::guard(
            'parent'
        )->logout();


        $request
            ->session()
            ->forget([
                'parent_student_id',
                'parent_auth_email',
            ]);


        $request
            ->session()
            ->invalidate();


        $request
            ->session()
            ->regenerateToken();


        return redirect()
            ->route(
                'parent.login'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Clear Temporary OTP Session
    |--------------------------------------------------------------------------
    */

    private function clearOtpSession()
    {
        session()->forget([
            'parent_login_guardian_id',
            'parent_login_email',
            'parent_login_otp_hash',
            'parent_login_otp_expires_at',
            'parent_login_otp_attempts',
            'parent_login_otp_destination',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Mask Email
    |--------------------------------------------------------------------------
    */

    private function maskEmail(
        $email
    ) {
        if (
            !$email
            ||
            !str_contains(
                $email,
                '@'
            )
        ) {
            return $email;
        }


        [$name, $domain] =
            explode(
                '@',
                $email,
                2
            );


        $visible =
            mb_substr(
                $name,
                0,
                min(
                    2,
                    mb_strlen($name)
                )
            );


        return
            $visible
            .
            str_repeat(
                '*',
                max(
                    3,
                    mb_strlen($name) - 2
                )
            )
            .
            '@'
            .
            $domain;
    }
}

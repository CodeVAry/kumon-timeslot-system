<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view(
            'auth.register'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Start Registration
    |--------------------------------------------------------------------------
    |
    | Do NOT create the user yet.
    |
    | First:
    |
    | 1. Validate registration
    | 2. Generate OTP
    | 3. Store temporary registration in session
    | 4. Email OTP
    |
    */

    public function store(
        Request $request
    ) {
        $validated =
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::defaults(),
                ],

                'phone' => [
                    'nullable',
                    'string',
                    'max:30',
                ],
            ]);


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
        | Store Temporary Registration
        |--------------------------------------------------------------------------
        |
        | Password is already hashed before
        | storing it in session.
        |
        | OTP is also hashed.
        |
        */

        session([
            'registration_pending' => [
                'name' =>
                    trim(
                        $validated[
                            'name'
                        ]
                    ),

                'email' =>
                    strtolower(
                        trim(
                            $validated[
                                'email'
                            ]
                        )
                    ),

                'phone' =>
                    !empty(
                        $validated[
                            'phone'
                        ]
                    )
                        ? trim(
                            $validated[
                                'phone'
                            ]
                        )
                        : null,

                'password' =>
                    Hash::make(
                        $validated[
                            'password'
                        ]
                    ),

                'otp_hash' =>
                    Hash::make(
                        (string)
                        $otp
                    ),

                'otp_expires_at' =>
                    now()
                        ->addMinutes(10)
                        ->timestamp,

                'otp_sent_at' =>
                    now()
                        ->timestamp,
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Send OTP
        |--------------------------------------------------------------------------
        */

        try {

            $this->sendOtpEmail(
                $validated[
                    'email'
                ],
                $validated[
                    'name'
                ],
                $otp
            );

        } catch (\Throwable $exception) {

            session()->forget(
                'registration_pending'
            );


            report(
                $exception
            );


            return back()
                ->withInput(
                    $request->except([
                        'password',
                        'password_confirmation',
                    ])
                )
                ->with(
                    'error',
                    'Unable to send the verification code. Please try again.'
                );
        }


        return redirect()
            ->route(
                'register.otp'
            )
            ->with(
                'success',
                'A 6-digit verification code has been sent to your email.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | OTP Page
    |--------------------------------------------------------------------------
    */

    public function showOtp()
    {
        $pending =
            session(
                'registration_pending'
            );


        if (!$pending) {

            return redirect()
                ->route(
                    'register'
                )
                ->with(
                    'error',
                    'Please complete the registration form first.'
                );
        }


        return view(
            'auth.register-otp',
            [
                'email' =>
                    $pending[
                        'email'
                    ],
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Verify Registration OTP
    |--------------------------------------------------------------------------
    */

    public function verifyOtp(
        Request $request
    ) {
        $validated =
            $request->validate([
                'otp' => [
                    'required',
                    'digits:6',
                ],
            ]);


        $pending =
            session(
                'registration_pending'
            );


        if (!$pending) {

            return redirect()
                ->route(
                    'register'
                )
                ->with(
                    'error',
                    'Your registration session has expired. Please register again.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check OTP Expiry
        |--------------------------------------------------------------------------
        */

        if (
            now()->timestamp
            >
            $pending[
                'otp_expires_at'
            ]
        ) {

            return back()
                ->withErrors([
                    'otp' =>
                        'The verification code has expired. Please request a new code.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Verify OTP
        |--------------------------------------------------------------------------
        */

        if (
            !Hash::check(
                (string)
                $validated[
                    'otp'
                ],
                $pending[
                    'otp_hash'
                ]
            )
        ) {

            return back()
                ->withErrors([
                    'otp' =>
                        'The verification code is incorrect.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Check Email Again
        |--------------------------------------------------------------------------
        |
        | Another account may theoretically
        | have been created while OTP was pending.
        |
        */

        $alreadyExists =
            User::where(
                'email',
                $pending[
                    'email'
                ]
            )
                ->exists();


        if ($alreadyExists) {

            session()->forget(
                'registration_pending'
            );


            return redirect()
                ->route(
                    'login'
                )
                ->with(
                    'error',
                    'An account with this email already exists.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Create Verified User
        |--------------------------------------------------------------------------
        */

        $user =
            User::create([
                'name' =>
                    $pending[
                        'name'
                    ],

                'email' =>
                    $pending[
                        'email'
                    ],

                'phone' =>
                    $pending[
                        'phone'
                    ],

                /*
                 * New registered staff waits
                 * for role assignment.
                 */
                'role_id' =>
                    null,

                'is_active' =>
                    true,

                /*
                 * Password is already hashed.
                 */
                'password' =>
                    $pending[
                        'password'
                    ],

                /*
                 * Email was verified by OTP.
                 */
                'email_verified_at' =>
                    now(),
            ]);


        /*
        |--------------------------------------------------------------------------
        | Remove Registration Session
        |--------------------------------------------------------------------------
        */

        session()->forget(
            'registration_pending'
        );


        event(
            new Registered(
                $user
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        Auth::login(
            $user
        );


        $request
            ->session()
            ->regenerate();


        return redirect(
            RouteServiceProvider::HOME
        )
            ->with(
                'success',
                'Your email has been verified and your account was created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Resend OTP
    |--------------------------------------------------------------------------
    */

    public function resendOtp(
        Request $request
    ) {
        $pending =
            session(
                'registration_pending'
            );


        if (!$pending) {

            return redirect()
                ->route(
                    'register'
                )
                ->with(
                    'error',
                    'Please complete the registration form again.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Resend Cooldown
        |--------------------------------------------------------------------------
        |
        | Prevent repeated email requests.
        |
        */

        $lastSentAt =
            $pending[
                'otp_sent_at'
            ]
            ?? 0;


        if (
            now()->timestamp
            -
            $lastSentAt
            <
            60
        ) {

            $secondsRemaining =
                60
                -
                (
                    now()->timestamp
                    -
                    $lastSentAt
                );


            return back()
                ->with(
                    'error',
                    'Please wait '
                    .
                    $secondsRemaining
                    .
                    ' seconds before requesting another code.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Generate New OTP
        |--------------------------------------------------------------------------
        */

        $otp =
            random_int(
                100000,
                999999
            );


        $pending[
            'otp_hash'
        ] =
            Hash::make(
                (string)
                $otp
            );


        $pending[
            'otp_expires_at'
        ] =
            now()
                ->addMinutes(10)
                ->timestamp;


        $pending[
            'otp_sent_at'
        ] =
            now()
                ->timestamp;


        session([
            'registration_pending' =>
                $pending,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Send New OTP
        |--------------------------------------------------------------------------
        */

        try {

            $this->sendOtpEmail(
                $pending[
                    'email'
                ],
                $pending[
                    'name'
                ],
                $otp
            );

        } catch (\Throwable $exception) {

            report(
                $exception
            );


            return back()
                ->with(
                    'error',
                    'Unable to send a new verification code. Please try again.'
                );
        }


        return back()
            ->with(
                'success',
                'A new verification code has been sent to your email.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Registration
    |--------------------------------------------------------------------------
    */

    public function cancelOtp()
    {
        session()->forget(
            'registration_pending'
        );


        return redirect()
            ->route(
                'register'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Send OTP Email
    |--------------------------------------------------------------------------
    */

    private function sendOtpEmail(
        string $email,
        string $name,
        int $otp
    ): void {
        Mail::raw(
            "Hello {$name},\n\n"
            .
            "Your Kumon Time Scheduling System verification code is:\n\n"
            .
            "{$otp}\n\n"
            .
            "This code will expire in 10 minutes.\n\n"
            .
            "If you did not request this account, you can ignore this email.",
            function ($message) use (
                $email
            ) {

                $message
                    ->to(
                        $email
                    )
                    ->subject(
                        'Kumon Account Verification Code'
                    );
            }
        );
    }
}

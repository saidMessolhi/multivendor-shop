<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Services\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends Controller {
    // ── Login ─────────────────────────────────────────────

    public function showLogin() {
        return view( 'auth.login' );
    }

    public function login( Request $request ) {
        $request->validate( [
            'email'    => [ 'required', 'email' ],
            'password' => [ 'required' ],
        ] );

        if ( ! Auth::attempt( $request->only( 'email', 'password' ), $request->boolean( 'remember' ) ) ) {
            return back()
            ->withInput( $request->only( 'email', 'remember' ) )
            ->withErrors( [ 'email' => 'These credentials do not match our records.' ] );
        }

        $request->session()->regenerate();

        // Merge guest cart
        app( CartService::class )->mergeGuestCart(
            session()->getId(),
            Auth::id()
        );

        return $this->redirectBasedOnRole();
    }

    // ── Register ──────────────────────────────────────────

    public function showRegister() {
        return view( 'auth.register' );
    }

    public function register( Request $request ) {
        $request->validate( [
            'name'     => [ 'required', 'string', 'max:255' ],
            'email'    => [ 'required', 'string', 'email', 'max:255', 'unique:users' ],
            'password' => [ 'required', 'confirmed', Rules\Password::defaults() ],
            'role'     => [ 'required', 'in:customer,vendor' ],
            'terms'    => [ 'accepted' ], 
        ] );

        $user = User::create( [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make( $request->password ),
        ] );

        $user->assignRole( $request->role );

        event( new Registered( $user ) );

        Auth::login( $user );

        // Merge guest cart
        app( CartService::class )->mergeGuestCart(
            session()->getId(),
            $user->id
        );

        return $this->redirectBasedOnRole();
    }

    // ── Logout ────────────────────────────────────────────

    public function logout( Request $request ) {
        Auth::guard( 'web' )->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route( 'home' );
    }

    // ── Forgot Password ───────────────────────────────────

    public function showForgotPassword() {
        return view( 'auth.forgot-password' );
    }

    public function sendResetLink( Request $request ) {
        $request->validate( [ 'email' => [ 'required', 'email' ] ] );

        $status = Password::sendResetLink( $request->only( 'email' ) );

        return $status === Password::RESET_LINK_SENT
        ? back()->with( 'status', __( $status ) )
        : back()->withErrors( [ 'email' => __( $status ) ] );
    }

    // ── Reset Password ────────────────────────────────────

    public function showResetPassword( Request $request, string $token ) {
        return view( 'auth.reset-password', [ 'token' => $token, 'email' => $request->email ] );
    }

    public function resetPassword( Request $request ) {
        $request->validate( [
            'token'    => [ 'required' ],
            'email'    => [ 'required', 'email' ],
            'password' => [ 'required', 'confirmed', Rules\Password::defaults() ],
        ] );

        $status = Password::reset(
            $request->only( 'email', 'password', 'password_confirmation', 'token' ),

            function ( User $user, string $password ) {
                $user->forceFill( [
                    'password'       => Hash::make( $password ),
                    'remember_token' => Str::random( 60 ),
                ] )->save();
                event( new PasswordReset( $user ) );
            }
        );

        return $status === Password::PASSWORD_RESET
        ? redirect()->route( 'login' )->with( 'status', __( $status ) )
        : back()->withErrors( [ 'email' => [ __( $status ) ] ] );
    }

    // ── Helpers ───────────────────────────────────────────

    private function redirectBasedOnRole() {
        $user = Auth::user();

        if ( $user->isAdmin() ) {
            return redirect()->route( 'home' )
            ->with( 'success', 'Welcome Admin! Filament panel coming soon.' );
        }

        if ( $user->isVendor() ) {
            $vendor = $user->vendor;
            if ( ! $vendor ) {
                return redirect()->route( 'vendor.apply' );
            }
            return redirect()->route( 'vendor.dashboard' );
        }

        return redirect()->intended( route( 'home' ) );
    }
}

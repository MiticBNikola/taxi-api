<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\Customer;
use App\Models\User\Driver;
use App\Models\User\Manager;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected string $redirectTo = '/home';

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse|Response
     *
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse|RedirectResponse|Response
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($loginData = $this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request, $loginData);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param Request $request
     * @return array|null
     */
    protected function attemptLogin(Request $request): ?array
    {
        $credentials = $request->only('email', 'password');

        $user = Customer::where('email', $credentials['email'])->first();
        $type = 'customer';

        if (!$user) {
            $user = Driver::where('email', $credentials['email'])->first();
            $type = 'driver';
        }
        if (!$user) {
            $user = Manager::where('email', $credentials['email'])->first();
            $type = 'manager';
        }
        if ($user && Hash::check($credentials['password'], $user->password)) {
            $user->tokens()->delete();
            $token = $user->createToken($type . 'Token');
            $tokenRecord = PersonalAccessToken::find($token->accessToken->id);
            $tokenRecord->expires_at = now()->addHours(2);
            $tokenRecord->save();
            event(new Login($type, $user, false));
            return ["user" => $user, "token" => $token->plainTextToken, "type" => $type];
        }
        return null;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param Request $request
     * @param $loginData
     * @return JsonResponse|RedirectResponse
     */
    protected function sendLoginResponse(Request $request, $loginData): JsonResponse|RedirectResponse
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $loginData)) {

            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param mixed $user
     * @return JsonResponse
     */
    protected function authenticated(Request $request, $user): JsonResponse
    {
        return response()->json($user);
    }

    /**
     * /auth-check
     *
     * Check for authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function authCheck(Request $request): JsonResponse
    {
        $type = $request->query('type', 'web');
        if (auth()->guard($type)->check()) {
            $user = auth()->guard($type)->user();

            return response()->json(["user" => $user, "type" => $type]);
        }

        return response()->json(NULL, 401);
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $type = $request->get('type', 'web');
        $user = auth()->guard($type)->user();
        if (auth()->guard($type)->check()) {
            auth()->guard($type)->user()->tokens()->delete();
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        event(new Logout('web', $user));
        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }
}

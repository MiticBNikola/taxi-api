<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\Customer;
use App\Models\User\Driver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected string $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $user->tokens()->delete();
        $token = $user->createToken($request->get('type') . 'Token');
        $tokenRecord = PersonalAccessToken::find($token->accessToken->id);
        $tokenRecord->expires_at = now()->addHours(2);
        $tokenRecord->save();

        if ($response = $this->registered($request, $user, $token->plainTextToken)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . $data['type'] . 's'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'type' => ['required', 'string', Rule::in(['customer', 'driver'])],
            'driving_license_category' => [Rule::requiredIf($data['type'] === 'driver'), 'string', Rule::in(['B', 'BE'])],
            'driving_license_number' => [Rule::requiredIf($data['type'] === 'driver'), 'numeric']
        ]);
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return StatefulGuard
     */
    protected function guard(): StatefulGuard
    {
        return Auth::guard();
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return Customer | Driver
     */
    protected function create(array $data): Customer|Driver
    {
        if ($data['type'] == 'customer') {
            return Customer::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        } else {
            return Driver::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'driving_license_category' => $data['driving_license_category'],
                'driving_license_number' => $data['driving_license_number'],
            ]);
        }
    }

    /**
     * The user has been registered.
     *
     * @param Request $request
     * @param Customer|Driver $user
     * @param string $token
     * @return mixed
     */
    protected function registered(Request $request, Customer|Driver $user, string $token): JsonResponse
    {
        return response()->json(["user" => $user, "token" => $token, "type" => $request->get('type')]);
    }
}

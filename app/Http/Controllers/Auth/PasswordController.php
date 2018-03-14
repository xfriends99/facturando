<?php namespace app\Http\Controllers\Auth;

use app\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use app\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	use ResetsPasswords;

    public function postEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $user = User::where('email', $request->email)->get()->first();

        if($user){
            $password = str_random(8);
            $user->password = bcrypt($password);
            $user->save();
            Mail::send('emails.new_password', ['user' => $user, 'password' => $password], function ($m) use ($user) {
                $m->from(env('MAIL_USERNAME'), 'Facturando');

                $m->to('argensite@gmail.com', 'argensite')->subject('Nueva contraseña!');
            });
            return redirect()->back()->with('status', 'Nueva contraseña generada satisfactoriamente');
        } else {
            return redirect()->back()->withErrors(['name' => 'Nombre de usuario invalido']);
        }
    }

	/**
	 * Create a new password controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
	 * @return void
	 */
	public function __construct(Guard $auth, PasswordBroker $passwords)
	{
		$this->auth = $auth;
		$this->passwords = $passwords;

		$this->middleware('guest');
	}

}

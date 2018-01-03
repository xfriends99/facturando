<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Request;
use Session;
use Auth;

class UsersController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function getEditProfile($id = null)
	{	

		$user = \app\User::find($id);
		if($user!=null){
			if(Auth::user()->roles_id == 1){
				$roles = \app\Role::all();
				return view('user.edit')
				->with('user',$user)
				->with('roles',$roles);
			}else{
				return view('home');
			}
		}else{
			return view('home');
		}

	}

	public function postEditProfile()
	{	

		$id = Input::get('user_id');
		if(Auth::user()->roles_id==1){
			$rules = array(
				'name' => 'required|max:255',
				'lastname' => 'required|max:255',
				'email' => 'required|email|max:255',
				
				);
		}else{
			$rules = array(
				'name' => 'required|max:255',
				'lastname' => 'required|max:255',
				'email' => 'required|email|max:255',
				'rol' => 'required',
				);
		}
		
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('profile/'.$id)
			->withErrors($validator);
		} else {

			$user = \app\User::find($id);
			if(Auth::user()->roles_id==1 && Auth::user()->id==$id){
				
				$fax = Input::get('fax');
				if(!empty($fax)){
					$user->companies->fax = Input::get('fax');
				}
				$user->companies->tax_id = Input::get('tax_id');    
				$user->companies->tel = Input::get('tel');           
				$user->companies->addresses->post_code = Input::get('post_code');
				
				$web = Input::get('web');
				if(!empty($web)){
					$user->companies->web = Input::get('web');
				}
				$user->companies->company_name = Input::get('company_name');
				$file = Request::file('logo');
				if(!empty($file)){
					$extension = $file->getClientOriginalExtension();
					Storage::disk('local')->put($file->getFilename().'.'.$extension,  File::get($file));
					$user->companies->logo = $file->getFilename().'.'.$extension;
				}
			}
			$user->name = Input::get('name');
			if(Auth::user()->roles_id==1 && Auth::user()->id!=$id){
			$user->roles_id = Input::get('rol');
			}
			$user->lastname = Input::get('lastname');
			$user->email = Input::get('email');
			$password = Input::get('password');
			if(!empty($password)){
				$user->password = bcrypt($password);
			}
			
			if($user->push()){
				Session::flash('message', 'Usuario actualizado correctamente!!');
				return Redirect::to('profile/'.$id);
			}


		}
	}


	public function getAddUser()
	{
		$roles = \app\Role::all();
		return view('user.new')->with('roles',$roles);

	}

	public function postAddUser()
	{

		$rules = array(
			'name' => 'required|max:255',
			'lastname' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:8',
			'rol' => 'required',
			);

		$id = Input::get('user_id');
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('adduser')
			->withErrors($validator);
		} else {
			$creador = \app\User::find($id);
			$user = new \app\User;

			$user->name = Input::get('name');
			$user->lastname = Input::get('lastname');
			$user->email = Input::get('email');
			$user->password = bcrypt(Input::get('password'));
			$user->is_active = 1;
			$user->roles_id = Input::get('rol');;
			$user->companies_id = $creador->companies_id;


			if($user->save()){
				Session::flash('message', 'Usuario creado correctamente!!');
				return Redirect::to('adduser');
			}

		}
	}

	public function listUser()
	{

		$usuarios = \app\User::where('is_active','=','1')
		->get();

		return view('user.userList')->with('usuarios',$usuarios);

	}

	public function deleteUser( $id = null )
	{

		$user = \app\User::find($id);
				
		if($user!=null){

			if(Auth::user()->roles_id==1){ 

				$user->is_active = 0;
				if($user->save()) {
					Session::flash('message', 'Usuario eliminado correctamente!!');
					return Redirect::to('users');
				}
			}else{
				return view('home');
			}

		}else{
			return view('home');
		}

	}


}

<?php namespace app;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password','lastname','is_active','roles_id','companies_id'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

	public function companies()
	{
		return $this->belongsTo('app\Company');
	}

	public function roles()
	{
		return $this->belongsTo('app\Role','roles_id');
	}

	public function factura()
	{
		return $this->hasMany('app\InvoiceHead', 'users_id');
	}

	public function pagos()
	{
		return $this->hasMany('app\Pago', 'users_id');
	}

	public function getPermission($type, $keyy)
    {
        if($this->permissions){
            $search = $this->permissions->filter(function($item) use($type, $keyy){
                return $item->type == $type && $item->keyy==$keyy;
            })->first();
            return $search == true;
        }
        return false;
    }

    public function getPermissionType($type)
    {
        if($this->permissions){
            $search = $this->permissions->filter(function($item) use($type){
                return $item->type == $type;
            })->first();
            return $search == true;
        }
        return false;
    }

}

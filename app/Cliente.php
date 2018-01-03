<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{

    protected $table = 'customer';

    protected $primaryKey = "id_customer";
       
    public $timestamps = false;
    
     public function corredor()
	{
		return $this->belongsTo('app\Corredor','corredores_id');
	}
     
	
}
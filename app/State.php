<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{

    protected $connection = 'mysql2';

    protected $table = 'ps_state';

    public $timestamps = false;
	
     
}
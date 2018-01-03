<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{   
        protected $connection = 'mysql2';
        
        protected $table = 'countries';
	
        public $timestamps = false;
	
}
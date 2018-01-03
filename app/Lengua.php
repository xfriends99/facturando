<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Lengua extends Model
{

    protected $connection = 'mysql2';

    protected $table = 'ps_product_lang';
    
    protected $primaryKey = "id_lang";

    public $timestamps = false;

     
}
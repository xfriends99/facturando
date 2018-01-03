<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Concepto extends Model
{


    protected $table = 'conceptos';

	protected $fillable = array('code','concepto');
	 public $timestamps = false;

}
<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

	protected $fillable = ['name', 'type', 'keyy'];

}

<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $connection = 'mysql2';

    protected $table = 'ps_order_history';

    public $timestamps = false;

    public $primaryKey = 'id_order_history';

}

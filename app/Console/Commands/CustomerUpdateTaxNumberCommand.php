<?php namespace app\Console\Commands;

use app\Cliente;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CustomerUpdateTaxNumberCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:update_customer';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update tax_numbers to int.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
	    $clients = Cliente::all();
	    foreach ($clients as $client){
	        if(!is_numeric($client->tax_number)){
	            $this->info("{$client->tax_number}");
	            preg_match_all('/[0-9]+/', $client->tax_number, $matchs);
	            $number = '';
	            if(isset($matchs[0])){
                    foreach ($matchs[0] as $m){
                        $number .= $m;
                    }
                }
                if($number!=''){
	                $client->tax_number = $number;
	                $client->save();
                }
            }
        }
	}


}

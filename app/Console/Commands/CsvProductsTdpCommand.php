<?php namespace app\Console\Commands;

use app\ProductoTDP;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CsvProductsTdpCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'csv:process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
	    $excel = $this->processTxt(storage_path('COSTOS.csv'));
        foreach ($excel as $item) {
            ProductoTDP::where('reference', $item['reference'])->update($item);
        }
	    $this->info("Archivo procesado");
	}

    public function processTxt($file)
    {
        $dat = [];
        $file = fopen($file,'r');
        $i = 1;
        $header = [];
        while ($linea = fgets($file)) {
            if($i==1){
                $header = $this->parseHeaderTxt($linea);
            } else {
                $row = $this->getData($header, $linea, ',');
                $dat[] = $row;
            }
            $i++;
        }
        return $dat;
    }

    public function parseHeaderTxt($row)
    {
        $data = preg_replace('/["\r\n]/','',$row);
        if(substr($data,-1)==';'){
            $data = substr($data, 0, strlen($data)-1);
        }
        return explode(',', $data);
    }

    public function getData($headers, $data, $delimiter = ',', $comillas = false)
    {
        $response = [];
        $i = 0;
        $data = preg_replace('/\s\s+/', ' ', $data);
        if($comillas){
            $dat2 = explode('"'.$delimiter.'"', preg_replace('/[\r\n]/','',$data));
            foreach ($dat2 as $r){
                $dat[] = str_replace('"','',$r);
            }
        } else {
            $data = preg_replace('/["\r\n]/','',$data);
            if(substr($data,-1)==';'){
                $data = substr($data, 0, strlen($data)-1);
            }
            $dat = explode($delimiter, $data);
        }
        foreach ($headers as $key => $val){
            $response[$val] = $dat[$i];
            $i++;
        }
        return $response;
    }
}

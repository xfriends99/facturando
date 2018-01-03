<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Response;
use Session;
use Auth;
use Request;

class CajaController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */

	public function listCajaMov($date = null){

               if($date==null){
		$yesterday = date('Y-m-d', strtotime( '-1 days' ));
                $today = date('Y-m-d');
                $tomorrow = date('Y-m-d', strtotime( '+1 days' ));
                if(\app\CierreCaja::all()->last()!=null){
                $ultimo_cierre = strtotime(\app\CierreCaja::all()->last()->created_at);
                }else{
                $ultimo_cierre = 0;
                }

                $cierreAnterior = \app\Caja::where('created_at','<',$today)->orderBy('created_at', 'desc')->first();
                $caja = \app\Caja::whereBetween('created_at',[$today,$tomorrow])->get();
                $today = strtotime(\app\Caja::all()->last()->created_at);

		return view('caja.list')->with('cierreAnterior',$cierreAnterior)->with('caja',$caja)->with('today',$today)->with('ultimo_cierre',$ultimo_cierre);
                }else{
                
                $date = date( 'Y-m-d', strtotime( $date ) );
                $yesterday = date('Y-m-d', strtotime( $date.'-1 days' ));
                $today = $date;
                $tomorrow = date('Y-m-d', strtotime( $date.'+1 days' ));
                
                $cierreAnterior = \app\CierreCaja::whereBetween('created_at',[$yesterday,$today])->first();
                $caja = \app\Caja::whereBetween('created_at',[$today,$tomorrow])->get();

		return view('caja.list')->with('cierreAnterior',$cierreAnterior)->with('caja',$caja)->with('today',$today)->with('date',$date);
                
                }
	}

        public function getCajaMov(){
        
                $conceptos = \app\ConceptosCaja::where('is_active','=',1)->get();

		return view('caja.movimiento')->with('conceptos',$conceptos);
	}

        public function reporteCaja(){

        if(Input::has('desde') && Input::has('hasta')){
        $rango[0] = Input::get('desde');
	$rango[1] = date('Y-m-d', strtotime( Input::get('hasta').'+1 days' ));

        $movimientos = \app\Caja::whereBetween('created_at',$rango)
        ->orderBy('created_at','ASC')
        ->get();
         return view('report.reporte_caja')->with('movimientos',$movimientos)->with('desde',Input::get('desde'))->with('hasta',Input::get('hasta'));
       }else{
        $hoy = date("Y-m-d");   
	$movimientos = \app\Caja::where('created_at','=',$hoy)->orderBy('created_at','ASC')->get();
        return view('report.reporte_caja')->with('movimientos',$movimientos)->with('hoy',$hoy);
     
     }	
   }
	
	public function cierreCaja(){
        
                $cierres = \app\CierreCaja::orderBy('created_at','DESC')->get();

		return view('caja.cierre')->with('cierres',$cierres);
	}
	
	public function getConceptosCaja(){
        
                $conceptos = \app\ConceptosCaja::where('is_active','=',1)->get();

		return view('caja.conceptos')->with('conceptos',$conceptos);
	}
	
	public function postConceptosCaja(){
        
                $concepto = new \app\ConceptosCaja;

		$concepto->concepto = Input::get('concepto'); 
		
		if($concepto->save()){
				
					Session::flash('message', 'Concepto creado correctamente!!');
					return Redirect::to('conceptosCaja/');
				}
	}

         public function postCajaMov(){
        
                $rules = array(
			'user_id' => 'required',
			'tipo_movimiento_caja' => 'required',
			'importe' => 'required',
			'conceptos_caja_id' => 'required'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('movimiento/')
			->withErrors($validator);
		} else {
                        
                        $today = date('Y-m-d');

                        $ultimo = \app\Caja::all()->last();

                        if($ultimo!= null && $today == date('Y-m-d', strtotime($ultimo->created_at))){
                            
                           if(Input::get('tipo_movimiento_caja')==1){
                                  
                              $saldo = $ultimo->saldo + Input::get('importe');

                            }else{
                         
                              $saldo = $ultimo->saldo - Input::get('importe');
                          
                            }
                             
                        }else{

                           $ultimo_saldo = \app\Caja::all()->last();  
                           
                           if($ultimo_saldo!=null){
                           $ultimo_saldo = $ultimo_saldo->saldo;
                           }
                           
                           if($ultimo_saldo!=null){
                           if(Input::get('tipo_movimiento_caja')==1){
                                  
                              $saldo = $ultimo_saldo + Input::get('importe');

                            }else{
                         
                              $saldo = $ultimo_saldo - Input::get('importe');
                          
                            }}else{
                              $saldo = Input::get('importe');
                            }
                        }

                        $movimiento = new \app\Caja;

			$movimiento->users_id = Input::get('user_id'); 			
			$movimiento->tipo_movimiento_caja = Input::get('tipo_movimiento_caja');
			$movimiento->importe = Input::get('importe');
                        $movimiento->saldo = $saldo;
			$movimiento->conceptos_caja_id = Input::get('conceptos_caja_id');
			$movimiento->detalle= Input::get('detalle');
                        if($movimiento->save()){
				
					Session::flash('message', 'Movimiento cargado correctamente!!');
					return Redirect::to('movimiento/');
				}

                }
	}

	public function getCerrarCaja(){

          return view('caja.cerrar');
        }

        public function postCerrarCaja(){
          
           $mon_01 = (Input::get('mon_01') * 0.01);
           $mon_05 = (Input::get('mon_05') * 0.05);
           $mon_010 = (Input::get('mon_010') * 0.1);
           $mon_025 = (Input::get('mon_025') * 0.25);
           $mon_050 = (Input::get('mon_050') * 0.50);
           $mon_1 = (Input::get('mon_1') * 1);
           $mon_2 = (Input::get('mon_2') * 2);
           $mon_5 = (Input::get('mon_5') * 5);
           $mon_10 = (Input::get('mon_10') * 10);
           $mon_20 = (Input::get('mon_20') * 20);
           $mon_50 = (Input::get('mon_50') * 50);
           $mon_100 = (Input::get('mon_100') * 100);
           $mon_200 = (Input::get('mon_200') * 200);
           $mon_500 = (Input::get('mon_500') * 500);

           $saldo = $mon_01 + $mon_05 + $mon_010 + $mon_025 + $mon_050 + $mon_1 + $mon_2 + $mon_5 + $mon_10 + $mon_20 + $mon_50 + $mon_100 + $mon_200 + $mon_500 + Input::get('cheques_importe');

            $ultimo = \app\Caja::all()->last();

            if(number_format($saldo,2,'.','') == number_format($ultimo->saldo,2,'.','')){

            $cierre = new \app\CierreCaja;
            $cierre->mon_01= Input::get('mon_01');
            $cierre->users_id = Input::get('user_id');  
            $cierre->mon_05= Input::get('mon_05'); 
            $cierre->mon_010= Input::get('mon_010'); 
            $cierre->mon_025= Input::get('mon_025'); 
            $cierre->mon_050= Input::get('mon_050'); 
            $cierre->mon_1= Input::get('mon_1'); 
            $cierre->mon_2= Input::get('mon_2'); 
            $cierre->mon_5= Input::get('mon_5'); 
            $cierre->mon_10= Input::get('mon_10'); 
            $cierre->mon_20= Input::get('mon_20'); 
            $cierre->mon_50= Input::get('mon_50'); 
            $cierre->mon_100= Input::get('mon_100');             
            $cierre->mon_200= Input::get('mon_200'); 
            $cierre->mon_500= Input::get('mon_500'); 
            $cierre->cheques_importe= Input::get('cheques_importe');
            $cierre->saldo = number_format($saldo,2,'.','');
            
            // Movemos los retiros del dia a la caja especial
            $today = date('Y-m-d');
            $tomorrow = date('Y-m-d', strtotime( '+1 days' ));
            
            $retiros = \app\Caja::whereBetween('created_at',[$today,$tomorrow])->get();
           
            foreach($retiros as $retiro){
                
                if($retiro->conceptos_caja_id == 30 || $retiro->conceptos_caja_id == 31 ||$retiro->conceptos_caja_id == 32){
                    
                        $ultimo = \app\CajaEspecial::all()->last();
        
                                if($ultimo!= null && $today == date('Y-m-d', strtotime($ultimo->created_at))){
                                    
                                    $saldo = $ultimo->saldo + $retiro->importe;
                                     
                                }else{

                                   $ultimo_saldo = \app\CajaEspecial::all()->last();  
                                   
                                   if($ultimo_saldo!=null){
                                   $ultimo_saldo = $ultimo_saldo->saldo;
                                   $saldo = $ultimo_saldo +  $retiro->importe;
                                   }else{
                                
                                   $saldo = Input::get('importe');
                                  } 
                                }

                        $movimiento = new \app\CajaEspecial;

            			$movimiento->users_id = $retiro->users_id; 			
            			$movimiento->tipo_movimiento_caja = 1;
            			$movimiento->importe = $retiro->importe;
                        $movimiento->saldo = $saldo;
            			$movimiento->conceptos_caja_id = $retiro->conceptos_caja_id;
            			$movimiento->detalle= $retiro->detalle;
            			$movimiento->save();
            }
                            
            }
            
            
            
            if($cierre->save()){
				
					Session::flash('message', 'Caja cerrada correctamente!!');
					return Redirect::to('caja/');
				}

                     }else{
                      Session::flash('message', 'Verificar los valores, saldo final no concuerda con cierre!');
					return Redirect::to('cerrarCaja/')->withInput();
                      
                    }

        }

public function deleteConcepto($id = null){

		$concepto = \app\ConceptosCaja::find($id);
				
		if($concepto!=null){

				$concepto->is_active = 0;
				if($concepto->save()) {
					Session::flash('message', 'Concepto eliminado correctamente!!');
					return Redirect::to('conceptosCaja');

		}else{
			return view('conceptosCaja');
		}
		

}else{ return view('conceptosCaja'); }

}

}

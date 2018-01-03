<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Response;
use Session;
use Auth;
use Request;

class CajaEspecialController extends Controller {

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
            
                if(\app\CierreCajaEspecial::all()->last()!=null){

                $ultimo_cierre = strtotime(\app\CierreCajaEspecial::all()->last()->created_at);   

                }else{
                $ultimo_cierre = 0;
                }
                
                $cierreAnterior = \app\CierreCajaEspecial::where('created_at','<=',$today)->orderBy('created_at', 'desc')->first();
                $fechaCA = strtotime('+1 day', strtotime ( $cierreAnterior->created_at));
                $fechaCA = date('Y-m-d', $fechaCA);
                $caja = \app\CajaEspecial::where('created_at','>',$fechaCA)->get();
                $today = strtotime(\app\CajaEspecial::all()->last()->created_at);
             
		        return view('cajaEspecial.list')->with('cierreAnterior',$cierreAnterior)->with('caja',$caja)->with('today',$today)->with('ultimo_cierre',$ultimo_cierre);
                
                   
               }else{
                
                $date = date( 'Y-m-d', strtotime( $date ) );
                $yesterday = date('Y-m-d', strtotime( $date.'-1 days' ));
                $today = $date;
                $tomorrow = date('Y-m-d', strtotime( $date.'+1 days' ));
                
                $cierreAnterior = \app\CierreCajaEspecial::whereBetween('created_at',[$yesterday,$today])->first();
                $caja = \app\CajaEspecial::whereBetween('created_at',[$today,$tomorrow])->get();

		return view('cajaEspecial.list')->with('cierreAnterior',$cierreAnterior)->with('caja',$caja)->with('today',$today)->with('date',$date);
                
                }
	}

        public function getCajaMov(){
        
                $conceptos = \app\ConceptosCajaEspecial::where('is_active','=',1)->get();

		return view('cajaEspecial.movimiento')->with('conceptos',$conceptos);
	}

        public function reporteCaja(){

        if(Input::has('desde') && Input::has('hasta')){
        $rango[0] = Input::get('desde');
	$rango[1] = date('Y-m-d', strtotime( Input::get('hasta').'+1 days' ));

        $movimientos = \app\CajaEspecial::whereBetween('created_at',$rango)
        ->orderBy('created_at','ASC')
        ->get();
         return view('report.reporte_caja_especial')->with('movimientos',$movimientos)->with('desde',Input::get('desde'))->with('hasta',Input::get('hasta'));
       }else{
        $hoy = date("Y-m-d");   
	$movimientos = \app\CajaEspecial::where('created_at','=',$hoy)->orderBy('created_at','ASC')->get();
        return view('report.reporte_caja_especial')->with('movimientos',$movimientos)->with('hoy',$hoy);
     
     }	
   }
	
	public function cierreCaja(){
        
                $cierres = \app\CierreCajaEspecial::orderBy('created_at','DESC')->get();

		return view('cajaEspecial.cierre')->with('cierres',$cierres);
	}
	
	public function getConceptosCaja(){
        
                $conceptos = \app\ConceptosCajaEspecial::where('is_active','=',1)->get();

		return view('cajaEspecial.conceptos')->with('conceptos',$conceptos);
	}
	
	public function postConceptosCaja(){
        
                $concepto = new \app\ConceptosCajaEspecial;

		$concepto->concepto = Input::get('concepto'); 
		
		if($concepto->save()){
				
					Session::flash('message', 'Concepto creado correctamente!!');
					return Redirect::to('conceptosCajaEspecial/');
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
			return Redirect::to('movimientoEspecial/')
			->withErrors($validator);
		} else {
                        
                        $today = date('Y-m-d');

                        $ultimo = \app\CajaEspecial::all()->last();

                        if($ultimo!= null && $today == date('Y-m-d', strtotime($ultimo->created_at))){
                            
                           if(Input::get('tipo_movimiento_caja')==1){
                                  
                              $saldo = $ultimo->saldo + Input::get('importe');

                            }else{
                         
                              $saldo = $ultimo->saldo - Input::get('importe');
                          
                            }
                             
                        }else{

                           $ultimo_saldo = \app\CajaEspecial::all()->last();  
                           
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

                        $movimiento = new \app\CajaEspecial;

			$movimiento->users_id = Input::get('user_id'); 			
			$movimiento->tipo_movimiento_caja = Input::get('tipo_movimiento_caja');
			$movimiento->importe = Input::get('importe');
                        $movimiento->saldo = $saldo;
			$movimiento->conceptos_caja_id = Input::get('conceptos_caja_id');
			$movimiento->detalle= Input::get('detalle');
                        if($movimiento->save()){
				
					Session::flash('message', 'Movimiento cargado correctamente!!');
					return Redirect::to('movimientoEspecial/');
				}

                }
	}

	public function getCerrarCaja(){

          return view('cajaEspecial.cerrar');
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

            $ultimo = \app\CajaEspecial::all()->last();

            if(number_format($saldo,2,'.','') == number_format($ultimo->saldo,2,'.','')){

            $cierre = new \app\CierreCajaEspecial;
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


            if($cierre->save()){
				
					Session::flash('message', 'Caja cerrada correctamente!!');
					return Redirect::to('cajaEspecial/');
				}

                     }else{
                      Session::flash('message', 'Verificar los valores, saldo final no concuerda con cierre!');
					return Redirect::to('cerrarCajaEspecial/')->withInput();
                      
                    }

        }

public function deleteConcepto($id = null){

		$concepto = \app\ConceptosCajaEspecial::find($id);
				
		if($concepto!=null){

				$concepto->is_active = 0;
				if($concepto->save()) {
					Session::flash('message', 'Concepto eliminado correctamente!!');
					return Redirect::to('conceptosCajaEspecial');

		}else{
			return view('conceptosCaja');
		}
		

}else{ return view('conceptosCaja'); }

}

}

<?php

use tklovett\barcodes\BarcodeGenerator;
use tklovett\barcodes\BarcodeType;
use app\TipoIva;
use app\TaxType;
use app\Concepto;
use app\TipoCbte;
use app\InvoiceHead;

class myFunctions {

public static function CreateTRA($SERVICE){

	 $TRA = new SimpleXMLElement(
    '<?phpxml version="1.0" encoding="UTF-8"?>' .
    '<loginTicketRequest version="1.0">'.
    '</loginTicketRequest>');
  $TRA->addChild('header');
  $TRA->header->addChild('destination','cn=wsaa,o=afip,c=ar,serialNumber=CUIT 33693450239');
  $TRA->header->addChild('uniqueId',date('U'));
  $TRA->header->addChild('generationTime',date('c',date('U')-60));
  $TRA->header->addChild('expirationTime',date('c',date('U')+60));
  $TRA->addChild('service','wsfe');
  $TRA->asXML('TRA.xml');
}

public static function SignTRA()
{
  $STATUS=openssl_pkcs7_sign("TRA.xml", "TRA.tmp", "file://".env('CERT'),
    array("file://".env('PRIVATEKEY'), env('PASSPHRASE')),
    array(),
    !PKCS7_DETACHED
    );
  if (!$STATUS) {exit("ERROR generating PKCS#7 signature\n");}
  $inf=fopen("TRA.tmp", "r");
  $i=0;
  $CMS="";
  while (!feof($inf)) 
  { 
      $buffer=fgets($inf);
      if ( $i++ >= 4 ) {$CMS.=$buffer;}
  }
  fclose($inf);
# unlink("TRA.xml");
  unlink("TRA.tmp");
  return $CMS;
}

public static function CallWSAA($CMS)
{
  $client=new SoapClient(env('WSDL_WSAA'), array(
      'soap_version'   => SOAP_1_2,
      'location'       => env('URL_WSAA'),
      'trace'          => 1,
      'exceptions'     => 0
      )); 
  $results=$client->loginCms(array('in0'=>$CMS));
  file_put_contents("request-loginCms.xml",$client->__getLastRequest());
  file_put_contents("response-loginCms.xml",$client->__getLastResponse());
  if (is_soap_fault($results)) 
    {exit("SOAP Fault: ".$results->faultcode."\n".$results->faultstring."\n");}
return $results->loginCmsReturn;
}

public static function ShowUsage($MyPath)
{
  printf("Uso  : %s Arg#1 Arg#2\n", $MyPath);
  printf("donde: Arg#1 debe ser el service name del WS de negocio.\n");
  printf("  Ej.: %s wsfe\n", $MyPath);
}

public static function CheckErrors($results, $method, $client)
{
  if (env('LOG_XMLS'))
  {
    file_put_contents("request-".$method.".xml",$client->__getLastRequest());
    file_put_contents("response-".$method.".xml",$client->__getLastResponse());
}
if (is_soap_fault($results)) 
  { printf("Fault: %s\nFaultString: %s\n",
    $results->faultcode, $results->faultstring); 
exit (1);
}
$Y=$method.'Result';
$X=$results->$Y;
if (isset($X->Errors))
{
  foreach ($X->Errors->Err as $E)
  {
      printf("Method=%s / Code=%s / Msg=%s\n",$method, $E->Code, $E->Msg);
  }
  exit (1);
}

}


public static function FECompUltimoAutorizado ($client, $token, $sign, $CUIT, $PV, $TC)
{
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign'] = $sign;
  $params['Auth']['Cuit'] = $CUIT;
  $params['PtoVta'] = $PV;
  $params['CbteTipo'] = $TC;
  $results=$client->FECompUltimoAutorizado($params);
  \myFunctions::CheckErrors($results, 'FECompUltimoAutorizado', $client);
  $X=$results->FECompUltimoAutorizadoResult;
  //printf("PV=%s  / TC=%s  /  Ult.Cbte=%s\n",$PV, $TC,$X->CbteNro);
  return $X->CbteNro;
}

public static function FECAESolicitar ($client, $params)
{
  $results=$client->FECAESolicitar($params);
  \myFunctions::CheckErrors($results, 'FECAESolicitar', $client);
  $C=$results->FECAESolicitarResult->FeCabResp;
  $D=$results->FECAESolicitarResult->FeDetResp;
  // printf("Resultado Cabecera=%s\n",$C->Resultado);
  $resul=array();
  foreach ($D->FECAEDetResponse as $d)
  {
      //printf("Resultado Cbte#%s = %s  /  CAE=%s  /  vto=%s\n", 
        $resul['cbteNro']= $d->CbteDesde;
        $resul['resultado']= $d->Resultado; 
        $resul['cae'] = $d->CAE;
        $resul['fecha_cae'] = date('Y-m-d',strtotime($d->CAEFchVto));
        $resul['motivo_r'] = null;
     if (isset($d->Observaciones))
      {
          foreach ($d->Observaciones->Obs as $O)
          {
              $resul['motivo_r'] = $O->Msg;
          }
      }

   
  }
  return $resul;
}

public static function EmitirFC ($client, $token, $sign, $CUIT, $PV, $CANT, $IDfacturaHead)
{

  $facturaHead = InvoiceHead::find($IDfacturaHead);
  $TC=$facturaHead->cbte_tipo;
  $ULT_CBTE=\myFunctions::FECompUltimoAutorizado($client, $token, $sign, env('CUIT'), $PV, $TC);
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign'] = $sign;
  $params['Auth']['Cuit'] = $CUIT;
  $FeCabReq['CantReg'] = $CANT;
  $FeCabReq['PtoVta']= $PV;
  $FeCabReq['CbteTipo'] = $TC;
  $FeDetReq=array();
  for ($i=0;$i<$CANT;$i++)
  {
      $FEDetRequest='';
      $FEDetRequest['Concepto']=1;
      $FEDetRequest['DocTipo']=$facturaHead->tax_type->code;
      $FEDetRequest['DocNro']= $facturaHead->tax_id;
      $facturaHead->cbte_desde = $ULT_CBTE+$i+1;
      $facturaHead->cbte_hasta = $ULT_CBTE+$i+1;    
      $FEDetRequest['CbteDesde']=$facturaHead->cbte_desde;
      $FEDetRequest['CbteHasta']=$facturaHead->cbte_hasta;
      $fecha = $facturaHead->fecha_facturacion;
      $FEDetRequest['CbteFch']=date('Ymd',strtotime($fecha));
      $FEDetRequest['ImpTotal']=round($facturaHead->imp_total, 2);
      $FEDetRequest['ImpTotConc']=0;
      $FEDetRequest['ImpNeto']=round($facturaHead->imp_net, 2);
      $FEDetRequest['ImpOpEx']=0;
      $FEDetRequest['ImpTrib']=0;
      $FEDetRequest['ImpIVA']=round($facturaHead->iva_imp_total, 2);
      $FEDetRequest['MonId']='PES';
      $FEDetRequest['MonCotiz']=1; 
      if(round($facturaHead->iva_imp_total, 2)>0){
      $FEDetRequest['Iva']['AlicIva'][0]['Id']=5;
     }else{
      $FEDetRequest['Iva']['AlicIva'][0]['Id']=3;
      }
      $FEDetRequest['Iva']['AlicIva'][0]['BaseImp']=round($facturaHead->imp_net, 2);
      $FEDetRequest['Iva']['AlicIva'][0]['Importe']=round($facturaHead->iva_imp_total, 2);
      $FeDetReq[$i]=$FEDetRequest;
  }


  $params['FeCAEReq']['FeCabReq'] = $FeCabReq;
  $params['FeCAEReq']['FeDetReq'] = $FeDetReq;

  $resul = \myFunctions::FECAESolicitar($client, $params);

  $facturaHead->cae = $resul['cae'];
  $facturaHead->fecha_vto_cae = $resul['fecha_cae'];
  $facturaHead->nro_cbte = $resul['cbteNro'];
  $facturaHead->status = $resul['resultado'];
  $facturaHead->motivo_r = $resul['motivo_r'];
  $facturaHead->save();

  return $facturaHead;

}

public static function FECompConsultar ($client, $token, $sign, $CUIT, $PV, $TC)
{
  $ULT_CBTE=\myFunctions::FECompUltimoAutorizado($client, $token, $sign, env('CUIT'), $PV, $TC);
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign'] = $sign;
  $params['Auth']['Cuit'] = $CUIT;
  $params['FeCompConsReq']['CbteTipo']=$TC;
  $params['FeCompConsReq']['CbteNro']=$ULT_CBTE;
  $params['FeCompConsReq']['PtoVta']=$PV;
  $results=$client->FECompConsultar($params);
  \myFunctions::CheckErrors($results, 'FECompConsultar', $client);
}

public static function FECompTotXRequest ($client, $token, $sign, $CUIT)
{
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign'] = $sign;
  $params['Auth']['Cuit'] = $CUIT;
  $results=$client->FECompTotXRequest($params);
  \myFunctions::CheckErrors($results, 'FECompTotXRequest', $client);
  $X=$results->FECompTotXRequestResult;
  return $X->RegXReq;
}


public static function get_soap(){
 return	new soapClient(env('WSDL_WSFEX'),
      array('soap_version' => SOAP_1_2,
        'location'     => env('URL_WSFEX'),
        'exceptions'   => 0,
        'encoding'     => 'ISO-8859-1',
        'features'     => SOAP_USE_XSI_ARRAY_TYPE + SOAP_SINGLE_ELEMENT_ARRAYS,
        'trace'        => 1));
}


public static function digitoVerificadorCaeAfip($txt){
  $i = $pares = $impares = 0;

  for( $i = 0; $i < strlen($txt); $i++ ) {
    if( $i % 2 ) {
      $pares += $txt[$i];
    } else {
      $impares += $txt[$i];
    }
  }
  $impares = $impares * 3;
  
  $total = $pares + $impares;
  
  $dv = $total % 10;

  return $dv;
}

public static function codigoBarraCAE( $cuit, $tipoComprobante, $puntotoDeVenta, $cae, $fechaVencimiento ) {
 
  $tipoComprobante = str_pad( $tipoComprobante, 2, '0', STR_PAD_LEFT );
  $puntotoDeVenta = str_pad( $puntotoDeVenta, 4, '0', STR_PAD_LEFT ); 
  $txt = $cuit . $tipoComprobante . $puntotoDeVenta . $cae . $fechaVencimiento;
  $dv = \myFunctions::digitoVerificadorCaeAfip($txt);  
  $txt = $txt . $dv;

  $generator = new BarcodeGenerator();
  $barcode = $generator->generate(BarcodeType::INTERLEAVED_2_OF_5, $txt);

  return $barcode->toHTML() . $txt . "</br>" ;  
}


public static function FEParamGetTiposIva ($client, $token, $sign, $CUIT)
{
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign']= $sign;
  $params['Auth']['Cuit']= $CUIT;
  $results=$client->FEParamGetTiposIva($params);
  \myFunctions::CheckErrors($results, 'FEParamGetTiposIva', $client);
  $X=$results->FEParamGetTiposIvaResult;
  $fh=fopen("TiposIva.txt","w");
  foreach ($X->ResultGet->IvaTipo as $Y)
    {
      $tipoIVA = new TipoIVA();
      $tipoIVA->code =  $Y->Id; 
      $tipoIVA->tipo_iva = $Y->Desc; 
      $tipoIVA->save();
    }
  fclose($fh);
}

public static function FEParamGetTiposCbte ($client, $token, $sign, $CUIT)
{
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign']= $sign;
  $params['Auth']['Cuit']= $CUIT;
  $results=$client->FEParamGetTiposCbte($params);
  \myFunctions::CheckErrors($results, 'FEParamGetTiposCbte', $client);
  $X=$results->FEParamGetTiposCbteResult;
  $fh=fopen("TiposCbte.txt","w");
  foreach ($X->ResultGet->CbteTipo as $Y)
    {
      $tipoCbte = new TipoCbte();
      $tipoCbte->code =  $Y->Id; 
      $tipoCbte->tipo_cbte = $Y->Desc; 
      $tipoCbte->save();	
    }
  fclose($fh);
}

public static function FEParamGetTiposDoc ($client, $token, $sign, $CUIT)
{
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign']= $sign;
  $params['Auth']['Cuit']= $CUIT;
  $results=$client->FEParamGetTiposDoc($params);
  \myFunctions::CheckErrors($results, 'FEParamGetTiposDoc', $client);
  $X=$results->FEParamGetTiposDocResult;
  $fh=fopen("TiposDoc.txt","w");
  foreach ($X->ResultGet->DocTipo as $Y)
    {
      $tipoIVA = new TaxType();
      $tipoIVA->code =  $Y->Id; 
      $tipoIVA->type = $Y->Desc; 
      $tipoIVA->save();	
    }
  fclose($fh);
}

public static function FEParamGetPtosVenta ($client, $token, $sign, $CUIT)
{
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign']= $sign;
  $params['Auth']['Cuit']= $CUIT;
  $results=$client->FEParamGetPtosVenta($params);
  \myFunctions::CheckErrors($results, 'FEParamGetPtosVenta', $client);
  $X=$results->FEParamGetPtosVentaResult;
  $fh=fopen("PtosVenta.txt","w");
  foreach ($X->ResultGet->PtoVentaTipo as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}

public static function FEParamGetTiposConcepto ($client, $token, $sign, $CUIT)
{
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign']= $sign;
  $params['Auth']['Cuit']= $CUIT;
  $results=$client->FEParamGetTiposConcepto($params);
  \myFunctions::CheckErrors($results, 'FEParamGetTiposConcepto', $client);
  $X=$results->FEParamGetTiposConceptoResult;
  $fh=fopen("TiposConcepto.txt","w");
  foreach ($X->ResultGet->ConceptoTipo as $Y)
    {
    
      $concepto = new Concepto();
      $concepto->code =  $Y->Id; 
      $concepto->concepto = $Y->Desc; 
      $concepto->save();
    
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}

public static function FEParamGetCotizacion ($client, $token, $sign, $CUIT, $MON)
{
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign']= $sign;
  $params['Auth']['Cuit']= $CUIT;
  $params['MonId'] = $MON;
  $results=$client->FEParamGetCotizacion($params);
  \myFunctions::CheckErrors($results, 'FEParamGetCotizacion', $client);
  $X=$results->FEParamGetCotizacionResult->ResultGet;
  printf("Id=%s  /  Cotiz=%f  / Fecha=%s\n", $X->MonId, $X->MonCotiz, 
         $X->FchCotiz);
  return $X->MonCotiz;
}

public static function FEParamGetTiposTributos ($client, $token, $sign, $CUIT)
{
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign']= $sign;
  $params['Auth']['Cuit']= $CUIT;
  $results=$client->FEParamGetTiposTributos($params);
  \myFunctions::CheckErrors($results, 'FEParamGetTiposTributos', $client);
  $X=$results->FEParamGetTiposTributosResult;
  $fh=fopen("TiposTributos.txt","w");
  foreach ($X->ResultGet->TributoTipo as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}

public static function FEParamGetTiposMonedas ($client, $token, $sign, $CUIT)
{
  $params['Auth']['Token'] = $token;
  $params['Auth']['Sign']= $sign;
  $params['Auth']['Cuit']= $CUIT;
  $results=$client->FEParamGetTiposMonedas($params);
  \myFunctions::CheckErrors($results, 'FEParamGetTiposMonedas', $client);
  $X=$results->FEParamGetTiposMonedasResult;
  $fh=fopen("TiposMonedas.txt","w");
  foreach ($X->ResultGet->Moneda as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}

public static function FacturaStatus ($status)
{
  switch ($status) {
    case "G":
        return "Guardado";
        break;
    case "A":
        echo "Aprobado";
        break;
    case "R":
        echo "Rechazado";
        break;
    default:
        echo "";
	}
}



}
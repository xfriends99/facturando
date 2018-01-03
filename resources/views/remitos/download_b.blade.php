<!DOCTYPE html>
<html lang="en">
  <head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
    
    <title>Remito</title>
    <style>
  
body {

  width: 18cm;  
  height: 29.7cm; 
  margin: 0 auto; 
  color: #001028;
  background: #FFFFFF; 
  font-family: Arial, sans-serif; 
  font-size: 15px; 
  font-family: Arial;
}

#project {
  float: left;
  margin-top: 90px;
  margin-left: 20px;
}

#lineas{
  position: absolute;
  text-align: left;
 text-align: left;
font-size: 15px;
margin-top:50px;
}
#project div,
#company div {
  white-space: nowrap; 
  font-size: 15px;  
  margin-top: 5px;
 
}

#fecha{
    text-align: right;
    margin-top: 25px;
    margin-right: 180px;
}
#footer {
  position: absolute;
  text-align: left;
 padding: 20px;
  text-align: left;
font-size: 15px;
margin-top:300px;
}
  </style>
  </head>
  <body>
    <header>
<div id="fecha"> {{date('d-m-Y',strtotime($invoice->fecha_facturacion))}} </div>
      <div id="project">
        <div>{{$invoice->company_name}} </div>
        <div>{{$invoice->address}} </div>
        <div>{{$invoice->fiscal_situation->fisc_situation}} &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;  {{ $invoice->tax_id }} </div>
        <div style="margin-top:15px;"> &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;{{ $invoice->tipo_pago}} </div>      
      </div>
    </header>
     <div id="lineas">
            @foreach($lines as $line)
              <div class="linea"> {{$line->code }} &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; {{ $line->quantity . '   ' }}  &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;{{ $line->name }}</div>
             @endforeach
    </div>
    @if($customer->transporte!=null)
    <div id="footer">
        <div>Transporte:&nbsp;{{$customer->transporte}} </div>
        <div>Dirección:&nbsp;{{$customer->direccion}} </div>
        <div>Teléfono:&nbsp;{{$customer->telefono}} </div>      
      </div>
@endif
    
     </body>
</html>
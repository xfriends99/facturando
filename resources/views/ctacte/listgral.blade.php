@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default">
				<div class="panel-heading">Cuenta Corriente
				</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
				    <label for="kwd_searchz">Cliente: </label> <input type="text" class="form-control" id="kwd_searchz" value=""/>
					<label for="kwd_search_v">Filtrar por saldos positivos y negativos: </label> <input style="cursor:pointer;" type="checkbox" id="kwd_search_v" value=""/>
					<table class="search-table table table-hover" id="my-table">
						<thead>
							<tr>

								<th>Razón Social</th>
								<th>Saldo</th>
								<th>Acción</th>
								</tr>
						</thead>
						<tbody>
							<?php 
							$saldo = 0;
							$i = 0;
							?>
							@foreach($invoices as $invoice)
							<?php
								$se = $saldos->search(function($item) use($invoice){
								   return $item['customer_id'] == $invoice->companies_id;
								});
								$sald = $se!==false ? $saldos->get($se)->saldo : 0;

							?>
							
							<tr>
								<th>@if($invoice->company_name!=''){{ $invoice->company_name }}@elseif($com = \app\Customer::find($invoice->companies_id)) {{$com->firstname}} {{$com->lastname}}  @endif </th>
								<td class="saldo_user">{{ '$ '.number_format($invoice->getSaldo($invoice->companies_id),2) }}</td>
								<td><a href= "/ctacteCompany/{{$invoice->companies_id}}" class="btn btn-info" >Ver detalle</a></td>
                                                        <?php

							$saldo = $saldo + $invoice->sumaSaldo + $sald;
							
							?>
							</tr>
							@endforeach
                                                        <tr>	
                                                        <td></td>
                                                        <td><b>{{'$ '.number_format($saldo,2)}}</b></td>
                                                        <td></td>
                                                        </tr>
						</tbody>
					</table>
					
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{ asset('/js/buscador.js') }}"></script>

<script>

$(document).ready(function(){
  $('table.search-table').tableSearch();
            var tableObj =  $('#my-table');
                inputObj = $("#kwd_searchz");
                caseSensitive = false;
                searchFieldVal = '';
                pattern = '';
            inputObj.off('keyup').on('keyup', function(){
                searchFieldVal = $(this).val();
                pattern = (caseSensitive)?RegExp(searchFieldVal):RegExp(searchFieldVal, 'i');
                tableObj.find('tbody tr').hide().each(function(){
                    var currentRow = $(this);
                    if($("#kwd_search_v").is(':checked')) {
                        valid = false;

                        currentRow.find('th:first').each(function() {
                            if(pattern.test($(this).html())){
                                valid = true;
                            }
                        });
                        currentRow.find('.saldo_user').each(function() {
                            if (valid && $(this).html().indexOf('$ 0.00') == -1 && $(this).html().indexOf('$ -0.00') == -1) {
                                currentRow.show();
                            }
                        });
                    } else {
                        currentRow.find('th:first').each(function() {
                            if(pattern.test($(this).html())){
                                currentRow.show();
                                return false;
                            }
                        });
                    }
                });
            });

  $('#kwd_search_v').change(function(){
	      var tableObj = $('#my-table'),
          inputObj = $("#kwd_searchz"),
          caseSensitive = false,
          searchFieldVal = '',
          pattern = '';
          searchFieldVal = $("#kwd_searchz").val();
          pattern = (caseSensitive)?RegExp(searchFieldVal):RegExp(searchFieldVal, 'i');
          tableObj.find('tbody tr').hide().each(function(){
              var currentRow = $(this);
              if($("#kwd_search_v").is(':checked')) {
                  valid = false;

                  currentRow.find('th:first').each(function() {
                      if(pattern.test($(this).html())){
                          valid = true;
                      }
                  });
                  currentRow.find('.saldo_user').each(function() {
                      if (valid && $(this).html().indexOf('$ 0.00') == -1 && $(this).html().indexOf('$ -0.00') == -1) {
                          currentRow.show();
                      }
                  });
              } else {
                  currentRow.find('th:first').each(function() {
                      if(pattern.test($(this).html())){
                          currentRow.show();
                          return false;
                      }
                  });
              }
          });
  });
});

</script>

@endsection

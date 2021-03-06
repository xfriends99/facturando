@extends('app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Carga manual de producción</div>
                    <div class="panel-body">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Ups!</strong> Existen los siguientes errores.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif
                        <form class="form-horizontal" role="form" method="POST" action="{{url('/cargaManualProduccion/store')}}" id="cargar-form">

                            <div class="list-group">
                                <div class="list-group-item">
                                    <legend>Producción</legend>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="products" id="products">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Fecha</label>
                                        <div class="col-md-8">
                                            <input id="input-date" type="date" class="form-control" name="date" value="{{ old('date') }}" required >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none;" id="products_add">
                                        <label class="col-md-2 control-label">Producto</label>
                                        <div class="col-md-8">
                                            <input autocomplete="off" class="form-control typeahead" id="name">
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="list-form">

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-5"><br/>
                                    <button type="button" class="btn btn-primary" id="cargar">
                                        Cargar producción
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            product_list = [];
            $("#input-date").change(function(){
                $("#products_add").val('');
                if($(this).val()!=''){
                    $("#products_add").show();
                    $("#list-form").html('');
                    product_list = [];
                    $('#products').val('');
                    $.ajax({
                        url: '/cargaManualProduccion/getListProduct/?fecha='+$(this).val(),
                        method: 'GET',
                        success: function(response){
                            if(response.data){
                                for(var i=0; i<response.data.length;i++){
                                    current = response.data[i];
                                    product_list.push(current.id);
                                    var html = '<div class="form-group">';
                                    if(current.tipo=='I'){
                                        html += '<label style="text-align: left; padding-bottom: 10px;" class="col-md-offset-2 col-md-10 control-label">'+current.name+' | Intercalado</label>';
                                        html += '<div class="col-md-offset-2 col-md-2">Packs<input value="'+current.packs+'" class="form-control" type="text" name="packs'+current.id+'"></div>';
                                        html += '<div class="col-md-2">Mangas<input value="'+current.mangas+'" class="form-control" type="text" name="mangas'+current.id+'"></div>';
                                        html += '<div class="col-md-2">Peso<input value="'+current.peso+'" class="form-control" type="text" name="peso'+current.id+'"></div>';
                                        html += '<div class="col-md-2">Tipo de manga<select class="form-control" name="type_manga'+current.id+'">';
                                        if(current.type_manga=='c'){
                                            html += '<option value="c" selected>Chica</option><option value="g">Grande</option>';
                                        } else {
                                            html += '<option value="c">Chica</option><option value="g" selected>Grande</option>';
                                        }
                                        html += '</select></div>';
                                    } else {
                                        if(!current.pro){
                                            html += '<label style="text-align: left; padding-bottom: 10px;" class="col-md-offset-2 col-md-10 control-label">'+current.name+' | Rebobinado | No hay producción</label>';
                                            html += '<div class="col-md-offset-2 col-md-2">Caso<select id="scrap-'+current.id+'" class="form-control scrap" name="type_case'+current.id+'"><option value="">Seleccione</option>';
                                            if(current.type_case=='A'){
                                                html += '<option value="A" selected>Recodificado</option><option value="B">Scrap</option>';
                                                html += '</select></div>';
                                                html += '<div id="scrap-div-'+current.id+'">';
                                                html += '<div class="col-md-2">Packs<input value="'+current.packs+'" class="form-control" type="text" name="packs'+current.id+'"></div>';
                                                html += '<div class="col-md-2">Código original<input value="'+current.original_code+'" class="form-control" type="text" name="code'+current.id+'"></div>';
                                                html += '</div>';
                                            } else {
                                                html += '<option value="A">Recodificado</option><option value="B" selected>Scrap</option>';
                                                html += '</select></div>';
                                                html += '<div id="scrap-div-'+current.id+'">';
                                                html += '<div class="col-md-2">Peso<input value="'+current.peso+'" class="form-control" type="text" name="peso'+current.id+'"></div>';
                                                html += '</div>';
                                            }
                                        } else {
                                            html += '<label style="text-align: left; padding-bottom: 10px;" class="col-md-offset-2 col-md-10 control-label">'+current.name+' | Rebobinado</label>';
                                            html += '<div class="col-md-offset-2 col-md-2">Packs<input value="'+current.packs+'" class="form-control" type="text" name="packs'+current.id+'"></div>';
                                        }
                                    }
                                    html += '</div>';
                                    $('#list-form').append(html);
                                }
                                $('#products').val(product_list.toString());
                            }
                        }
                    });
                } else {
                    $("#list-form").html('');
                    $("#products_add").hide();
                    product_list = [];
                    $('#products').val('');
                }
            });

            var $input = $(".typeahead");
            $input.typeahead({
                source: [
                        @foreach($products_lists as $key => $v)
                    {id: "{{$key}}", name: "{{$v}}"},
                    @endforeach
                ],
                autoSelect: true
            });
            $input.change(function() {
                var current = $input.typeahead("getActive");
                if (current) {
                    // Some item from your model is active!
                    if (current.name == $input.val()) {
                        var found = product_list.find(function (element) {
                            return element==current.id;
                        });
                        if(!found){
                            product_list.push(current.id);
                            $.ajax({
                                url: '/cargaManualProduccion/getProductType/'+current.id+'?fecha='+$("#input-date").val(),
                                method: 'GET',
                                success: function(response){
                                    var html = '<div class="form-group">';
                                    if(response.data=='I'){
                                        html += '<label style="text-align: left; padding-bottom: 10px;" class="col-md-offset-2 col-md-10 control-label">'+current.name+' | Intercalado</label>';
                                        html += '<div class="col-md-offset-2 col-md-2">Packs<input class="form-control" type="text" name="packs'+current.id+'"></div>';
                                        html += '<div class="col-md-2">Mangas<input class="form-control" type="text" name="mangas'+current.id+'"></div>';
                                        html += '<div class="col-md-2">Peso<input class="form-control" type="text" name="peso'+current.id+'"></div>';
                                        html += '<div class="col-md-2">Tipo de manga<select class="form-control" name="type_manga'+current.id+'"><option value="c">Chica</option><option value="g">Grande</option></select></div>';
                                    } else {
                                        if(!response.pro){
                                            html += '<label style="text-align: left; padding-bottom: 10px;" class="col-md-offset-2 col-md-10 control-label">'+current.name+' | Rebobinado | No hay producción</label>';
                                            html += '<div class="col-md-offset-2 col-md-2">Caso<select id="scrap-'+current.id+'" class="form-control scrap" name="type_case'+current.id+'"><option value="" selected>Seleccione</option><option value="A">Recodificado</option><option value="B">Scrap</option></select></div>';
                                            html += '<div id="scrap-div-'+current.id+'"></div>';
                                        } else {
                                            html += '<label style="text-align: left; padding-bottom: 10px;" class="col-md-offset-2 col-md-10 control-label">'+current.name+' | Rebobinado</label>';
                                            html += '<div class="col-md-offset-2 col-md-2">Packs<input class="form-control" type="text" name="packs'+current.id+'"></div>';
                                        }
                                    }
                                    html += '</div>';
                                    $('#list-form').append(html);
                                }
                            });
                        }
                        $('#products').val(product_list.toString());
                        $input.val('');
                        // This means the exact match is found. Use toLowerCase() if you want case insensitive match.
                    } else {
                        console.log('d2');
                        // This means it is only a partial match, you can either add a new item
                        // or take the active if you don't want new items
                    }
                } else {
                    console.log('d3');
                    // Nothing is active so it is a new value (or maybe empty value)
                }
            });

            $('#cargar').click(function () {
                console.log('a');
                $('#cargar-form').submit();
            });

            $('body').on('change', '.scrap', function(){
                id = $(this).attr('id').split('-')[1];
                var html = '';
                console.log(id);
                if($(this).val()=='A'){
                    html += '<div class="col-md-2">Packs<input class="form-control" type="text" name="packs'+id+'"></div>';
                    html += '<div class="col-md-2">Código original<input class="form-control" type="text" name="code'+id+'"></div>';
                } else if($(this).val()=='B'){
                    html += '<div class="col-md-2">Peso de scrap<input class="form-control" type="text" name="peso'+id+'"></div>';
                }
                $('#scrap-div-'+id).html(html);
            });
        });
    </script>
@endsection

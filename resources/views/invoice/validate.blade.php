@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Factura {{$invoice->supplier->name}} - {{$invoice->number}}</div>

                <div class="card-body">
                    <div class="row">
                    	<div class="col d-flex flex-row justify-content-center">
                    		<div class="d-flex flex-column text-center">
                                <a href="{{asset('facturas')}}/{{$invoice->file}}"><svg width="100px" height="100px" viewBox="0 0 16 16" class="bi bi-file-earmark-text-fill" fill="#52b788" xmlns="http://www.w3.org/2000/svg">
                                  <path fill-rule="evenodd" d="M2 2a2 2 0 0 1 2-2h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm7 2l.5-2.5 3 3L10 5a1 1 0 0 1-1-1zM4.5 8a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zM4 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/>
                                </svg></a>
                                <br>
                    			<h1>{{ucfirst($invoice->concept)}}</h1>
                    			<h3>$ {{number_format($invoice->total,0)}}</h3>
                    			<h6>${{number_format($invoice->subtotal,0)}} | ${{number_format($invoice->iva,0)}}</h6>
                    		</div>
                    	</div>
                    </div>
                    <br>
                    <form action="{{url('log')}}" method="POST">
                    	@csrf
                    	<input type="hidden" name="invoice_id" value="{{$invoice->id}}">
	                    
	                    <br>
                        <div class="coces">
                            <h5 class="text-center"><strong>Centros de Costos</strong></h5>
                            <div class="form-row">
                                <div class="col">
                                    <select class="form-control" id="coce1" name="coce1" required>
                                        <option value="">Centro de costos...</option>   
                                        @foreach($costCenters as $coce)
                                            <option value="{{$coce->id}}">{{$coce->name}}</option>  
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="percent1" placeholder="Porcentaje" required>
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="value1" placeholder="Valor" required>
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <select class="form-control" id="coce2" name="coce2">
                                        <option value="">Centro de costos...</option>   
                                        @foreach($costCenters as $coce)
                                            <option value="{{$coce->id}}">{{$coce->name}}</option>  
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="percent2" placeholder="Porcentaje">
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="value2" placeholder="Valor">
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <select class="form-control" id="coce3" name="coce3">
                                        <option value="">Centro de costos...</option>   
                                        @foreach($costCenters as $coce)
                                            <option value="{{$coce->id}}">{{$coce->name}}</option>  
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="percent3" placeholder="Porcentaje">
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="value3" placeholder="Valor">
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <select class="form-control" id="coce4" name="coce4">
                                        <option value="">Centro de costos...</option>   
                                        @foreach($costCenters as $coce)
                                            <option value="{{$coce->id}}">{{$coce->name}}</option>  
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="percent4" placeholder="Porcentaje">
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="value4" placeholder="Valor">
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <select class="form-control" id="coce5" name="coce5">
                                        <option value="">Centro de costos...</option>   
                                        @foreach($costCenters as $coce)
                                            <option value="{{$coce->id}}">{{$coce->name}}</option>  
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="percent5" placeholder="Porcentaje">
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="value5" placeholder="Valor">
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <select class="form-control" id="coce6" name="coce6">
                                        <option value="">Centro de costos...</option>   
                                        @foreach($costCenters as $coce)
                                            <option value="{{$coce->id}}">{{$coce->name}}</option>  
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="percent6" placeholder="Porcentaje">
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" name="value6" placeholder="Valor">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="description">Observación:</label>
                            <textarea class="form-control" id="concept" name="description" rows="3"></textarea>
                        </div>
                        <br>
	                    <div class="form-group">
                            <label for="approver_id">Solcitar Aprobación de:</label>
                            <select class="form-control" id="approver_id" name="approver_id">
                                <option value="">Seleccionar Aprobador...</option>   
                                @foreach($approvers as $ap)
                                    <option value="{{$ap->user->id}}">{{$ap->user->name}}</option>  
                                @endforeach
                            </select>
                        </div>
	                    <br>
	                    <div class="row">
	                    	<div class="col d-flex flex-row justify-content-center">
	                    		<div class="d-flex flex-column text-center">
	          						<input type="submit" class="btn btn-success" name="action" value="Aprobar">
	                    		</div>

	                    		<div class="d-flex flex-column text-center">
	          						<input type="submit" class="btn btn-danger" name="action" value="Rechazar">
	                    		</div>
	                    	</div>
	                    </div>
                    </form>
                    <br>
                    <div class="row">
                    	<div class="col">
                    		<table class="table-responsive-md table-bordered table-striped table-sm">
                    			<thead>
                    				<tr>
                                        <th>Fecha</th>
                    					<th>Responsable</th>
                    					<th>Estado</th>
                    					<th>Observación</th>
                    				</tr>
                    			</thead>
                    			<tbody>
                    				@foreach($invoice->log as $log)
                    				<tr>
                                        <td>{{$log->created_at}}</td>
                    					<td>{{$log->user->name}}</td>
                    					<td>{{$log->state->name}}</td>
                    					<td>{{$log->description}}</td>
                    				</tr>
                    				@endforeach
                    			</tbody>
                    		</table>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

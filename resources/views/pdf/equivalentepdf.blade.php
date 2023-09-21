<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<style type="text/css">
		.titulo{
			text-align: center;
			font: 2rem;
			color: black;
		}

			form {
			  /* Centrar el formulario en la página */
			  margin: 0 auto;
			  width: 100%;
			  height: 90%;
			  /* Esquema del formulario */
			  padding: 1em;
			  border: 1px solid #CCC;
			  border-radius: 1em;
			}

			label {
			  /* Tamaño y alineación uniforme */
			  display: inline-block;
			  width: 90px;
			  text-align: right;
			}

			input,
			textarea {
			  /* Para asegurarse de que todos los campos de texto tienen la misma configuración de letra
			     Por defecto, las áreas de texto tienen un tipo de letra monoespaciada */
			  font: 1em sans-serif;

			  outline: none;
			  border: none;

			  /* Tamaño uniforme del campo de texto */
			  width: 230px;

			  
			 /* box-sizing: border-box;

			  /* Hacer coincidir los bordes del campo del formulario */
			  /*border: 1px solid #999;*/
			}
		
	</style>
</head>
<body>
<div class="card-body">

<form action="/my-handling-form-page" method="post">
	@if($company_final == '1000')
	<img src="img/perez.png" alt="perezycardona" width="250" height="60" class="alineadoTextoImagenArriba">
	@elseif($company_final == '2000')
	<img src="img/galagro.png" alt="perezycardona" width="250" height="60" class="alineadoTextoImagenArriba">
	@endif
    <h1 style="float: right; margin-top: 1%; margin-right:5%; ">{{$prefix}}{{$consecutivo}}</h1>
	<div class="titulo"><h4>Documento soporte en adquisiciones efectuadas a no obligados a facturar</h4></div>
            <fieldset>
              <legend>Información adquiriente:</legend>
              <div class='form-group'>
                <div class='col-sm-12'>
                  <label for="user_login">Razón social:</label>
						@if($company_final == '1000')
		                 <input class="form-control input" id="supplier_name" name="" type="text" value="PEREZ Y CARDONA S.A.S">
						@elseif($company_final == '2000')
						<input class="form-control input" id="supplier_name" name="" type="text" value="MP GALAGRO S.A.S" />
						@else
						<input class="form-control input" id="supplier_name" name="" type="text" value="SUPER AGRO INVERSIONES S.A.S" />
						@endif

                        <label for="user_password">Nit</label>
						@if($company_final == '1000')
		                 <input class="form-control input" id="supplier_name" name="" type="text" value="890.912.426-7">
						@elseif($company_final == '2000')
						<input class="form-control input" id="supplier_name" name="" type="text" value="811.046.781-4" />
						@else
						<input class="form-control input" id="supplier_name" name="" type="text" value="890.938.441-0" />
						@endif
                </div>
              </div>
            </fieldset><br>
            <fieldset>
              <legend>Información proveedor:</legend>
                     @foreach($company AS $companies)
            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="first_name">Ciudad:</label>
                    <input type="text" class="form-control input" id="first_name" name="first_name" placeholder="" value="{{$companies->city}}"  required disabled>
                    <label for="last_name">Proveedor:</label>
                    <input type="text" class="form-control required email" id="last_name" name="last_name" placeholder="" value="{{$companies->supplier}}"  required disabled>
                </div>
                <div class="form-group col-sm-6">
                  <label for="user_email">Dirección:</label>
                  <input class="form-control required email" id="user_email" name="user[email]" required="true" type="text" value="{{$companies->address}}">
                  <label for="user_email">Fecha doc:</label>
                  <input class="form-control required email" id="user_email" name="user[email]" required="true" size="30" type="text" value="{{$companies->date}}">
                </div>
            </div>
              <div class='form-group'>
                  <label for="user_email">CC/Nit:</label>
                  <input class="form-control required email" id="user_email" name="user[email]" required="true" type="text" value="{{$companies->id_supplier}}">
                  <label for="user_email">Telefono:</label>
                  <input class="form-control required email" id="user_email" name="user[email]" required="true" type="text" value="{{$companies->phone}}">
              </div>
            </fieldset><br>
            @endforeach
            <fieldset>
<style type="text/css">
.tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #9dcc7a;border-collapse: collapse;}
.tftable th {font-size:12px;background-color:#abd28e;border-width: 1px;padding: 8px;border-style: solid;border-color: #9dcc7a;text-align:center;}
.tftable tr {background-color:#ffffff;}
.tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #9dcc7a;}
</style>

<table class="tftable" border="1">
<tr><th style="font-size: 15px;">Concepto</th><th style="font-size: 15px;">Valor</th></tr>
@foreach($information AS $info)
<tr><td>{{$info->description}}</td><td>$ {{number_format($info->value,2)}} {{$info->currency}}</td></tr>
@endforeach
<tr><td id="Total"><b>TOTAL</b></td><td> <b>$ {{number_format($Total_final,2)}} {{$info->currency}} </b></td></tr>
</table>
</fieldset><br><br><br>

<div class='form-group'>
	  <input class="" id="nombre_usuario" name="user[email]" required="true" type="text" value="{{$nombre_usuario}}" style="width: 50%;">
	  <hr style="width: 290px; margin-left: 1%; margin-top: -2%;">
	  <input class="" id="user_email" name="user[email]" required="true" type="text" value="Elaborado por:" style="margin-top: -1%;"><br><br><br>
	  <hr style="width: 290px; margin-left: 1%;">
	  <input class="" id="user_email" name="user[email]" required="true" size="30" type="text" value="Firma del proveedor" style="margin-top: -2%;">
</div><br><br>
<div>
	@foreach($company AS $companies)
	<h4 style="margin-left: 10%; color:red;">Autorización {{$companies->resolution}}  Fecha {{$companies->finish_date}}  desde {{$companies->inicio}} hasta {{$companies->final}}. Vigencía: {{$companies->meses}} meses</h4>
	@endforeach
</div>
</form>
</div>

</body>
</html>
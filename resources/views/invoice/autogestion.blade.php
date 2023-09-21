@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Herramientas') }}</div>
            </div>
        </div>
    </div>
</div><br>

<div class="container">
<i><h4>Selecciona la herramienta a la cual deseas acceder</h4></i><br><br>





<!-- Carousel wrapper -->
<div
  id="carouselMultiItemExample"
  class="carousel slide carousel-dark text-center"
  data-mdb-ride="carousel"
>
  <!-- Inner -->
  <div class="carousel-inner py-4">
    <!-- Single item -->
    <div class="carousel-item active">
      <div class="container">
        <div class="row">
          <div class="col-lg-4">
            <div class="card">
              <img
                src="../img/quicksight.png"
                class="card-img-top"
                alt="Waterfall" width="40" height="100"
              />
              <div class="card-body">
                <h5 class="card-title">Quicksight</h5>
                <p class="card-text">
                  QuickSight es el servicio de Inteligencia Empresarial (BI). Puedes realizar consulta y seguimiento a nuestros indicadores clave para gestión del negocio y toma de decisiones.
                </p>
                <a href="https://us-east-1.signin.aws.amazon.com/oauth?SignatureVersion=4&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAIYOJP4OBNT7XFVSQ&X-Amz-Date=2022-01-28T16%3A54%3A50.454Z&X-Amz-Signature=d558d03590fb94095bc90099ba209ee448767b2b2a41035cf9d499fda89638ab&X-Amz-SignedHeaders=host&client_id=arn%3Aaws%3Aiam%3A%3A015428540659%3Auser%2Fspaceneedle-prod&enable-sso=&forceMobileApp=0&qs-signin-user-auth=&response_type=code&redirect_uri=https%3A%2F%2Fquicksight.aws.amazon.com%2Fsn%2Fstart%3Fstate%3DhashArgs%2523%26isauthcode%3Dtrue&qs-signin-account-name=tierragro&directory_alias=tierragro&rdfs=true" target="_blank" class="btn btn-primary">Ingresar</a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 d-none d-lg-block">
            <div class="card">
              <img
                src="../img/shopihub.png"
                class="card-img-top"
                alt="Sunset Over the Sea" width="40" height="100"
              />
              <div class="card-body">
                <h5 class="card-title">Shopihub</h5>
                <p class="card-text">
                  Herramienta para administración de ventas realizadas en línea por los clientes.
                </p>
                <a href="https://shopihub-tierragro.azurewebsites.net" class="btn btn-primary" target="_blank">Ingresar</a>
              </div>
            </div>
          </div>


          <div class="col-lg-4 d-none d-lg-block">
            <div class="card">
              <img
                src="../img/kactus1.png"
                class="card-img-top"
                alt="Sunset Over the Sea" width="40" height="100"
              />
              <div class="card-body">
                <h5 class="card-title">SmatPeople</h5>
                <p class="card-text">
                  Herramienta para gestionar solicitudes y certificados como carta laboral, certificado de cesantias y vacaciones.
                </p>
                <a href="https://selfkactus.digitalwaresaas.com.co/SmartPeopleTierrago/" class="btn btn-primary" target="_blank">Ingresar</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>


@endsection


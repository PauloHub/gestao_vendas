
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.108.0">
    <title>Gestão de Vendas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <meta name="theme-color" content="#712cf9">


    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }

    </style>

  </head>
  <body class="bg-light">

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(empty($result))
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'Erro ao carregar página',
        });
      </script>
    @else
      @if(!$result['success'])
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: '{{ $result['message'] }}',
          });
        </script>
      @else
        @if(!empty($result['message']))
          <script>
            Swal.fire({
              icon: 'success',
              title: 'Sucesso!',
              text: '{{ $result['message'] }}',
            });
          </script>
        @endif
      @endif
    @endif
    
    
    <div class="container">
      <main>
        <div class="py-2 text-center">
          <h2>Gestão de vendas</h2>
          <p class="lead">Mini Sistema de gestão de vendas de produtos</p>
        </div>

        <form action="/" method="post" class="needs-validation">
          @csrf
          <div class="row g-5">
            <div class="col-md-5 col-lg-4 order-md-last">
              <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Produtos</span>
                <span class="badge bg-primary rounded-pill">{{$result['data']['amount_products']}}</span>
              </h4>
              <ul class="list-group mb-3">
                @if(!empty($result) && $result['success'])
                  @foreach($result['data']['products'] as $product)
                    <li class="list-group-item d-flex justify-content-between lh-sm row">
                      <div class="col-md-8">
                        <h6 class="my-0">{{ $product['name'] }}: <span class="text-success">R$ {{ $product['price'] }}</span></h6>
                        <small class="text-muted">Estoque: {{ $product['stock'] }}</small>
                      </div>
                      <div class="col-md-4">
                        <input type="number" class="form-control product-list" price="{{ $product['price'] }}" id="quant_{{$product['product_id']}}" name="quant_{{$product['product_id']}}" onChange="subTotal()" value="0" placeholder="0" max="{{ $product['stock'] }}" min="0">
                      </div>
                    </li>
                  @endforeach
                @else
                <li class="list-group-item d-flex justify-content-between lh-sm row">
                  <div class="col-md-12 text-center">
                    <h6 class="my-0">Nenhum produto cadastrado</h6>
                    <small class="text-muted">Cadastre produtos no banco</small>
                  </div>
                </li>

              @endif
                <li class="list-group-item d-flex justify-content-between">
                  <span>Total (R$)</span>
                  <h6 id="sub_total" name="sub_total">0</h6>
                </li>
              </ul>
            </div>

            <div class="col-md-7 col-lg-8">
              <h4 class="mb-3">Realizar Pedido</h4>
                <div class="row g-3">
                  <div class="col-sm-12">
                    <label for="country" class="form-label">Cliente</label>
                    <select class="form-select" id="client_id" name="client_id"required>
                      @if(!empty($result) && $result['success'])
                        <option value="">Selecione um cliente</option>
                        @foreach($result['data']['clients'] as $client)
                          <option value="{{ $client['client_id'] }}">{{ $client['name'] }}</option>
                        @endforeach
                      @else
                        <option value="">Cadastre clientes no banco</option>
                      @endif
                    </select>
                    <div class="invalid-feedback">
                      Selecione um Cliente
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="row">
                      <div class="col-sm-8">
                        <label for="zip_code" class="form-label">Cep</label>
                        <input type="text" class="form-control" id="zip_code" name="zip_code" value="" maxlength="9" minlength="8" required>
                        <div class="invalid-feedback">
                          O CEP é obrigatório
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <label for="district" class="form-label">Bairro</label>
                        <input type="text" class="form-control disabled" id="district" name="district" value="" required readonly style="background: #dddddd;">
                        <div class="invalid-feedback">
                          O Bairro é obrigatório
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="row">
                      <div class="col-sm-8">
                        <label for="street" class="form-label">Logradouro</label>
                        <input type="text" class="form-control" id="street" name="street" value="">
                      </div>
                      <div class="col-sm-4">
                        <label for="number" class="form-label">Número</label>
                        <input type="number" class="form-control" id="number" name="number" value="">
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="row">
                      <div class="col-sm-6">
                        <label for="city" class="form-label">Cidade</label>
                        <input type="text" class="form-control disabled" id="city" name="city" value="" required readonly style="background: #dddddd;">
                      </div>
                      <div class="col-sm-6">
                        <label for="state" class="form-label">Estado</label>
                        <input type="text" class="form-control disabled" id="state" name="state" value="" required readonly style="background: #dddddd;">
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="col-sm-12">
                      <label for="complement" class="form-label">Complemento</label>
                      <input type="text" class="form-control" id="complement" name="complement" value="">
                    </div>
                  </div>

                  <div class="col-sm-12 text-center">
                    <button class="w-50 btn btn-primary btn-lg" type="submit">Salvar Pedido</button>
                  </div>

                </div>

              </div>
            </div>
          </form>
          <hr class="my-4">
          <div class="text-center">
            <h2>Lista de Pedidos</h2>
          </div>
          <div>
            <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Cliente</th>
                <th scope="col">Produtos</th>
                <th scope="col">Quantidades</th>
                <th scope="col">Preço Total</th>
                <th scope="col">Data</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($result['data']['requests']))
                @foreach($result['data']['requests'] as $request)
                  <tr>
                    <th scope="row">{{ $request['request_id'] }}</th>
                    <th>{{ $request['client_name'] }}</th>
                    <th>{{ $request['products_names'] }}</th>
                    <th>{{ $request['amounts'] }}</th>
                    <th>{{ $request['total_price'] }}</th>
                    <th>{{ $request['created_at'] }}</th>
                  </tr>
                @endforeach
              @else
                <tr class="text-center">
                  <th scope="row">1</th>
                  <td colspan="5">Nenhum pedido realizado</td>
                </tr>
              @endif
            </tbody>
          </table>
          </div>
      </main>

      <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; Paulo Cruz</p>
        <ul class="list-inline">
          <li class="list-inline-item"><a href="https://github.com/paulohubf">GitHub</a></li>
        </ul>
      </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>

    <script>
      const zip_code = document.getElementById("zip_code");
      zip_code.addEventListener("keyup", formatZipCode);

      function formatZipCode(e){
        var v= e.target.value.replace(/\D/g,"")                
        v=v.replace(/^(\d{5})(\d)/,"$1-$2") 
        e.target.value = v;
        if(v.length == 9){
          $.ajax({
            method: 'GET', // Type of response and matches what we said in the route
            url: "/consult-zip-code/"+$("#zip_code").val(), // This is the url we gave in the route
            data: {}, // a JSON object to send back
            success: function(response){ // What to do if we succeed
              if(response.success){
                $("#district").val(response.data.bairro);
                $("#street").val(response.data.logradouro);
                $("#city").val(response.data.localidade);
                $("#state").val(response.data.uf);
              }else{
                Swal.fire({
                  icon: 'error',
                  title: 'Erro!',
                  text: response.message,
                });
              }  
              console.log(response); 
            },
            error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
              Swal.fire({
                icon: 'error',
                title: 'Erro ao consultar o CEP',
                text: 'Tente um outro CEP',
              });
            }
          });
        }else{
          $("#district").val('');
          $("#street").val('');
          $("#city").val('');
          $("#state").val('');
        }
      }

      function subTotal(){
        var sub_total = 0;
        $('.product-list').each(function(i, obj) {
          sub_total = parseFloat(sub_total) + parseInt(obj.value) * parseFloat(obj.getAttribute('price'));
        });
        $("#sub_total").text(sub_total);
      }


    </script>
  </body>
</html>

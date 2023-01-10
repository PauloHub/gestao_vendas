<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models;

class HomeController extends Controller
{

    public function index(Request $request){
        try{
            //consulta os produtos disponíveis
            $products = Models\ProductModel
                ::select('product_id', 'name', 'price', 'stock')
                ->whereNull('deleted_at')
                ->get();
            //consulta os clientes cadastrados
            $clients = Models\ClientModel
                ::select('client_id', 'name')
                ->whereNull('deleted_at')
                ->get();
            //consulta os pedidos
            $requests = Models\RequestProductModel
                ::join('requests as r', 'r.request_id', '=', 'requests_products.request_id')
                ->join('clients as c', 'c.client_id', '=', 'r.client_id')
                ->join('products as p', 'p.product_id', '=', 'requests_products.product_id')
                ->select(
                    'r.request_id',
                    'c.name as client_name',
                    'p.name as product_name',
                    'requests_products.amount',
                    'requests_products.price',
                    'r.created_at',
                )
                ->get()
                ->mapToGroups(function($value){
                    //agrupa por pedido
                    return [$value->request_id => $value];
                })->map(function($value){
                    //oganiza e formata dados
                    $total_price = $value->map(function($value2){
                        return number_format($value2->amount * $value2->price, 2, '.', '');
                    })->sum();
                    return [
                        'request_id'     => $value->first()->request_id,
                        'client_name'    => $value->first()->client_name,
                        'products_names' => $value->implode('product_name', ', '),
                        'amounts'        => $value->implode('amount', ', '),
                        'total_price'    => $total_price,
                        'created_at'     => Carbon::parse($value->first()->created_at)->format('d/m/Y H:i:s'),
                    ];
                });
            $params = $request->all();
            $result = [
                'success' => isset($params['success']) ? $params['success'] : true,
                'data'    => [
                    'products'        => $products->toArray(),
                    'amount_products' => $products->sum('stock'),
                    'clients'         => $clients->toArray(),
                    'requests'        => $requests->toArray(),
                ],
                'message' => $request->message,
            ];
        }catch(\Exception $e){
            $result = [
                'success' => false,
                'data'    => [
                    'products'        => [],
                    'amount_products' => 0,
                    'clients'         => [],
                    'requests'        => [],
                ],
                'message' => $e->getMessage(),
            ];
        }
        return view('home', ['result' => $result]);
    }

    public function consultZipCode(Request $request){
        try{
            $response = Http::get('https://viacep.com.br/ws/'.$request->zip_code.'/json/');
            if(!$response->successful() && $response->status() != 200){
                throw new \Exception('Erro ao tentar consultar o CEP, consulte um outro');
            }
            $result = [
                'success' => true,
                'data'    => $response->json(),
                'message' => 'CEP Consultado com sucesso',
            ];
        }catch(\Exception $e){
            $result = [
                'success' => false,
                'data'    => [],
                'message' => $e->getMessage(),
            ];
        }
        return $result;
    }

    public function saveRequest(Request $request){
        try{
            DB::beginTransaction();
            $validated = $request->validate([
                "zip_code" => 'required|string|min:8|max:9',
                "district" => 'required|string',
                'street'   => 'nullable|string',
                'number'   => 'nullable|numeric',
                'city'     => 'required|string',
                'state'    => [
                    'required',
                    'string',
                    'size:2',
                    Rule::in(Models\StateModel::getAcronymsList()),
                ],
                'complement' => 'nullable|text',
            ]);
            $params = $request->all();
            $params['name_state'] = Models\StateModel::getStatesList()[$request->state];
            //cadastro ou busca do estado
            $state_db = Models\StateModel::firstOrCreate([
                'acronym' => $params['state'],
                'name'    => $params['name_state'],
            ]);
            if(empty($state_db)){
                throw new \Exception('Erro ao cadastrar o estado');
            }
            //cadastro ou busca da cidade
            $city_db = Models\CityModel::firstOrCreate([
                'state_id' => $state_db['state_id'],
                'name'     => $params['city'],
            ]);
            if(empty($state_db)){
                throw new \Exception('Erro ao cadastrar a cidade');
            }
            //cadastro ou atualizacao do endereco
            $address_db = Models\AddressModel::firstOrCreate ([
                'client_id'  => $params['client_id'],
                'city_id'    => $city_db['city_id'],
                'zip_code'   => $params['zip_code'],
                'street'     => $params['street'],
                'district'   => $params['district'],
                'complement' => $params['complement'],
            ]);
            if(empty($address_db)){
                throw new \Exception('Erro ao cadastrar o endereço');
            }
            //sava o pedido
            $request_db = Models\RequestModel::create([
                'client_id' => $request->client_id,
            ]);
            if(empty($request_db)){
                throw new \Exception('Erro ao tentar salvar o pedido');
            }
            //cadastra o pedido e os produtos escolhidos do pedido
            $total_price = collect($params)->mapWithKeys(function($value, $key){
                //separa apenas os produtos
                if(Str::contains($key, 'quant_')){
                    return [Str::after($key, 'quant_') => $value];
                }
                return [];
            })->map(function($amount, $key) use($request_db){
                $product = Models\ProductModel::whereNull('deleted_at')
                    ->where('product_id', $key)
                    ->first();
                if(!empty($product)){
                    //valida a quantidade enviada
                    if($amount <= 0){
                        return null;
                    }
                    //valida a quantidade enviada com o estoque
                    if($product->stock < $amount){
                        throw new \Exception('Quantidade inválida para o produto '.$product->name);
                    }
                    //salva os produtos do pedido
                    $request_product_db = Models\RequestProductModel::create([
                        'request_id' => $request_db['request_id'],
                        'product_id' => $key,
                        'amount'     => $amount,
                        'price'      => $product->price,
                    ]);
                    if(empty($request_product_db)){
                        throw new \Exception('Erro ao tentar salvar os produtos do pedido');
                    }
                    //atualiza estoque
                    $produto_db = Models\ProductModel::where('product_id', $key)
                        ->update([
                            'stock' => $product->stock - $amount
                        ]);
                    //
                    return number_format($amount * $product->price, 2, '.', '');
                }
            })->filter()
            ->sum();
            DB::commit();
            return $this->index(new Request([
                'message' => 'Pedido registrado com sucesso',
                'success' => true
            ]));
        }catch(\Exception $e){
            DB::rollBack();
            return $this->index(new Request([
                'message' => $e->getMessage(),
                'success' => false
            ]));
        }
    }
}

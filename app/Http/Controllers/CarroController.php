<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\CarroRepository;

class CarroController extends Controller
{
    public function __construct(Carro $carro){
        $this->carro = $carro;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index($request)
    {
        // 
        $carroRepository = new CarroRepository($this->carro);

        if($request->has('atributos_modelo')) {
            $atributos_modelo = 'modelo:id,'.$request->atributos_modelo;
            $carroRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        } else {
            $carroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if($request->has('filtro')) {
            $carroRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $carroRepository->selectAtributos($request->atributos);
        } 
        return response()->json($carroRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $request->validate($this->carro->rules(), $this->carro->feedback());
        $carro = $this->carro->create([
                'modelo_id' => $request->modelo_id,
                'placa' => $request->placa,
                'disponivel' => $request->disponivel,
                'km' => $request->km
            ]
        );
        //No Terminal: php artisan storage:link // Cria o link para "publicar" as imagens do upload; 
        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $carro = $this->carro->with('modelo')->find($id);
        if($carro === null){
            return response()->json( ['erro'=>'Recurso pesquisado não existe'], 404);
        }
        return response()->json($carro, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request  $request
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $carro = $this->carro->find($id);
        if($carro === null){
            return response()->json( ['erro'=>'Impossível realizar a atualização. O recurso solicitado não existe'], 404 );
        }

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = array();

            foreach ($carro->rules() as $input => $regra) {
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $carro->feedback());
        } else {
            $request->validate($carro->rules(), $carro->feedback());
        }

        //Preenche o objeto com os ddados do request
        $carro->fill($request->all());
        $carro->save();

        return response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $carro = $this->carro->find($id);
        if($carro === null){
            return response()->json( ['erro'=>'Impossível realizar a exclusão. O recurso solicitado não existe'], 404 );
        }

        $carro->delete();
        return response()->json( ['msg' => 'O carro foi removida com sucesso!'], 200);
    }
}

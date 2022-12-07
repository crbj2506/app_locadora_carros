<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo){
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response()->json($this->modelo->with('marca')->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //    protected $fillable = ['marca_id','nome','imagem','numero_portas','lugares','air_bag','abs'];

        $request->validate($this->modelo->rules(), $this->modelo->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public'); // app/public/ ou app/local

        $modelo = $this->modelo->create([
                'marca_id' => $request->marca_id,
                'nome' => $request->nome,
                'imagem' => $imagem_urn,
                'numero_portas' => $request->numero_portas,
                'lugares' => $request->lugares,
                'air_bag' => $request->air_bag,
                'abs' => $request->abs
            ]
        );
        //No Terminal: php artisan storage:link // Cria o link para "publicar" as imagens do upload; 
        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $modelo = $this->modelo->with('marca')->find($id); //with retorna a 'marca' do modelo
        if($modelo === null){
            return response()->json( ['erro'=>'Recurso pesquisado não existe'], 404);
        }
        return response()->json($modelo, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $modelo = $this->modelo->find($id);
        if($modelo === null){
            return response()->json( ['erro'=>'Impossível realizar a atualização. O recurso solicitado não existe'], 404 );
        }

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = array();

            foreach ($modelo->rules() as $input => $regra) {
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $modelo->feedback());
        } else {
            $request->validate($modelo->rules(), $modelo->feedback());
        }

        //Caso tenha uma nova imagem, deleta a antiga do disco e armazena a nova
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/modelos', 'public'); // app/public/ ou app/local
            //Exemplo http://127.0.0.1:8000/storage/imagens/dtIujwgyj1huwanbFJ7TfbhOz4cKjvDp5R9qdzOT.png
        }



        //Preenche o objeto com os ddados do request
        $modelo->fill($request->all());
        //sobrescreve a imagem
        $request->file('imagem') ? $modelo->imagem = $imagem_urn : '';
        //Salva
        $modelo->save();

        //$modelo->update([
        //    'marca_id' => $request->marca_id,
        //    'nome' => $request->nome,
        //    'imagem' => $imagem_urn,
        //    'numero_portas' => $request->numero_portas,
        //    'lugares' => $request->lugares,
        //   'air_bag' => $request->air_bag,
        //    'abs' => $request->abs
        //]);
        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $modelo = $this->modelo->find($id);
        if($modelo === null){
            return response()->json( ['erro'=>'Impossível realizar a exclusão. O recurso solicitado não existe'], 404 );
        }

        Storage::disk('public')->delete($modelo->imagem);


        $modelo->delete();
        return response()->json( ['msg' => 'A modelo foi removida com sucesso!'], 200);
    }
}

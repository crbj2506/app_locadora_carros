<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    use HasFactory;
    protected $fillable = ['modelo_id','placa','disponivel','km'];

    public function rules(){
        return [
            'modelo_id' =>'exists:modelos,id',
            'placa' => 'required|unique:carros,placa,'.$this->id,
            'disponivel' => 'required',
            'km' => 'required'
        ];
    }
    public function feedback(){
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'O nome do modelo já existe',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres'
        ];
    }
    public function modelo(){
        return $this->belongsTo('App\Models\Modelo');
    }
}

<template>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!--Card BUSCA-->
            <card-component titulo="Busca de Marcas">
                <template v-slot:conteudo>
                    
                    <div class="form-row">
                        <div class="mb-3 col">
                            <input-container-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional. Informe o ID da Marca">
                                <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID">
                            </input-container-component>                         
                        </div>
                        <div class="mb-3 col">
                            <input-container-component titulo="Nome" id="inputNome" id-help="nomelHelp" texto-ajuda="Opcional. Informe o Nome da Marca">
                                <input type="text" class="form-control" id="inputNome" aria-describedby="nomelHelp" placeholder="Nome da Marca">
                            </input-container-component> 

                        </div>
                    </div>
                </template>
                <template v-slot:rodape>
                    <button type="submit" class="btn btn-primary btn-sm float-right">Pesquisar</button>
                </template>
            </card-component>
        
            <!--Card LISTA-->
            <div class="card">
                <div class="card-header">Relação de Marcas</div>
                <div class="card-body">
                    <table-component></table-component>
                </div>
                <div class="card-footer">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">
                        Adicionar
                    </button>
                </div>
            </div>
        </div>

        <modal-component id="modalMarca" titulo="Adicionar Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso" v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao cadastrar a marca" v-if="transacaoStatus == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome" id="novoNome" id-help="novoNomelHelp" texto-ajuda="Informe o Nome da Marca">
                        <input type="text" class="form-control" id="inputNome" aria-describedby="novoNomelHelp" placeholder="Nome da Marca" v-model="nomeMarca">
                    </input-container-component>
                    {{nomeMarca}}
                </div>
                <div class="form-group">
                    <input-container-component titulo="Logo" id="novoImagem" id-help="novoImagemlHelp" texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control-file" id="inputNome" aria-describedby="novoImagemlHelp" placeholder="Selecione uma imagem..." @change="carregarImagem($event)">
                    </input-container-component>
                    {{arquivoImagem}}
                </div>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>


    </div>
</template>

<script>
    export default{
        computed:{
            token() {
                let token = document.cookie.split(';').find( indice => {
                    return indice.includes('token=')
                })
                token = token.split('=')[1]
                token = 'Bearer ' + token
                return token
            }
        },
        data(){
            return{
                urlBase: 'http://localhost:8000/api/v1/marca',
                nomeMarca: '',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: []
            }
        },
        methods:{

            carregarImagem(e){
                this.arquivoImagem = e.target.files
            },
            salvar(){
                console.log(this.nomeMarca, this.arquivoImagem)
                let formData = new FormData();
                formData.append('nome', this.nomeMarca)
                formData.append('imagem', this.arquivoImagem[0])
                let config = {
                    headers:{
                        'Content-Type':  'multpart/form-data',
                        'Accept':  'application/json',
                        'Authorization': this.token
                    }
                }

                axios.post(this.urlBase, formData, config)
                    .then(response => {
                        this.transacaoStatus = 'adicionado'
                        this.transacaoDetalhes = response
                        console.log(response)
                    })
                    .catch(errors => {
                        this.transacaoStatus = 'erro'
                        this.transacaoDetalhes = errors.response//.data.message
                        console.log(errors.response)
                        console.log(errors.response.data)
                        console.log(errors.response.data.message)
                    })
            }
        }
    }
    
</script>

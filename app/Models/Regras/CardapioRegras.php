<?php

namespace App\Models\Regras;

use App\Models\Entity\Cardapio;
use App\Models\Entity\CardapioFoto;
use App\Models\Entity\CardapioTipo;
use App\Models\Regras\ImagemRegras;

class CardapioRegras
{
    public static $FILE_SIZE = 0;

    public static function salvar($request)
    {
        //Cadastro
        if(!isset($request->id)) {
            $cardapio = new Cardapio();
            $cardapio->status = 1;
            $cardapio->created_at = date('Y-m-d H:i:s');
        } 
        //Alteração
        else {
            $cardapio = Cardapio::find($request->id);
            $cardapio->updated_at = date('Y-m-d H:i:s');
        }

        $cardapio->fk_tipo_cardapio = $request->tipo;
        $cardapio->fk_categoria = $request->categoria;
        $cardapio->nome_item = $request->nomeItem;
        $cardapio->detalhe_item = $request->detalheItem;
        $cardapio->unid = 1;
        $cardapio->valor = $request->valor;            
        $cardapio->save();


        self::addAnexos($request, $cardapio);
    }

    public static function addAnexos($request, $cardapio)
    {
        $extensoesNaoPermitidas = ['application/x-dosexec', 'application/x-executable'];

        // Verifica se informou o arquivo de upload
        if($request->hasFile('foto')) {
                
            $uploadFiles = $request->file('foto');

            foreach($uploadFiles as $file) {
                
                if(in_array($file->getMimeType(), $extensoesNaoPermitidas)){
                    throw new \Exception("<li>O arquivo <b>{$file->getClientOriginalName()}</b> não é permitido.</li>");
                }               

                $binario = self::getBinarioDB($file);

                if(!$binario) {
                    throw new \Exception("<li>O arquivo anexado deve ser uma foto.</li>");
                }

                if(self::$FILE_SIZE > 100000000) {
                    throw new \Exception("<li>O arquivo <b>{$file->getClientOriginalName()}</b> não pode conter mais de 100MB.</li>");
                }

                if(self::$FILE_SIZE == 0) {
                    throw new \Exception("<li>Não foi possível salvar a foto.</li>");
                }

                $imagem = new ImagemRegras();

                // gera uma nova imagem redimencionada
                $thumbBinario = $imagem->gerarMiniatura($file);

                $foto = CardapioFoto::where('fk_cardapio', $cardapio->id)->first();

                if(!$foto) {
                    $foto = new CardapioFoto();                    
                }

                $foto->fk_cardapio = $cardapio->id;
                $foto->foto = $binario;
                $foto->thumbnail = $thumbBinario;                
                $foto->nome = $file->getClientOriginalName();
                $foto->type = $file->getMimeType();
                $foto->size = self::$FILE_SIZE;
                $foto->created_at = date('Y-m-d H:i:s');

                #dd($foto);

                $foto->desabilitarLog();
                $foto->save();
            }

        }

        return true;
    }


    public static function addAnexosTipoCardapio($request, $tipoCardapio)
    {
        $extensoesNaoPermitidas = ['application/x-dosexec', 'application/x-executable'];

        // Verifica se informou o arquivo de upload
        if($request->hasFile('foto')) {
                
            $uploadFiles = $request->file('foto');

            foreach($uploadFiles as $file) {
                
                if(in_array($file->getMimeType(), $extensoesNaoPermitidas)){
                    throw new \Exception("<li>O arquivo <b>{$file->getClientOriginalName()}</b> não é permitido.</li>");
                }               

                $binario = self::getBinarioDB($file);

                if(!$binario) {
                    throw new \Exception("<li>O arquivo anexado deve ser uma foto.</li>");
                }

                if(self::$FILE_SIZE > 100000000) {
                    throw new \Exception("<li>O arquivo <b>{$file->getClientOriginalName()}</b> não pode conter mais de 100MB.</li>");
                }

                if(self::$FILE_SIZE == 0) {
                    throw new \Exception("<li>Não foi possível salvar a foto.</li>");
                }

                $imagem = new ImagemRegras();

                // gera uma nova imagem redimencionada
                $thumbBinario = $imagem->gerarMiniatura($file);

                $foto = CardapioTipo::find($tipoCardapio->id);

                if(!$foto) {
                    $foto = new CardapioTipo();                    
                }

                $foto->foto = $binario;
                $foto->thumbnail = $thumbBinario;                
                $foto->nome_foto = $file->getClientOriginalName();
                $foto->type = $file->getMimeType();
                $foto->size = self::$FILE_SIZE;
                $foto->created_at = date('Y-m-d H:i:s');

                #dd($foto);

                $foto->desabilitarLog();
                $foto->save();
            }

        }

        return true;
    }

    /**
     * Retorna o binário do arquivo para salvar no banco
     */
    public static function getBinarioDB($file)
    {
        if($file->isValid()) {

            //verifica se o arquivo é uma imagem
            if(preg_match("/^image/",  $file->getMimeType())) {
                
                $imagem = new ImagemRegras();

                // gera uma nova imagem redimencionada
                #$imgBinario = $imagem->gerarImagem($file);
                $imgBinario = $imagem->gerarMiniatura($file, 500, 500);
                
                // pega o size da nova imagem
                self::$FILE_SIZE = strlen((string) $imgBinario);
                
                //retorna o binário da nova imagem
                return $imgBinario;
            }

            // entra aqui se o arquivo não for imagem
            else {

                return false;
            }
        }
    }

}
<?php

namespace App\Http\Controllers;

use App\Models\Entity\Cartao;
use Exception;
use Illuminate\Http\Request;
use Parque\Seguranca\App\Models\DB;
use QRcode;
use WideImage;

class CartaoController extends Controller
{
    public function index()
    {
        $cartoes = Cartao::all();
        return view('cartao.index', compact('cartoes'));
    }

    public function gerarImpressaoCartoes()
    {
        return view('cartao.gerar-impressao-cartoes');
    }

    public function gerarQrCode($id)
    {
        return view('cartao.gera-qrcode', compact('id'));
    }

    public function gerarCartoes()
    {
        try {

            include_once 'lib/phpqrcode/qrlib.php';
            require_once 'lib/WideImage/WideImage.php';
            
            $cartoes = Cartao::where('id', '>=', 4392)->where('cartao_gerado', 0)->get();

            #dd($cartoes);
            
            foreach($cartoes as $i => $c) {

                //Gera o a imagem do qrcode baseado no código e salva na pasta qrcode do storage
                QRcode::png($c->codigo, storage_path().'/qrcode/qrcode.png');

                //Pega a imagem qrcode salva acima em storage
                $imgQrcode = WideImage::load(storage_path().'/qrcode/qrcode.png');

                //Redimenciona o tamanho do qrcode e gera uma nova imagem
                $newQrCode = $imgQrcode->resize(1400, 1400);

                //Pega o modelo da imagem do cartão
                $img = WideImage::load(storage_path().'/cartao-paynow.png');

                //Mescla a imagem do cartão com a imagem do qrcode e gera uma nova imagem
                $newImage = $img->merge($newQrCode, "right - 1570", "bottom - 720", 100);


                $cartao = Cartao::where('codigo', $c->codigo)->first();

                //Salva a nova imagem na pasta storage
                #$newImage->saveToFile(storage_path()."/cartoes/1.png");
                $newImage->saveToFile(storage_path()."/cartoes/".$cartao->id."-".$c->codigo.".png");

                
                $cartao->cartao_gerado = 1;
                $cartao->dt_geracao_cartao = date('Y-m-d H:i:s');
                $cartao->save();
            }

            
        } catch(Exception $ex){
            dd('Error '.$ex->getMessage());
        }
    }

    public function create()
    {
        return view('cartao.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            for($i=0; $i<$request->quantidade; $i++){
                $codigo = rand(1, 999999).date('dmyHis');

                if(!Cartao::where('codigo', $codigo)->first()) {
                    Cartao::create([
                        'codigo' => str_pad($codigo, 15, "0", STR_PAD_RIGHT),
                        'hash' => md5($codigo),
                        'data' => date('Y-m-d'),
                        'fk_situacao' => 1
                    ]);
                }
            }

            DB::commit();
            return redirect('cartao')->with('sucesso', 'Cartões salvos com sucesso.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('cartao/create')->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }

    public function edit($codigo)
    {
        $cartao = Cartao::where('hash', $codigo)->first();
        return view('cartao.edit', compact('cartao'));
    }

    public function bloqueiaDesbloqueia(Request $request)
    {
        DB::beginTransaction();

        try {
            $cartao = Cartao::find($request->id);
            $cartao->fk_situacao = ($cartao->fk_situacao === 2 ? 3 : 2); //bloqueia
            $cartao->save();

            DB::commit();
            return redirect('cartao/edit/'.$request->codigo)->with('sucesso', 'Este cartão foi bloqueado.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('cartao/edit/'.$request->codigo)->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }

}

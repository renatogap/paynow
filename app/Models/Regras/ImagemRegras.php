<?php
namespace App\Models\Regras;

use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;

class ImagemRegras
{
    private $tamanhoMaximo; //3MB -> 3145728 bytes

    private $larguraMaxima;
    private $alturaMaxima;
    private $miniaturaLargura = 200;
    private $miniaturaAltura = 200;

    public function __construct($tamanhoMaximo = null, $larguraMaxima = null, $alturaMaxima = null)
    {
        $this->tamanhoMaximo = $tamanhoMaximo ? $tamanhoMaximo : 3145728;
        $this->larguraMaxima = $larguraMaxima ? $larguraMaxima : TamanhosFoto::WIDTH_6_2MP_4_3;
        $this->alturaMaxima = $alturaMaxima ? $alturaMaxima : TamanhosFoto::HEIGHT_6_2MP_4_3;
    }

    /**
     * Este método analisa a imagem e verifica qual o tipo correto de redução que deve ser aplicada a ela.
     * Imagem na horizontal tem a redução da altura proporcional a $this->larguraMaxima
     * Imagem na vertical tem a redução da largura proporcional a $this->alturaMaxima
     * Imagem quadrada tem a redução da altura proporcional a $this->larguraMaxima
     * Imagens menores que a largura ou altura padrão não serão redimensionadas
     *
     * Em qualquer um dos casos é gerada uma nova imagem com redução de 10% da qualidade
     * para que seja sempre menor que a imagem original. Tal redução de qualidade deve
     * ser imperceptível a visão humana olhando a imagem em 100% do tamanho gerado
     *
     */
    public function gerarImagem(UploadedFile $uploadedFile)
    {
        
        $manager = new ImageManager();

        $img = $manager->make(file_get_contents($uploadedFile->path()));

        //redimensionando imagem para o padrão estabelecido se for mais larga que o padrão
        if ($img->width() > $img->height() && $img->width() > $this->larguraMaxima) { //a imagem está no modo paisagem

            return $img->widen($this->larguraMaxima)->encode();

        } else if ($img->width() < $img->height() && $img->height() > $this->alturaMaxima) { //altura ultrapassa o limite estabelecido

            return $img->heighten($this->alturaMaxima)->encode();

        } else if ($img->width() === $img->height() && $img->width() > $this->larguraMaxima) { //imagem quadrada maior que o limite estabelecido

            return $img->widen($this->larguraMaxima);
        }

        
        $type = str_replace('image/', '', $uploadedFile->getMimeType());

        return $img->encode($type); //imagem que não precisou de modificação
    }

    /**
     * Este método analisa a imagem e verifica qual o tipo correto de redução que deve ser aplicada a ela.
     * Imagem na horizontal tem a redução da altura proporcional a $largura
     * Imagem na vertical tem a redução da largura proporcional a $altura
     * Imagem quadrada tem a redução da altura proporcional a $largura
     * Imagens menores que a largura ou altura padrão não serão redimensionadas
     *
     * Em qualquer um dos casos é gerada uma nova imagem com redução de 10% da qualidade
     * para que seja sempre menor que a imagem original. Tal redução de qualidade deve
     * ser imperceptível a visão humana olhando a imagem em 100% do tamanho gerado
     *
     */
    public function gerarMiniatura($binarioImagem, $largura = 100, $altura = 100)
    {

        $manager = new ImageManager();

        //$img = $manager->make($binarioImagem);
        $img = $manager->make(file_get_contents($binarioImagem->path()));
        
        /*
        if ($img->width() > $img->height() && $img->width() > $largura) { //a imagem está no modo paisagem

            return $img->widen($largura)->encode();

        } else if ($img->width() < $img->height() && $img->height() > $altura) { //altura ultrapassa o limite estabelecido

            return $img->heighten($altura)->encode();

        } else if ($img->width() === $img->height() && $img->width() > $largura) { //imagem quadrada maior que o limite estabelecido

            return $img->widen($largura);
        }*/

        $img->resize($largura, $altura);

        $type = str_replace('image/', '', $binarioImagem->getMimeType());

        return $img->encode($type); //a imagem já está menor que o limite do thumb solicitado
    }
}

<?php

namespace App\Models\Regras;

use App\Models\Entity\Anexos;
use App\Models\Entity\Denuncia;
use Illuminate\Support\Facades\DB;

class DenunciaRegras
{
    public static $FILE_SIZE = 0;
    public static $LONGITUDE = null;
    public static $LATITUDE = null;

    public static function salvar($data)
    {
        $denuncia = new Denuncia();
        $denuncia->nome          = $data->nome;
        $denuncia->contato       = $data->contato;
        $denuncia->descricao     = $data->descricao;
        $denuncia->tipo_denuncia = 1; //Corrupção;
        $denuncia->ip            = $data->ip;
        $denuncia->created_at    = date('Y-m-d H:i:s');
        $denuncia->desabilitarLog();
        $denuncia->save();

        $denuncia->hash = md5($denuncia->id);
        $denuncia->save();

        return $denuncia;
    }

    public static function addAnexos($request, $id_denuncia)
    {
        $extensoesNaoPermitidas = ['application/x-dosexec', 'application/x-executable'];

        // Verifica se informou o arquivo de upload
        if($request->hasFile('anexos')) {
                
            $uploadFiles = $request->file('anexos');

            
            foreach($uploadFiles as $file) {
                
                if(in_array($file->getMimeType(), $extensoesNaoPermitidas)){
                    throw new \Exception("<li>O arquivo <b>{$file->getClientOriginalName()}</b> não é permitido.</li>");
                }               

                $binario = self::getBinarioDB($file);


                if(self::$FILE_SIZE > 100000000) {
                    throw new \Exception("<li>O arquivo <b>{$file->getClientOriginalName()}</b> não pode conter mais de 100MB.</li>");
                }

                $anexoDenuncia              = new Anexos();
                $anexoDenuncia->nome        = $file->getClientOriginalName();
                $anexoDenuncia->type        = $file->getMimeType();
                $anexoDenuncia->fk_denuncia = $id_denuncia;
                $anexoDenuncia->arquivo     = DB::raw("decode('" . base64_encode($binario) . "', 'base64')");
                $anexoDenuncia->size        = self::$FILE_SIZE;
                $anexoDenuncia->latitude    = self::$LATITUDE;
                $anexoDenuncia->longitude   = self::$LONGITUDE;
                $anexoDenuncia->desabilitarLog();
                $anexoDenuncia->save();

                $anexoDenuncia->hash = md5($anexoDenuncia->id);
                $anexoDenuncia->save();
            }

        }

        return true;
    }



    /**
     * Retorna o binário do arquivo para salvar no banco
     */
    public static function getBinarioDB($file)
    {
        self::$LATITUDE = null;
        self::$LONGITUDE = null;

        if($file->isValid()) {
                    
            //verifica se o arquivo é uma imagem
            if(preg_match("/^image/",  $file->getMimeType())) {

                $imagem = new ImagemRegras();

                // gera uma nova imagem redimencionada
                $imgBinario = $imagem->gerarImagem($file);

                // pega o size da nova imagem
                self::$FILE_SIZE = strlen((string) $imgBinario);

                $coordenada = self::get_image_location($file->getRealPath());

                if($coordenada){
                    self::$LATITUDE = $coordenada['latitude'];
                    self::$LONGITUDE = $coordenada['longitude'];
                }
                
                //retorna o binário da nova imagem
                return $imgBinario;
            }

            // entra aqui se o arquivo não for imagem
            else {

                // pega o binário do arquivo de upload
                $binario = file_get_contents($file->path());

                // pega o size do arquivo de upload
                self::$FILE_SIZE = $file->getClientSize();

                //retorna o binário do arquivo
                return $binario;
            }
        }
    }


    public static function get_image_location($image = '')
    {
        $exif = @exif_read_data($image, 0, true);
        if($exif && (isset($exif['GPS']) 
                 && isset($exif['GPS']['GPSLatitudeRef'])
                 && isset($exif['GPS']['GPSLatitude'])
                 && isset($exif['GPS']['GPSLongitudeRef'])
                 && isset($exif['GPS']['GPSLongitude']))){
            $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
            $GPSLatitude    = $exif['GPS']['GPSLatitude'];
            $GPSLongitudeRef= $exif['GPS']['GPSLongitudeRef'];
            $GPSLongitude   = $exif['GPS']['GPSLongitude'];
            
            $lat_degrees = count($GPSLatitude) > 0 ? self::gps2Num($GPSLatitude[0]) : 0;
            $lat_minutes = count($GPSLatitude) > 1 ? self::gps2Num($GPSLatitude[1]) : 0;
            $lat_seconds = count($GPSLatitude) > 2 ? self::gps2Num($GPSLatitude[2]) : 0;
            
            $lon_degrees = count($GPSLongitude) > 0 ? self::gps2Num($GPSLongitude[0]) : 0;
            $lon_minutes = count($GPSLongitude) > 1 ? self::gps2Num($GPSLongitude[1]) : 0;
            $lon_seconds = count($GPSLongitude) > 2 ? self::gps2Num($GPSLongitude[2]) : 0;
            
            $lat_direction = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
            $lon_direction = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;
            
            $latitude = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60*60)));
            $longitude = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60*60)));
    
            return array('latitude'=>$latitude, 'longitude'=>$longitude);
        }else{
            return false;
        }
    }

    public static function gps2Num($coordPart)
    {
        $parts = explode('/', $coordPart);
        if(count($parts) <= 0)
        return 0;
        if(count($parts) == 1)
        return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }
}
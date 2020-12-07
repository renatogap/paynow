#!/bin/bash
#execute o comando abaixo apenas uma vez na máquina.
#sudo usermod -a -G www-data NOME_DO_USUARIO

#script que tenta resolver os problemas mais comuns que podem ocorrer num projeto laravel
composer dump-autoload

sudo chown -R `whoami`:www-data $(pwd)
sudo find $(pwd) -type f -exec chmod 664 {} \;
sudo find $(pwd) -type d -exec chmod 775 {} \;

#Na raiz do projeto execute:
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache

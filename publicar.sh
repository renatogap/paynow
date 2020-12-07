#!/bin/bash
rsync -av --filter=':- .gitignore' --exclude='.git' ~/www/skeleton administrador@10.74.0.4:/home/administrador/www
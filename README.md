# filas-laravel
Objetivo do sistema é importar um arquivo .json, e processar cada item 1 a 1 por fila,

### Para rodar o projeto:
- Faça o clone desse repositório;
- Execute o composer install;
- Crie e ajuste o .env conforme necessário
- Execute as migrations e os seeders;

### Para rodar a fila
- php artisan queue:work

### Telas:

> O sistema possui 2 telas

- 1: Importação do arquivo
- 2: Tela de inico de processamento

![1](https://user-images.githubusercontent.com/31832571/228763761-6031fe79-0856-4d23-80d8-fa2c62049f00.png)![2](https://user-images.githubusercontent.com/31832571/228763770-904c430e-167e-4b92-a529-f14184cf6dbc.png)
![3](https://user-images.githubusercontent.com/31832571/228763772-f85e9707-1ca4-47cb-9fe9-817c2862721a.png)


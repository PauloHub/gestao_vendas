Olá!

O projeto foi desenvolvido utilizando com:
Laravel 9.46.0
PHP 8.0.3
MySQL 8.0.3

Link do projeto GitHub (púlico)
https://github.com/PauloHub/gestao_vendas

Detro do projeto tem um dump do banco (ou o script do banco) em:
database/scripts/dump.sql ou database/scripts/query.sql

O banco já tem cadastrado alguns clientes e produtos para fins de testes

Deve-se altera as informações da coneção do banco de dados no .env
Pode se iniciar o projeto acessando a pasta e rodando 'php artisan serve'

Como usar o sistema:
	Deve-se selecionar um cliente;
	Após digitar seu CEP, será consultado as informações do endereço;
	É possível alterar apenas o lougradouro, número e complemento do endereço;
	A direito tem a lista de produtos e o número total de estoque;
	Após preencher as infãções e submeter o pedido, tem uma lista abaixo onde listará os pedidos.

As tabelas de cidades e estados são preenchidas de acordo o uso do sistema (find or create).
Em caso de erro durante o processo, o mesmo é desfeito
Não foi utilizado migration para o banco nem paginate para a lista para poupar tempo

Qualquer dúvida sobre o funcionamento favor entrar em contato.
Pauloluis.f@gmail.com
71 98217 5837

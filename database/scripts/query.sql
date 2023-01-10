drop database gestao_vendas;
create database gestao_vendas;
use gestao_vendas;

-- tabela de salva os estados
create table states (
    state_id int primary key not null auto_increment,
    name varchar(150) default null,
    acronym char(2) default null,
    created_at timestamp null default current_timestamp(),
    updated_at timestamp null default null,
    deleted_at timestamp null default null
);
-- tabela de salva as cidades
create table cities (
    city_id int primary key not null auto_increment,
    state_id int not null,
    name varchar(150) default null,
    created_at timestamp null default current_timestamp(),
    updated_at timestamp null default null,
    deleted_at timestamp null default null,
    CONSTRAINT `fk_city_state`
        FOREIGN KEY (`state_id`)
        REFERENCES `states` (`state_id`)
);

-- tabela para salvar os clientes
create table clients (
    client_id int primary key not null auto_increment,
    name varchar(250) not null,
    created_at timestamp null default current_timestamp(),
    updated_at timestamp null default null,
    deleted_at timestamp null default null
);
-- inserts para teste
INSERT INTO clients (`name`) VALUES
    ('Paulo'),
    ('Pedro'),
    ('Luis'),
    ('José')
;

-- tabela para salvar os enderecos
create table adresses (
    address_id int primary key not null auto_increment,
    client_id int not null,
    city_id int not null,
    zip_code varchar(10) default null,
    street varchar(250) not null,
    district varchar(250) not null,
    complement varchar(250) default null,
    created_at timestamp null default current_timestamp(),
    updated_at timestamp null default null,
    deleted_at timestamp null default null,
    CONSTRAINT `fk_adresses_client`
        FOREIGN KEY (`client_id`)
        REFERENCES `clients` (`client_id`),
    CONSTRAINT `fk_adresses_cities`
        FOREIGN KEY (`city_id`)
        REFERENCES `cities` (`city_id`)
);

-- tabela para salvar os produtos
create table products (
    product_id int primary key not null auto_increment,
    name varchar(250) not null,
    price decimal(20,4) DEFAULT 0.0000,
    stock int default 0,
    created_at timestamp null default current_timestamp(),
    updated_at timestamp null default null,
    deleted_at timestamp null default null
);

-- inserts para teste
INSERT INTO products (name, price, stock) VALUES
    ('Maçã', '1.50', 10),
    ('Goiaba', '2.00', 20),
    ('Uva', '1.90', 30),
    ('Melancia', '3.00', 40)
;

-- tabela para salvar os pedidos
create table requests (
    request_id int primary key not null auto_increment,
    client_id int not null,
    created_at timestamp null default current_timestamp(),
    updated_at timestamp null default null,
    deleted_at timestamp null default null,
    CONSTRAINT `fk_requests_cleints`
        FOREIGN KEY (`client_id`)
        REFERENCES `clients` (`client_id`)
);

-- tabela para salvar os produtos e quantidade de cada pedido
create table requests_products (
    request_product_id int primary key not null auto_increment,
    request_id int not null,
    product_id int not null,
    amount int not null,
    price decimal(20,4) DEFAULT 0.0000,
    created_at timestamp null default current_timestamp(),
    updated_at timestamp null default null,
    deleted_at timestamp null default null,
    CONSTRAINT `fk_requests_products_requests`
        FOREIGN KEY (`request_id`)
        REFERENCES `requests` (`request_id`),
    CONSTRAINT `fk_requests_products_products`
        FOREIGN KEY (`product_id`)
        REFERENCES `products` (`product_id`)
);
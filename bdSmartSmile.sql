
-- banco de dados da clínica SmartSmile --
create database bdSmartSmile; -- criando database --
use bdSmartSmile; -- usando database --

-- criação da tabela usuário --
create table tbUsuario(
id_usuario int primary key auto_increment,
nome varchar (80) not null,
email varchar (80) not null, 
senha varchar (6) not null,
status_usuario boolean not null, -- se o usuário vai ser administrador ou não --
cpf varchar(11) not null,
telefone varchar(11) not null,
data_nascimento date not null
);

-- por enquanto sem outros dados pra acrescentar, mais tarde
-- criação tabela adm --
create table tbAdministrador(
    id_administrador int primary key,
    foreign key (id_administrador) references tbUsuario(id_usuario)
);

-- criação tabela dentista --
create table tbDentista(
    id_dentista int primary key,
    foreign key (id_dentista) references tbUsuario(id_usuario)
);

-- inserts para teste de usuário --
-- não vai o id pq ele é autoincrement
insert into tbUsuario (nome, email, senha, status_usuario, cpf, telefone, data_nascimento)
values ('Laura Cristine', 'laura@email.com', '123456', true, '12345678901', '11987654321', '2006-05-03');

insert into tbUsuario (nome, email, senha, status_usuario, cpf, telefone, data_nascimento)
values ('Maria Eduarda', 'maria@email.com', 'abc123', false, '98765432100', '11999887766', '2006-10-22');

insert into tbUsuario (nome, email, senha, status_usuario, cpf, telefone, data_nascimento)
values ('Eloá Vasconcelos', 'eloa@email.com', '654321', false, '32165498700', '21988776655', '2006-09-22');

-- tornando os usuários adm e dentista --
insert into tbAdministrador (id_administrador) values (1);
insert into tbDentista (id_dentista) values (2);

select * from tbUsuario;
select * from tbDentista;
select * from tbAdministrador;
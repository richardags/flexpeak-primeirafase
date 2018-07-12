<?php
    
    class DataBaseAPIConfig {
        //configurações do banco de dados mysqli

        const DB_CHARSET = 'utf8';

        //nome do host
        const DB_HOST = 'localhost';

        //usuário do banco de dados
        const DB_USER = 'username';

        //senha do banco de dados
        const DB_PASSWORD = 'password';

        //nome do banco de dados
        const DB_NAME = 'database_name';

        //nome das tabelas
        const TABLE_ALUNO = 'aluno';
        const TABLE_CURSO = 'curso';
        const TABLE_PROFESSOR = 'professor';

        //comando sql para criar o banco de dados
        const SQL_CREATE_DB = "CREATE DATABASE ".DataBaseAPIConfig::DB_NAME;

        //comando sql para criar a tabela aluno
        const SQL_CREATE_TABLE_ALUNO = "CREATE TABLE ".DataBaseAPIConfig::TABLE_ALUNO." (
                                        id_aluno INT AUTO_INCREMENT,
                                        nome VARCHAR(100) NOT NULL,
                                        data_nascimento DATE NOT NULL,
                                       	id_curso INT NOT NULL,
                                       	cep INT NOT NULL,
                                       	logradouro VARCHAR(100) NOT NULL,
                                        numero INT NOT NULL,
                                        bairro VARCHAR(100) NOT NULL,
                                        cidade VARCHAR(100) NOT NULL,
                                        estado VARCHAR(100) NOT NULL,
                                        data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                        primary key (id_aluno))";

        //comando sql para criar a tabela curso
        const SQL_CREATE_TABLE_CURSO = "CREATE TABLE ".DataBaseAPIConfig::TABLE_CURSO." (
                                        id_curso INT AUTO_INCREMENT,
                                        nome VARCHAR(100) NOT NULL,
                                        id_professor INT NOT NULL,
                                        data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                        primary key (id_curso))";


        //comando sql para criar a tabela professor
        const SQL_CREATE_TABLE_PROFESSOR = "CREATE TABLE ".DataBaseAPIConfig::TABLE_PROFESSOR." (
                                            id_professor INT AUTO_INCREMENT,
                                            nome VARCHAR(100) NOT NULL,
                                            data_nascimento DATE NOT NULL,
                                            data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                            primary key (id_professor))";
    }
?>
<?php

    //Throw mysqli_sql_exception for errors instead of warnings
    //mysqli_report(MYSQLI_REPORT_STRICT);
    
    class DataBaseAPI {

        static function escapeString($connection, $string){
            return mysqli_real_escape_string($connection, $string);    
        }

        function _mysqli_fetch_all(mysqli_result $result) {
            while ($row = $result->fetch_array())
                $rows[] = $row;
            return $rows;
        }
        
        static function open_database_connection() {
            //open connection
            $connection = mysqli_connect(DataBaseAPIConfig::DB_HOST, DataBaseAPIConfig::DB_USER, DataBaseAPIConfig::DB_PASSWORD);
            //check error
            if(mysqli_error($connection))
                throw new Exception(mysqli_error($connection));
            //return
            return $connection;
        }
        static function open_database() {
            //open connection
            $connection = mysqli_connect(DataBaseAPIConfig::DB_HOST,
                                        DataBaseAPIConfig::DB_USER,
                                        DataBaseAPIConfig::DB_PASSWORD,
                                        DataBaseAPIConfig::DB_NAME);
            //check error
            if(mysqli_error($connection))
                throw new Exception(mysqli_error($connection));
            //set charset
            mysqli_set_charset($connection, DataBaseAPIConfig::DB_CHARSET);
            //return
            return $connection;
        }


        static function create_mysqli_query($SQL){
            //open connection
            $connection = DataBaseAPI::open_database();
            //create mysqli query
            mysqli_query($connection, $SQL);
            //check error
            if(mysqli_error($connection))
                throw new Exception(mysqli_error($connection));
            //close connection
            mysqli_close($connection);
        }
        static function check_mysqli_query($SQL){
            //open connection
            $connection = DataBaseAPI::open_database();
            //create mysqli query
            mysqli_query($connection, $SQL);
            //check error
            if(mysqli_error($connection))
                throw new Exception(mysqli_error($connection));
            //check for affected rows
            $return = mysqli_affected_rows($connection) > 0 ? true : false;
            //close connection
            mysqli_close($connection);
            //return
            return $return;
        }
        static function get_rows_mysqli_query($SQL){
            //open connection
            $connection = DataBaseAPI::open_database();
            //create mysqli query
            $result = mysqli_query($connection, $SQL);
            //check error
            if(mysqli_error($connection))
                throw new Exception(mysqli_error($connection));
            //check for affected rows
            if(mysqli_affected_rows($connection) > 0)
                $return = function_exists('mysqli_fetch_all') ? mysqli_fetch_all($result, MYSQLI_BOTH) : _mysqli_fetch_all($result);
            else
                $return = NULL;
            //close connection
            mysqli_close($connection);
            //return
            return $return;
        }
        static function get_row_mysqli_query($SQL){
            $result = DataBaseAPI::get_rows_mysqli_query($SQL);
            return $result ? $result[0] : NULL;
        }

        /* INSTALLER */
        static function create_database() {
            //open connection
            $connection = DataBaseAPI::open_database_connection();
            //create database
            mysqli_query($connection, DataBaseAPIConfig::SQL_CREATE_DB);
            //check error
            if(mysqli_error($connection))
                throw new Exception(mysqli_error($connection));
            //close connection
            mysqli_close($connection);
        }
        static function create_table_aluno(){
            DataBaseAPI::create_mysqli_query(DataBaseAPIConfig::SQL_CREATE_TABLE_ALUNO);
        }
        static function create_table_curso(){
            DataBaseAPI::create_mysqli_query(DataBaseAPIConfig::SQL_CREATE_TABLE_CURSO);
        }
        static function create_table_professor(){
            DataBaseAPI::create_mysqli_query(DataBaseAPIConfig::SQL_CREATE_TABLE_PROFESSOR);
        }
    }

?>
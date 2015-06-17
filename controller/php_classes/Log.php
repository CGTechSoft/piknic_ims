<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author Sakkeer Hussain
 */
class Log {
    private static $handle;
    private static $mode = "a";
    private static $date_format = "Y-M-d, H:i:s (D)";


    private static function open_file() {
        Log::$handle = fopen(Constants::$project_root.'/'.Constants::$log_file_name, Log::$mode);
        if(!Log::$handle){
            header("Location: ".Constants::$error_page."?error_message=log_file_opening_failed");
        }
    }
    private static function close_file(){
        fclose(Log::$handle);
    }
    public static function i($tag, $description) {
        Log::open_file();
        $log = "\n\nINFO  => [". Log::get_time_stamp()."]\t[".Constants::$ip."]\t[".$tag."]  =>  [".$description."]\n";
        fwrite(Log::$handle, $log);
        Log::close_file();
    }
    public static function d($tag, $description) {
        Log::open_file();
        $log = "\n\nDEBUG => [". Log::get_time_stamp()."]\t[".Constants::$ip."]\t[".$tag."]  =>  [".$description."]\n";
        fwrite(Log::$handle, $log);
        Log::close_file();
    }
    public static function e($tag, $description) {
        Log::open_file();
        $log = "\n\nERROR => [". Log::get_time_stamp()."]\t[".Constants::$ip."]\t[".$tag."]  =>  [".$description."]\n";
        fwrite(Log::$handle, $log);
        Log::close_file();
    }
    public static function get_time_stamp() {
        return date(Log::$date_format, (time()+(5.5*60*60)));
    }
}
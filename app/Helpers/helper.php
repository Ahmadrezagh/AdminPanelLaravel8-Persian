<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function setting($name)
{
    return \App\Models\Setting::getValue($name);
}

function upload_file($file,$file_path = null,$name = null )
{
    $path = '/uploads'.$file_path;
    // User image name is = user's phone.img
    if($name)
    {
        $file_name = $name.".".$file->getClientOriginalExtension();
    }else
    {
        $file_name = $file->getClientOriginalName();
    }
    // Upload Image
    $file->move(public_path($path), $file_name);
    // Generate image path
    return strval($path."/".$file_name);
}
function upload_files($files = null , $file_path = null , $random_name = false )
{
    if($files != null )
    {
        $file_paths = [];
        foreach ($files as $file)
        {
            $path = upload_file($file,$file_path,$random_name ? Str::random(8) : null);
            array_push($file_paths,$path);
        }
        return $file_paths;
    }
}

function make_slug($string) {
    return preg_replace('/\s+/u','-',trim($string));
}

function DB_create($table,$data = [])
{
    DB::table($table)->insert($data);
}

function DB_update($table,$id,$data = [])
{
    DB::table($table)->where('id','=',$id)->update($data);
}

function DB_delete($table,$id,$column = null)
{
    DB::table($table)->where($column ? $column : 'id','=',$id)->delete();
}

if (!function_exists('try_catch_null')) {
    function try_catch_null($closure)
    {
        try {
            return $closure();
        } catch (Exception $ex) {
            return null;
        }
    }
}



<?php

namespace App\Helpers;

/**
 * Created by Iqbal Hasan.
 * User: Iqbal Hasan
 * Date: 04/01/2021
 * Time: 09:53 Am
 */

/* User Instruction *

use App\Helpers\GlobalFileUploadFunctoin;


$file_path      = 'folder name';
$attachment     =  $request->file('attachment_form_name');

$attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment_form_name');

*For Create
GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment_form_name', $attachment_name);

*For Update
$old_file_name = $Model->attachment_name;
GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment_form_name', $attachment_name, $old_file_name);

*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;

class GlobalFileUploadFunctoin{

  /*Check Dir Permission*/
    public static function is_dir_set_permission($directory){
        if(is_dir($directory)) {
            GlobalFileUploadFunctoin::check_permission($directory);
            return true;
        }
        else
        {
            GlobalFileUploadFunctoin::make_directory($directory);
            return true;
        }

    }

   /*Make Diretory*/
    protected static function make_directory($directory){
        File::makeDirectory($directory, 0755, true, true);
        return true;
    }

  /*Check Permission*/
    protected static function check_permission($directory){
        if(is_writable($directory))
        {
            return true;
        }
        else
        {
            GlobalFileUploadFunctoin::set_permission($directory);
            return true;
        }
    }

 /*Set Permission*/
    protected static function set_permission($directory){
        if(!is_dir($directory)){
            File::makeDirectory($directory, 0777, true, true);
            return true;
        }
        return false;
    }

/*+++++File validation+++++++*/
    public static function file_validation_and_return_file_name($request,$file_path,$uploads_name){

        $file = $request->file($uploads_name);
        $rules = array( $uploads_name => 'required|mimes:png,gif,jpeg,svg,tiff,pdf,doc,docx,tex,txt,rtf');
        $validator = Validator::make(array($uploads_name => $file), $rules);
        if ($validator->passes()) {
            $file_name =time().'.' . $file->getClientOriginalExtension();
        }
        if ($validator->fails()) {
            return ([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }
        return $file_name;
    }

    /*+++++Upload File++++++++++*/
    public static function file_upload($request, $file_path, $uploads_name=null, $file_name, $old_file=null){
        $file = $request->file($uploads_name);
        $fileDestinationPath = storage_path('uploads/'.$file_path.'/original');

        if ($file !=null) {
            if($file != "") {
                    if(file_exists($fileDestinationPath.'/'.$old_file) && $old_file != null ){
                        unlink($fileDestinationPath.'/'.$old_file);
                    }
                GlobalFileUploadFunctoin::is_dir_set_permission($fileDestinationPath);
                $file->move($fileDestinationPath,$file_name);
            }
        }
    }

    public static function fileUpload($request, $file_path, $uploads_name=null, $file_name, $old_file=null){
        $file = $request->file($uploads_name);
        $fileDestinationPath = storage_path('uploads/'.$file_path.'/original');

        if ($file !=null) {
            if($file != "") {
                    if(file_exists($fileDestinationPath.'/'.$old_file) && $old_file != null ){
                        unlink($fileDestinationPath.'/'.$old_file);
                    }
                GlobalFileUploadFunctoin::is_dir_set_permission($fileDestinationPath);
                $file->move($fileDestinationPath,$file_name);
            }
        }
    }

    /*+++++Multi File Uploads+++++++*/
    public static function multi_file_upload_validation_return_with_names($request, $uploads_name){
        $files = $request->file($uploads_name);
        $i = 0;
        $file_names = [];

        if ($files !=null) {
            if($files != "") {
                foreach ($files as $file) {
                    $i++;
                    $rules   = array( $uploads_name => 'required|mimes:png,gif,jpeg,svg,tiff,pdf,doc,docx,tex,txt,rtf');
                    $validator = Validator::make(array($uploads_name => $file), $rules);

                    if ($validator->passes()) {
                        $file_name = $i.time().'.' . $file->getClientOriginalExtension();
                        $file_names .= $file_name;
                    }

                    if ($validator->fails()) {
                        return ([
                            'success' => false,
                            'errors' => $validator->errors()
                        ]);
                    }                    
                }
            }
        }
        return $file_names;
    }

    /*+++++Upload File++++++++++*/
    public static function multi_file_upload($request, $file_path, $uploads_name, $file_names, $id_folder){
        $files                  = $request->file($uploads_name);
        $fileDestinationPath    = storage_path('uploads/'.$file_path.'/original/'.$id_folder);       

        if ($files !=null) {
            if($files != "") {
                foreach ($files as $key => $file) {

                    if (dir_exists( $fileDestinationPath) && is_dir($fileDestinationPath) ){
                        rmdir($fileDestinationPath);
                    } else {
                        //unlink($fileDestinationPath);
                    }
                    GlobalFileUploadFunctoin::is_dir_set_permission($fileDestinationPath);
                    $file->move($fileDestinationPath,$file_names[$key]);
                }
            }
        }
    }


}

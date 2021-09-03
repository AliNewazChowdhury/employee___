<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class DataArchiveController extends Controller
{
    private $dbConInfo = [];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->dbConInfo = config('database.connections.mysql');
    }

    public function getDbBackupFiles()
    {
        $dirPath = storage_path('db-backup');

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0755);
        }

        //Converting sql file into zile file
        $dir = new \DirectoryIterator($dirPath);
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isDot()) {
               continue; 
            }
            $fileName = $fileInfo->getFilename();
            $filePath = $dirPath . DIRECTORY_SEPARATOR .$fileName;

            $this->createZipfile($filePath, $fileName);
        }

        $dir = new \DirectoryIterator($dirPath);
        $fileList = [];
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isDot()) {
               continue; 
            }
            
            $fileName = $fileInfo->getFilename();
            $filePath = $dirPath . DIRECTORY_SEPARATOR .$fileName;

            $fileSize = filesize($filePath);
            $fileSize = round($fileSize / 1000000, 3);
            $fileNameParts = explode('-', $fileName);
            $len = count($fileNameParts);

            $stdObj = new \stdClass();
            $stdObj->file_name = $fileName;
            $stdObj->file_size = $fileSize;
            $stdObj->created_date = substr($fileNameParts[$len - 1], 0, 4) . '-' . $fileNameParts[$len - 2] . '-' . $fileNameParts[$len - 3];
            $fileList[$fileName] = $stdObj;
            krsort($fileList);
        }
        
        return response([
            'success' => true,
            'data' => array_values($fileList)
        ]);
    }

    public function dumpDB()
    {
        try {
            $mysqldumpPath = config('app.mysqldump_path');
            $host = $this->dbConInfo['host'];
            $user = $this->dbConInfo['username'];
            $pass = $this->dbConInfo['password'];
            $dbName = $this->dbConInfo['database'];
            $exportPathSql = 'irrigation-scheme-service-db-' . (new \DateTime())->format('d-m-Y') . '.sql';
            $exportPathZip = 'irrigation-scheme-service-db-' . (new \DateTime())->format('d-m-Y') . '.zip';
            $exportPathSql = storage_path('db-backup'. DIRECTORY_SEPARATOR  . $exportPathSql);
            $exportPathZip = storage_path('db-backup'. DIRECTORY_SEPARATOR  . $exportPathZip);

            //Please do not change the following points
            //Export of the database and output of the status
            //$command= '"D:\xampp\mysql\bin\mysqldump.exe"' . ' --opt -h' .$host .' -u' .$user .' -p' .$pass .' ' .$dbName .' > ' .$exportPath;
            $command= '"'. $mysqldumpPath .'"' . ' --opt -h' .$host .' -u' .$user .' -p' .$pass .' ' .$dbName .' > ' .$exportPathSql;
            // $command= '"D:\xampp\mysql\bin\mysql\mysqldump.exe"' . 'mysqldump --opt -h' .$host .' -u' .$user .' -p' .$pass .' ' .$dbName .' > ' .$exportPath;
            $output = [];
            $resultCode = null;
            Log::info("DB backup started:");

            exec($command, $output, $resultCode);
            
            Log::info("Backup output:");
            Log::info(implode(',', $output));
            Log::info('Backup result:' . $resultCode . "\nBackup end");
            
        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => config('app.env') == 'production' ? "" : $ex->getMessage()
            ]);
            return $ex->getMessage();
        }

        return response([
            'success' => true,
            'message' => 'Db backup successful',
            'data' => 'my-backup.sql'
        ]);
    }
    
    /** This download route will download file which has path like 
     * file_name
     **/
  public function downloadBackupDb(Request $request)
  {
    return response()->download(storage_path('db-backup/' . $request->file_name));
  }

  public function deleteDbBackupFile(Request $request)
  {
    $fullPath = storage_path("db-backup/{$request->file_name}");

    if (empty($request->file_name) || !file_exists($fullPath)) {
        return response([
            'success' => false,
            'message' => 'fileNotFound'
        ]);
    }

    unlink($fullPath);

    return response([
        'success' => true,
        'message' => 'File deleted successfully'
    ]);
  }

  private function createZipfile($filePath, $fileName)
  {
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    if ($ext === 'zip') {
        return $filePath;
    }

    $zipFilePath = str_ireplace('sql', 'zip', $filePath);
    $zip = new \ZipArchive();

    if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
        $zip->addFile($filePath, $fileName);
        $zip->close();
        unlink($filePath);
        return $zipFilePath;
    } else {
        return $filePath;
    }
  }

  private function createZip()
  {
    $zip = new \ZipArchive;
    // echo $zip->open('test_new.zip', \ZipArchive::CREATE);
    $path = storage_path();
    // echo $path;
    if ($zip->open($path . '/test_new.zip', \ZipArchive::CREATE) === TRUE)
    {
        // Add files to the zip file
        $zip->addFile($path.'/my-backup.sql', 'my-backups.sql');
     
        // Add random.txt file to zip and rename it to newfile.txt
        $zip->addFile($path .'/random.txt', 'newfile.txt');
     
        // Add a file new.txt file to zip using the text specified
        $zip->addFromString('new.txt', 'text to be added to the new.txt file');
     
        // All files are added, so close the zip file.
        $zip->close();
    }
  }
  
}

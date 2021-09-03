<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
  /** This download route will download file which has path like 
   * uploads/file_name or something like that
   * where full file in the project is like storage/uploads/file_name
   **/
    public function downloadAttachment(Request $request)
    {
      return response()->download(base_path('storage/' . $request->file));
    }

    //
    public function generateBase64Font()
    {
        try {
            // $filePath = 'fonts/Nikosh.ttf';
            $filePath = 'fonts/Noto_Sans_Bengali-Bold.ttf';
            $filePath = 'fonts/TonnyMJ_Italic.ttf';
            $filePath = 'fonts/SutonnyMJ_Bold_Italic.ttf';
            $fullPath = base_path('public/'. $filePath);

            $b64 = base64_encode(file_get_contents($fullPath));
          } catch (\Exception $ex) {
            return [
              'success' => false,
              'message' => "Failed to get fiel. Error: {$ex->getMessage()}"
            ];
          }

          return [
            'success' => true,
            'data' => $b64
          ];
    }
}

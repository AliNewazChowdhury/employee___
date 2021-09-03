<?php

namespace App\Http\Controllers;

class ExampleController extends Controller
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

<?php
namespace App\Library;

use DB;

class PdfImage
{
  public static function getBase64Image($request)
  {
      $result = ['left_logo' => '', 'right_logo' => '', 'message' => ''];

      $leftLogo = self::convertImage($request->left_logo);
      $rightLogo = self::convertImage($request->right_logo);
      
      if ($leftLogo['success']) {
        $result['left_logo'] = $leftLogo['data'];
      } else {
          $result['message'] = $leftLogo['message'];
      }

      if ($rightLogo['success']) {
        $result['right_logo'] = $rightLogo['data'];
      } else {
          $result['message'] .= $rightLogo['message'];
      }

      return $result;
  }

  public static function convertImage($filePath)
  {
    try {      
      $fullPath = base_path('public/'. $filePath);
      
      $type = pathinfo(base_path('public/'. $fullPath), PATHINFO_EXTENSION);
      
      if (strtolower($type) === 'jpg' || strtolower($type) === 'jpeg') {
        $img = imagecreatefromjpeg($fullPath);
        ob_start();
        imagejpeg($img);
        $bin = ob_get_clean();
        $b64 = base64_encode($bin);
      } elseif (strtolower($type) === 'png') {
        $img = imagecreatefrompng($fullPath);
        ob_start();
        imagepng($img);
        $bin = ob_get_clean();
        $b64 = base64_encode($bin);
      } else {
          return [
              'success' => false,
              'message' => 'File does not exist'
          ];
      }
    } catch (\Exception $ex) {
      return [
        'success' => false,
        'message' => "Failed to get image {$ex->getMessage()}"
      ];
    }
    
    return [
      'success' => true,
      'data' => $b64
    ];
  }
}
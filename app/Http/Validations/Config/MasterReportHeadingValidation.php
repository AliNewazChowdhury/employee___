<?php
namespace app\Http\Validations\Config;

use Validator;

class MasterReportHeadingValidation
{
    /**
     * Master Report Heading Validation
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'heading'          => 'required|unique:master_report_headers,heading,'.$id,
            'org_id'           => 'required',
        ]);

        if ($validator->fails()) {
            return([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }
        return ['success'=>true]; 
    }
}
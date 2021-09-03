<?php

namespace App\Http\Controllers\WaterTesting;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\GlobalFileUploadFunctoin;
use App\Models\WaterTesting\FarmerWaterSamples;
use App\Models\WaterTesting\FarmerWaterTestRejects;
use App\Models\WaterTesting\FarmerWaterTestReports;
use App\Models\WaterTesting\FarmerWaterTestApplication;
use App\Models\Payment\IrrigationPayment;
use App\Models\FarmerProfile\FarmerBasicInfos;
use App\Models\Config\MasterLaboratory;
use App\Http\Validations\WaterTesting\FarmerWaterSamplesValidations;
use App\Http\Validations\WaterTesting\FarmerWaterTestRejectsValidations;
use App\Http\Validations\WaterTesting\FarmerWaterTestReportsValidations;
use App\Http\Validations\WaterTesting\FarmerWaterTestApplicationValidations;
use App\Http\Validations\FarmerOperator\PaymentValidation;
use App\Library\EkpayLibrary;
use App\Library\SmsLibrary;
use Validator;

class FarmerWaterTestApplicationController extends Controller
{
     public function __construct()
    {
        //
    }

    /**
     * get all Farmer Water Test Application
     */
    public function index(Request $request)
    {
        $query = FarmerWaterTestApplication::with(['waterTestReports','payment' => function ($q) {
            return $q->where('application_type', 4);
        }]);
        $query->whereFarmerId($request->auth_id);

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', $request->far_village);
        }

        if ($request->to_date) {
            $query = $query->whereDate('to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Farmer water test application list',
            'data' => $list
        ]);
    }

    public function singleDetails($id){
        return FarmerWaterTestApplication::with(['waterTestReports','payment' => function ($q) {
            return $q->where('application_type', 4);
        }])
                                    ->where('id', $id)
                                    ->first();
        // return FarmerWaterTestApplication::find($id)->first();
    }

    /*get All List*/
    public function listAll(Request $request)
    {   return $request;
        $query = DB::table('far_water_test_apps')
                      ->select('far_water_test_apps.*');

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', $request->far_village);
        }

        if ($request->to_date) {
            $query = $query->whereDate('to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        $list = $query->get();

	    return response([
            'success' => true,
            'message' => 'Farmer water test application list',
            'data' => $list
        ]);
    }


     /*get Drinking Water testing All List*/
    public function drinkingWaterTestinglist(Request $request)
    {
        // $query = DB::table('far_water_test_apps')
        //               ->select('far_water_test_apps.*')
        //               ->where('testing_type_id', 1);
        $query = FarmerWaterTestApplication::with(['waterTestReports','payment' => function ($q) {
            return $q->where('application_type', 4);
        }])
                                            ->where('testing_type_id', 1)
                                            ->where('payment_status', 1);
        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', $request->far_village);
        }

        if ($request->to_date) {
            $query = $query->whereDate('to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Farmer drinking water test application list',
            'data' => $list
        ]);
    }


    /*get Drinking Water testing All List*/
    public function drinkingWaterTestinglistAll(Request $request)
    {
        $query = DB::table('far_water_test_apps')
                      ->select('far_water_test_apps.*')
                      ->where('testing_type_id', 1);
        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', $request->far_village);
        }

        if ($request->to_date) {
            $query = $query->whereDate('to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Farmer drinking water test application all list',
            'data' => $list
        ]);
	}

    /*get drinking Water samples All List*/
    public function waterTestingSampleslist(Request $request)
    {   
         $query = DB::table('far_water_test_apps')
                    ->join('far_water_samples','far_water_test_apps.id', '=','far_water_samples.far_water_test_apps_id')
                    ->join('master_laboratories','far_water_samples.laboratory_id', '=','master_laboratories.id')
                    ->select('far_water_test_apps.*',
                            'far_water_samples.id as smaple_id',
                            'far_water_samples.note',
                            'far_water_samples.note_bn',
                            'far_water_samples.laboratory_id',
                            'far_water_samples.far_water_test_apps_id',
                            'master_laboratories.laboratory_name',
                            'master_laboratories.laboratory_name_bn')
                        ->where('far_water_test_apps.status', 3);

        if ($request->org_id) {
            $query = $query->where('far_water_test_apps.org_id', $request->org_id);
        }

        if ($request->office_id && $request->office_id != 0) {
            $query = $query->where('far_water_test_apps.office_id', $request->office_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('far_water_test_apps.farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('far_water_test_apps.email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('far_water_test_apps.sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('far_water_test_apps.sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('far_water_test_apps.testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_water_test_apps.far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_water_test_apps.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_water_test_apps.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_water_test_apps.far_union_id', $request->far_union_id);
        }

        if ($request->to_date) {
            $query = $query->whereDate('far_water_test_apps.to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('far_water_test_apps.from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        if(count($list) > 0) {
            return response([
                'success' => true,
                'message' => 'Farmer water samples list',
                'data' => $list
            ]);
        } else {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
        
    }

    /*get drinking Water samples All List*/
    public function waterTestingSampleslistAll(Request $request)
    {
        $query = DB::table('far_water_test_apps')
                    ->join('far_water_samples','far_water_test_apps.id', '=','far_water_samples.far_water_test_apps_id')
                    ->join('master_laboratories','far_water_samples.laboratory_id', '=','master_laboratories.id')
                    ->select('far_water_test_apps.*',
                            'far_water_samples.*',
                            'master_laboratories.laboratory_name',
                            'master_laboratories.laboratory_name_bn'
                        );

        if ($request->org_id) {
            $query = $query->where('far_water_test_apps.org_id', $request->org_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('far_water_test_apps.farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('far_water_test_apps.email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('far_water_test_apps.sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('far_water_test_apps.sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('far_water_test_apps.testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_water_test_apps.far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_water_test_apps.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_water_test_apps.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_water_test_apps.far_union_id', $request->far_union_id);
        }

        if ($request->to_date) {
            $query = $query->whereDate('far_water_test_apps.to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('far_water_test_apps.from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->status) {
            $query = $query->where('far_water_test_apps.status', $request->status);
        }

        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Farmer drinking water samples all list',
            'data' => $list
        ]);

    }

    /*get irrigation Water testing All List*/
	public function irrigationWaterTestinglist(Request $request)
	{
	    // $query = DB::table('far_water_test_apps')
	    //               ->select('far_water_test_apps.*')
	    //               ->where('testing_type_id', 2);
        $query = FarmerWaterTestApplication::with(['waterTestReports','payment' => function ($q) {
            return $q->where('application_type', 4);
        }])
                                            ->where('testing_type_id', 2)
                                            ->where('payment_status', 1);
        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', $request->far_village);
        }

        if ($request->to_date) {
            $query = $query->whereDate('to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

	    $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Farmer irrigation water test application list',
            'data' => $list
        ]);
	}

	/*get irrigation Water testing All List*/
	public function irrigationWaterTestinglistAll(Request $request)
	{
	    $query = DB::table('far_water_test_apps')
	                  ->select('far_water_test_apps.*')
	                  ->where('testing_type_id', 2);

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', $request->far_village);
        }

        if ($request->to_date) {
            $query = $query->whereDate('to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

	    $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Farmer irrigation water test application all list',
            'data' => $list
        ]);
	}


	/*get industrial Water testing All List*/
    public function industrialWaterTestinglist(Request $request)
    {
        // $query = DB::table('far_water_test_apps')
        //               ->select('far_water_test_apps.*')
        //               ->where('testing_type_id', 3);
        $query = FarmerWaterTestApplication::with(['waterTestReports','payment' => function ($q) {
            return $q->where('application_type', 4);
        }])
                                            ->where('testing_type_id', 3)
                                            ->where('payment_status', 1);
        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', $request->far_village);
        }

        if ($request->to_date) {
            $query = $query->whereDate('to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }
        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Farmer industrial water test application list',
            'data' => $list
        ]);
    }

    /*get industrial Water testing All List*/
    public function industrialWaterTestinglistAll(Request $request)
    {
        $query = DB::table('far_water_test_apps')
                      ->select('far_water_test_apps.*')
                      ->where('testing_type_id', 3);
        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', $request->far_village);
        }

        if ($request->to_date) {
            $query = $query->whereDate('to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Farmer industrial water test application all list',
            'data' => $list
        ]);


    }
    /**
     * Farmer Water Test Application store
     */
    public function store(Request $request)
    {
        $validationResult = FarmerWaterTestApplicationValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $orgId           = $request->org_id;
        $testingTypeId   = $request->testing_type_id;

        $getSampleSerial    = DB::table('far_water_test_apps')
	                            ->select('sample_serial')
	                            ->where('org_id', $orgId)
                        		->where('testing_type_id', $testingTypeId)
    							->get();

        $getLastSampleSerial = DB::table('far_water_test_apps')
                            		->select('sample_serial')->latest()
                                    ->orderBy('id','desc')
                                    ->first();

        if($getLastSampleSerial !=null && $getSampleSerial != null){

        	$countData = 1;

        	foreach ($getSampleSerial as $key => $value) {
        		$countData = $key;
        	}

        	$countData = $countData + 1;
		    $currentDataNo          = (int)$countData + 1;
            $sampleSerial = $getLastSampleSerial->sample_serial;
            $application_id = "wtappid#".$sampleSerial;

            if( $sampleSerial !="" ){
                $sampleSerial+= 1;
                $sampleNumber = $orgId.$testingTypeId.$sampleSerial.$currentDataNo;
            }
        } elseif($getSampleSerial == null ) {
        	if($getLastSampleSerial != null){
			    $currentDataNo =  1;
	            $sampleSerial = $getLastSampleSerial->sample_serial;
	            if( $sampleSerial !="" ){
	                $sampleSerial = $sampleSerial + 1;
	                $sampleNumber = $orgId.$testingTypeId.$sampleSerial.$currentDataNo;
                    $application_id = "wtappid#".$sampleSerial;
	            }

        	}

        } else {
        	$currentDataNo = 1;
            $sampleSerial  = 10000;
            $sampleNumber  = $orgId.$testingTypeId.$sampleSerial.$currentDataNo;
            $application_id = "wtappid#".$sampleSerial;
        }
        DB::beginTransaction();

        try {

            $farmerWaterTestApplication                     = new FarmerWaterTestApplication();
            $farmerWaterTestApplication->org_id             = (int)$orgId;
            $farmerWaterTestApplication->farmer_id          = (int)user_id();
            $farmerWaterTestApplication->email              = username();
            $farmerWaterTestApplication->name               = $request->name;
            $farmerWaterTestApplication->name_bn            = $request->name_bn;
            $farmerWaterTestApplication->sample_number      = $sampleNumber;
            $farmerWaterTestApplication->sample_serial      = (int)$sampleSerial;
            $farmerWaterTestApplication->testing_type_id    = (int)$testingTypeId;
            $farmerWaterTestApplication->far_division_id    = (int)$request->far_division_id;
            $farmerWaterTestApplication->far_district_id    = (int)$request->far_district_id;
            $farmerWaterTestApplication->far_upazilla_id    = (int)$request->far_upazilla_id;
            $farmerWaterTestApplication->far_union_id       = (int)$request->far_union_id;
            $farmerWaterTestApplication->office_id       = (int)$request->office_id;
            $farmerWaterTestApplication->far_village        = $request->far_village;
            $farmerWaterTestApplication->far_village_bn     = $request->far_village_bn;
            $farmerWaterTestApplication->from_date          = $request->from_date;
            $farmerWaterTestApplication->to_date            = $request->to_date;
            $farmerWaterTestApplication->application_id     = $application_id;
            $farmerWaterTestApplication->water_testing_parameter_id     = json_encode($request->water_testing_parameter_id);
            $farmerWaterTestApplication->save();

            save_log([
                'data_id'    => $farmerWaterTestApplication->id,
                'table_name' => 'far_pump_opt_rejects'
            ]);

            $transaction_no                          = strtoupper(uniqid());

            $irrigation_payment                      = new IrrigationPayment();
            $irrigation_payment->org_id              = (int)$orgId;
            $irrigation_payment->master_payment_id   = $request->payment['master_payment_id'];
            $irrigation_payment->farmer_id           = $farmerWaterTestApplication->farmer_id;
            $irrigation_payment->far_application_id  = $farmerWaterTestApplication->id;
            $irrigation_payment->application_type    = 4;
            $irrigation_payment->payment_type_id     = $testingTypeId;
            $irrigation_payment->amount              = $request->payment['amount'];
            $irrigation_payment->mac_addr             = strtok(exec("getmac"), ' '); 
            $irrigation_payment->trnx_currency       = "BDT";
            $irrigation_payment->transaction_no      = $transaction_no;
            $irrigation_payment->status              = 1;
            $irrigation_payment->pay_status          = "pending";
            $irrigation_payment->save();
            save_log([
                'data_id'   => $irrigation_payment->id,
                'table_name'=> 'irrigation_payments'
            ]);

            DB::commit();
            if ($request->is_bypass == 1) {
                $ekpay_payment = new EkpayLibrary();
                return $ekpay_payment->defaultSuccess($transaction_no);
            }

            if($request->final_pay == 1) {
                $basic_info = FarmerBasicInfos::whereFarmerId(user_id())->first();

                $pay_info['s_uri']          = config('app.base_url.project_url').'water-testing-request-list/success-other';
                $pay_info['f_uri']          = config('app.base_url.project_url').'water-testing-request-list/decline';
                $pay_info['c_uri']          = config('app.base_url.project_url').'water-testing-request-list/cancel';
                $pay_info['cust_id']        = (int)user_id();
                $pay_info['cust_name']      = $farmerWaterTestApplication->name;
                $pay_info['cust_mobo_no']   = username();
                $pay_info['cust_email']     = $basic_info->email;
                $pay_info['cust_mail_addr'] = $basic_info->far_village;

                $pay_info['trnx_id']        = $transaction_no;
                $pay_info['trnx_amt']       =  $request->payment['amount'];
                $pay_info['trnx_currency']  = 'BDT';
                $pay_info['ord_id']         = $irrigation_payment->id;
                $pay_info['ord_det']        = date('Y-m-d');

                $ekpay_payment = new EkpayLibrary();
                return $ekpay_payment->ekpay_payment($pay_info);
            } else {
                return response([
                    'success' => true,
                    'message' => 'Data save successfully',
                    'data'    => $farmerWaterTestApplication
                ]);
            }

        } catch (\Exception $ex) {
            DB::rollback();
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }
    }

    /**
     * Farmer Water Test Application by Admin store
     */
    public function adminStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'org_id'            => 'required',
            'laboratory_id'     => 'required',
            'testing_type_id'   => 'required',
            'from_date'         => 'required',
            'to_date'           => 'required'
        ]);

        if ($validator->fails()) {
            return([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }

        $orgId               = $request->org_id;
        $testingTypeId       = $request->testing_type_id;

        $getSampleSerial     = DB::table('far_water_test_apps')
                                ->select('sample_serial')
                                ->where('org_id', $orgId)
                                ->where('testing_type_id', $testingTypeId)
                                ->get();

        $getLastSampleSerial = DB::table('far_water_test_apps')
                                    ->select('sample_serial')->latest()
                                    ->orderBy('id','desc')
                                    ->first();

        if($getLastSampleSerial !=null && $getSampleSerial != null){
            $countData = 1;

            foreach ($getSampleSerial as $key => $value) {
                $countData = $key;
            }

            $countData = $countData + 1;
            $currentDataNo          = (int)$countData + 1;
            $sampleSerial = $getLastSampleSerial->sample_serial;
            $application_id = "wtappid#".$sampleSerial;

            if( $sampleSerial !="" ){
                $sampleSerial+= 1;
                $sampleNumber = $orgId.$testingTypeId.$sampleSerial.$currentDataNo;
            }
        } elseif($getSampleSerial == null ) {
            if($getLastSampleSerial != null){
                $currentDataNo =  1;
                $sampleSerial = $getLastSampleSerial->sample_serial;
                if( $sampleSerial !="" ){
                    $sampleSerial = $sampleSerial + 1;
                    $sampleNumber = $orgId.$testingTypeId.$sampleSerial.$currentDataNo;
                    $application_id = "wtappid#".$sampleSerial;
                }

            }

        } else {
            $currentDataNo = 1;
            $sampleSerial  = 10000;
            $sampleNumber  = $orgId.$testingTypeId.$sampleSerial.$currentDataNo;
            $application_id = "wtappid#".$sampleSerial;
        }
        DB::beginTransaction();

        try {

            $farmerWaterTestApplication                     = new FarmerWaterTestApplication();
            $farmerWaterTestApplication->org_id             = (int)$orgId;
            $farmerWaterTestApplication->farmer_id          = $request->farmer_id??null;
            $farmerWaterTestApplication->email              = $request->email??null;
            $farmerWaterTestApplication->name               = $request->name??null;
            $farmerWaterTestApplication->name_bn            = $request->name_bn??null;
            $farmerWaterTestApplication->sample_number      = $sampleNumber;
            $farmerWaterTestApplication->sample_serial      = (int)$sampleSerial;
            $farmerWaterTestApplication->testing_type_id    = (int)$testingTypeId;
            $farmerWaterTestApplication->far_division_id    = (int)$request->far_division_id??null;
            $farmerWaterTestApplication->far_district_id    = (int)$request->far_district_id??null;
            $farmerWaterTestApplication->far_upazilla_id    = (int)$request->far_upazilla_id??null;
            $farmerWaterTestApplication->far_union_id       = (int)$request->far_union_id??null;
            $farmerWaterTestApplication->far_village        = $request->far_village??null;
            $farmerWaterTestApplication->far_village_bn     = $request->far_village_bn??null;
            $farmerWaterTestApplication->from_date          = $request->from_date;
            $farmerWaterTestApplication->to_date            = $request->to_date;
            $farmerWaterTestApplication->application_id     = $application_id;
            $farmerWaterTestApplication->status           = 3;
            $farmerWaterTestApplication->water_testing_parameter_id     = json_encode($request->water_testing_parameter_id);

            if($farmerWaterTestApplication->save())
            {
                save_log([
                    'data_id'    => $farmerWaterTestApplication->id,
                    'table_name' => 'far_pump_opt_rejects'

                ]);

                $waterTestAppsId = $farmerWaterTestApplication->id;
                $farmerWaterSamples                            = new FarmerWaterSamples();
                $farmerWaterSamples->far_water_test_apps_id    =  $waterTestAppsId;
                $farmerWaterSamples->laboratory_id             = $request->laboratory_id;
                $farmerWaterSamples->save();

                save_log([
                    'data_id'   => $farmerWaterSamples->id,
                    'table_name'=> 'far_water_samples'
                ]);
            }

            DB::commit();

            return response([
                    'success' => true,
                    'message' => 'Data save successfully',
                    'data'    => $farmerWaterTestApplication
                ]);

        } catch (\Exception $ex) {
            DB::rollback();
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }
    }

    /*get drinking Water samples All List*/
    public function adminWaterTestingSamplesList(Request $request)
    {
        $query = DB::table('far_water_test_apps')
                    ->leftJoin('far_water_samples','far_water_test_apps.id', '=','far_water_samples.far_water_test_apps_id')
                    ->leftJoin('master_laboratories','far_water_samples.laboratory_id', '=','master_laboratories.id')
                    ->select('far_water_test_apps.*',
                            'far_water_samples.far_water_test_apps_id',
                            'far_water_samples.laboratory_id',
                            'master_laboratories.laboratory_name',
                            'master_laboratories.laboratory_name_bn'
                        );

        if ($request->sample_number) {
            $query = $query->where('far_water_test_apps.sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('far_water_test_apps.sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('far_water_test_apps.testing_type_id', $request->testing_type_id);
        }

        if ($request->to_date) {
            $query = $query->whereDate('far_water_test_apps.to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('far_water_test_apps.from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->laboratory_id) {
            $query = $query->where('far_water_samples.laboratory_id', $request->laboratory_id);
        }

        $query = $query->where('far_water_test_apps.farmer_id', NULL)
                        // ->where('far_water_test_apps.status', 3)
                        ->paginate($request->per_page ?? 10);

        return response([
            'success' => true,
            'message' => 'Admin water testing samples list',
            'data' => $query
        ]);

    }

 	/**
     * Farmer Water Test Application Update
     */
    public function update(Request $request, $id)
    {
        $validationResult = FarmerWaterTestApplicationValidations:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $farmerWaterTestApplication = FarmerWaterTestApplication::find($id);

         if (!$farmerWaterTestApplication) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $orgId           = $request->org_id;
        $testingTypeId   = $request->testing_type_id;

        $getSampleSerial    = DB::table('far_water_test_apps')
	                            ->select('sample_serial')
	                            ->where('org_id', $orgId)
                        		->where('testing_type_id', $testingTypeId)
    							->get();

        $sampleSerial    = $farmerWaterTestApplication->sample_serial;

        if ($getSampleSerial != null) {
        	$countData = 0;
        	foreach ($getSampleSerial as $key => $value) {
        		$countData = $key;
        	}

        	$countData              = $countData + 1;
		    $currentDataNo          = (int)$countData + 1;
            $sampleNumber           = $orgId.$testingTypeId.$sampleSerial.$currentDataNo;

        } else {
		    $currentDataNo =  1;
            $sampleNumber = $orgId.$testingTypeId.$sampleSerial.$currentDataNo;
        }
        DB::beginTransaction();

        try {

            $farmerWaterTestApplication->org_id             = (int)$orgId;
            $farmerWaterTestApplication->name               = $request->name??$farmerWaterTestApplication->name;
            $farmerWaterTestApplication->name_bn            = $request->name_bn??$farmerWaterTestApplication->name_bn;
            $farmerWaterTestApplication->sample_number      = $sampleNumber;
            $farmerWaterTestApplication->testing_type_id    = (int)$testingTypeId;
            $farmerWaterTestApplication->far_division_id    = (int)$request->far_division_id??$farmerWaterTestApplication->far_division_id;
            $farmerWaterTestApplication->far_district_id    = (int)$request->far_district_id??$farmerWaterTestApplication->far_district_id;
            $farmerWaterTestApplication->far_upazilla_id    = (int)$request->far_upazilla_id??$farmerWaterTestApplication->far_upazilla_id;
            $farmerWaterTestApplication->far_union_id       = (int)$request->far_union_id??$farmerWaterTestApplication->far_union_id;
            $farmerWaterTestApplication->office_id          = (int)$request->office_id??$farmerWaterTestApplication->office_id;
            $farmerWaterTestApplication->far_village        = $request->far_village??$farmerWaterTestApplication->far_village;
            $farmerWaterTestApplication->far_village_bn     = $request->far_village_bn??$farmerWaterTestApplication->far_village_bn;
            $farmerWaterTestApplication->from_date          = $request->from_date??$farmerWaterTestApplication->from_date;
            $farmerWaterTestApplication->to_date            = $request->to_date??$farmerWaterTestApplication->to_date;
            $farmerWaterTestApplication->water_testing_parameter_id     = $request->water_testing_parameter_id;

            $farmerWaterTestApplication->update();

            $irrigation_payment = IrrigationPayment::find($request->payment['id']);
            if ($irrigation_payment) {
                $irrigation_payment->master_payment_id   = $request->payment['master_payment_id'];
                $irrigation_payment->amount              = $request->payment['amount'];
                $irrigation_payment->org_id              = (int)$orgId;
                $irrigation_payment->payment_type_id     = $testingTypeId;
                $irrigation_payment->save();
            }

            save_log([
                'data_id' => $farmerWaterTestApplication->id,
                'table_name' => 'pump_operators',
                'execution_type' => 1
            ]);

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollback();
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $farmerWaterTestApplication
        ]);
    }

    public function pendingPayment (Request $request) 
    {
        $validationResult = PaymentValidation::validate($request);
        if (!$validationResult['success']) {
            return response($validationResult);
        }
        $irrigation_payment = IrrigationPayment::find($request->id);
        $transaction_no     = strtoupper(uniqid());

        if ($irrigation_payment) {
            $irrigation_payment->transaction_no   = $transaction_no;
            $irrigation_payment->status   = 1;
            $irrigation_payment->save();
        }

        $basic_info = FarmerBasicInfos::whereFarmerId(user_id())->first();

        $pay_info['s_uri']          = config('app.base_url.project_url').'water-testing-request-list/success-other';
        $pay_info['f_uri']          = config('app.base_url.project_url').'water-testing-request-list/decline';
        $pay_info['c_uri']          = config('app.base_url.project_url').'water-testing-request-list/cancel';
        $pay_info['cust_id']        = (int)user_id();
        $pay_info['cust_name']      = $basic_info->name;
        $pay_info['cust_mobo_no']   = username();
        $pay_info['cust_email']     = $basic_info->email;
        $pay_info['cust_mail_addr'] = $basic_info->far_village;

        $pay_info['trnx_id']        = $transaction_no;
        $pay_info['trnx_amt']       = $irrigation_payment->amount;
        $pay_info['trnx_currency']  = 'BDT';
        $pay_info['ord_id']         = $irrigation_payment->id;
        $pay_info['ord_det']        = date('Y-m-d');

        if ($request->is_bypass == 1) {
            $ekpay_payment = new EkpayLibrary();
            return $ekpay_payment->defaultSuccess($transaction_no);
        }

        $ekpay_payment = new EkpayLibrary();
        return $ekpay_payment->ekpay_payment($pay_info);

    }

    /* status update As Processing*/
    public function updateAsProcessing($id)
    {
        $farmerWaterTestApplication = FarmerWaterTestApplication::find($id);

        if (!$farmerWaterTestApplication) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $current_status= $farmerWaterTestApplication->status;

        if( $current_status == 1 ){
            $farmerWaterTestApplication->status = 2;
            $farmerWaterTestApplication->update();

        } elseif($current_status > 2 && $current_status != 5 ){
            return response([
                'success' => true,
                'message' => 'Application status already uper lavel',
                'data'    => $farmerWaterTestApplication
            ]);
        } else {
            return response([
                'success' => true,
                'message' => 'Application status already processing or rejected',
                'data'    => $farmerWaterTestApplication
            ]);
        }

        save_log([
            'data_id' => $farmerWaterTestApplication->id,
            'table_name' => 'far_pump_opt_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Application status update as processing',
            'data'    => $farmerWaterTestApplication
        ]);
    }

    public function updateAsSendLab(Request $request,$id)
    {
        $farmerWaterTestApplication = FarmerWaterTestApplication::find($id);

        if (!$farmerWaterTestApplication) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $current_status= $farmerWaterTestApplication->status;

        if( $current_status == 3 ){
            return response([
                'success' => true,
                'message' => 'Water samples already send to lab',
                'data'    => $farmerWaterTestApplication
            ]);
        } else {

            $office_id = MasterLaboratory::find($request->laboratory_id)->office_id;

            $farmerWaterTestApplication->status     = 3;
            $farmerWaterTestApplication->office_id = $office_id;

            DB::beginTransaction();
            try{
                if($farmerWaterTestApplication->update()){
                    /*+++++++++++++++++*/
                     $validationResult = FarmerWaterSamplesValidations:: validate($request);
                        if (!$validationResult['success']) {
                            return response($validationResult);
                        }

                        $farmerWaterSamples                         = new FarmerWaterSamples();
                        $farmerWaterSamples->far_water_test_apps_id =  (int)$id;
                        $farmerWaterSamples->laboratory_id          =  (int)$request->laboratory_id;
                        $farmerWaterSamples->note                   =  $request->note;
                        $farmerWaterSamples->note_bn                =  $request->note_bn;
                        $farmerWaterSamples->save();
                        DB::commit();

                        /*+++++++++++*/
                        save_log([
                            'data_id' => $farmerWaterTestApplication->id,
                            'table_name' => 'far_water_test_apps',
                            'execution_type' => 2
                        ]);
                }

            }catch (\Exception $ex) {
                return response([
                    'success' => false,
                    'message' => 'Failed to save data.',
                    'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
                ]);
            }

	        return response([
	            'success' => true,
	            'message' => 'Water samples send to lab',
	            'data'    => $farmerWaterTestApplication
	        ]);
	    }
	}

	/* update As Reports Collected */
	public function updateAsReportsCollected(Request $request)
	{
        $id = $request->far_water_test_apps_id;

	    $farmerWaterTestApplication = FarmerWaterTestApplication::find($id);

	    if (!$farmerWaterTestApplication) {
	        return response([
	            'success' => false,
	            'message' => 'Data not found.'
	        ]);
	    }

	    $current_status= $farmerWaterTestApplication->status;

	    if( $current_status == 4 ){
	        return response([
	            'success' => true,
	            'message' => 'Application statas already reports collected',
	            'data'    => $farmerWaterTestApplication
	        ]);
	    } else {

	        $farmerWaterTestApplication->status = 4;

	        DB::beginTransaction();
	        try{
	            if($farmerWaterTestApplication->update()){
	                /*+++++++++++++++++*/
	                $validationResult = FarmerWaterTestReportsValidations:: validate($request);
                    if (!$validationResult['success']) {
                        return response($validationResult);
                    }

                    $file_path      = 'water-test-reports';
                    $attachment     =  $request->file('attachment');

                    $FarmerWaterTestReports                         = new FarmerWaterTestReports();
                    $FarmerWaterTestReports->far_water_test_apps_id =  (int)$id;
                    $FarmerWaterTestReports->memo_no                =  $request->memo_no;

                    if($attachment !=null && $attachment !=""){
                        $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment');
                    	$FarmerWaterTestReports->attachment        =  $attachment_name ? $attachment_name : null;
                    }

                    if($FarmerWaterTestReports->save()){
                         GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
                    }

                    DB::commit();
                	/*+++++++++++++++++*/
                     save_log([
                        'data_id' => $farmerWaterTestApplication->id,
                        'table_name' => 'far_water_test_apps',
                        'execution_type' => 2
                    ]);

	            }

	        }catch (\Exception $ex) {
	            return response([
	                'success' => false,
	                'message' => 'Failed to save data.',
	                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
	            ]);
	        }

	        return response([
	            'success' => true,
	            'message' => 'Application statas update as reports collected',
	            'data'    => $farmerWaterTestApplication
	        ]);
	    }
	}

    /* status update As Reported*/
    public function updateAsReported($id)
    {
        $farmerWaterTestApplication = FarmerWaterTestApplication::find($id);

        if (!$farmerWaterTestApplication) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $current_status= $farmerWaterTestApplication->status;

        if ($current_status < 4){
             return response([
                'success' => true,
                'message' => 'Application status pending or under processing',
                'data'    => $farmerWaterTestApplication
            ]);
        } elseif ($current_status == 4){
            $farmerWaterTestApplication->status = 5;
            $farmerWaterTestApplication->update();
        } else {
            return response([
                'success' => true,
                'message' => 'Application status already reported or rejected',
                'data'    => $farmerWaterTestApplication
            ]);
        }

        save_log([
            'data_id' => $farmerWaterTestApplication->id,
            'table_name' => 'far_water_test_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Application status update as Reported',
            'data'    => $farmerWaterTestApplication
        ]);
    }


    public function reportsCollectList(Request $request) 
    {
        $query = DB::table('far_water_test_apps')
                    ->join('far_water_samples','far_water_test_apps.id', '=','far_water_samples.far_water_test_apps_id')
                    ->join('far_water_test_reports','far_water_test_apps.id', '=','far_water_test_reports.far_water_test_apps_id')
                    ->join('master_laboratories','far_water_samples.laboratory_id', '=','master_laboratories.id')
                    ->select('far_water_test_apps.*',
                            'far_water_samples.*',
                            'far_water_test_reports.*',
                            'master_laboratories.laboratory_name',
                            'master_laboratories.laboratory_name_bn'
                        )
                    ->where('far_water_test_apps.status', 4);

        if ($request->org_id) {
            $query = $query->where('far_water_test_apps.org_id', $request->org_id);
        }
        if ($request->memo_no) {
            $query = $query->where('far_water_test_reports.memo_no', $request->memo_no);
        }

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', $request->far_village);
        }

        if ($request->to_date) {
            $query = $query->whereDate('to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Farmer drinking water samples repoted all list',
            'data' => $list
        ]);

    }

    public function totalTestRequest($orgId) {
        $query = DB::table('far_water_test_apps')
                    ->where('org_id', $orgId)
                    ->count();

        return response([
            'success' => true,
            'message' => 'Farmer drinking water samples repoted all list',
            'data' => $query
        ]);
    }

    public function pendingRequest($orgId) {
        $query = DB::table('far_water_test_apps')
                    ->where('org_id', $orgId)
                    ->where('status', 1)
                    ->count();

        return response([
            'success' => true,
            'message' => 'Farmer drinking water samples repoted all list',
            'data' => $query
        ]);
    }

    public function totalPublishedReport($orgId) {
        $query = DB::table('far_water_test_apps')
                    ->where('org_id', $orgId)
                    ->where('status', 5)
                    ->count();

        return response([
            'success' => true,
            'message' => 'Farmer drinking water samples repoted all list',
            'data' => $query
        ]);
    }

    public function getFileAttachment($attachment) {
        $path = storage_path().'/'.'uploads'.'/water-test-reports/original/'.$attachment;
        if (file_exists($path)) {
            return Response::download($path);
        }
    }



    public function reportedList(Request $request) {
        $query = DB::table('far_water_test_apps')
                    ->join('far_water_samples','far_water_test_apps.id', '=','far_water_samples.far_water_test_apps_id')
                    ->join('far_water_test_reports','far_water_test_apps.id', '=','far_water_test_reports.far_water_test_apps_id')
                    ->join('master_laboratories','far_water_samples.laboratory_id', '=','master_laboratories.id')
                    ->select('far_water_test_apps.*',
                            'far_water_samples.*',
                            'far_water_test_reports.*',
                            'master_laboratories.laboratory_name',
                            'master_laboratories.laboratory_name_bn'
                        )
                    ->where('far_water_test_apps.status', 5);

        if ($request->org_id) {
            $query = $query->where('far_water_test_apps.org_id', $request->org_id);
        }
        if ($request->memo_no) {
            $query = $query->where('far_water_test_reports.memo_no', $request->memo_no);
        }

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->sample_number) {
            $query = $query->where('sample_number', $request->sample_number);
        }

        if ($request->sample_serial) {
            $query = $query->where('sample_serial', $request->sample_serial);
        }

        if ($request->testing_type_id) {
            $query = $query->where('testing_type_id', $request->testing_type_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', $request->far_village);
        }

        if ($request->to_date) {
            $query = $query->whereDate('to_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('from_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Farmer drinking water samples repoted all list',
            'data' => $list
        ]);

    }

  /* status update As Rejected*/
    public function updateAsReject(Request $request, $id)
    {  
		$farmerWaterTestApplication = FarmerWaterTestApplication::find($id);

		if (!$farmerWaterTestApplication) {
		    return response([
		        'success' => false,
		        'message' => 'Data not found.'
		    ]);
		}

		$current_status= $farmerWaterTestApplication->status;


		if( $current_status == 6 ){
		    return response([
		        'success' => true,
		        'message' => 'Application statas already rejected',
		        'data'    => $farmerWaterTestApplication
		    ]);
		} else {
		    $farmerWaterTestApplication->status = 6;

		    DB::beginTransaction();

		    try{

		        if($farmerWaterTestApplication->save()){
		            /*+++++++++++++++++*/
		            $validationResult = FarmerWaterTestRejectsValidations:: validate($request);
	                if (!$validationResult['success']) {
	                    return response($validationResult);
	                }
	                $FarmerWaterTestRejects                         = new FarmerWaterTestRejects();
	                $FarmerWaterTestRejects->far_water_test_apps_id =  (int)$id;
	                $FarmerWaterTestRejects->note                   =  $request->note;
	                $FarmerWaterTestRejects->note_bn                =  $request->note_bn;

	                $FarmerWaterTestRejects->save();

                    if ($request->supervisor_phone != null) {
                        $smsData['mobile']  = $request->supervisor_phone;
                        $smsData['message'] = "Water testing drinking water Application (ID :". $farmerWaterTestApplication->application_id . ') is rejected';
                        $sms = new SmsLibrary();
                        $sms->sms_helper($smsData); 
                    }    

	                DB::commit();

	                /*+++++++++++++++++*/
	                save_log([
	                    'data_id' => $farmerWaterTestApplication->id,
	                    'table_name' => 'far_water_test_apps',
	                    'execution_type' => 2
	                ]);
		        }

		    }catch (\Exception $ex) {
		        return response([
		            'success' => false,
		            'message' => 'Failed to save data.',
		            'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
		        ]);
		    }

			return response([
			    'success' => true,
			    'message' => 'Application statas update as rejected',
			    'data'    => $farmerWaterTestApplication
			]);
		}
	}

}

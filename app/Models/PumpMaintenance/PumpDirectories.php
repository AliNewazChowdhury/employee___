<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class PumpDirectories extends Model
{
    protected $table ="pump_directories";

	protected $fillable = [
		'type_id', 
		'name',	
		'name_bn',	
		'village_name',	
		'village_name_bn',	
		'address',	
		'address_bn',	
		'latitude',	
		'longitude',	
		'mobile',	
		'email',	
		'attachment',	
		'document_name',	
		'document_name_bn',	
		'division_id',	
		'district_id',	
		'upazila_id',	
		'union_id',	
		'created_by',	
		'updated_by',
		'status',
		'created_at'
	];

	public function pumpDirectoryEquipments(){
    	return $this->hasMany(PumpDirectoryEquipments::class, 'pump_directory_id');
    }

}

<?php

namespace App\Repositories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CommonRepository
{

    public function getTotal($table, $whereField = null, $whereValue = null)
    {
        $tb = DB::table($table);

         if ($whereField && $whereValue) {
            $tb->where($whereField, $whereValue);
         }

        return $tb->count();
    }

     // Formatting Data by Status Table and Status
     public function getCountByStatus($table, $statusList, $whereField = null, $whereValue = null)
     {
         $tb = DB::table($table);

         if ($whereField && $whereValue) {
            $tb->where($whereField, $whereValue);
         }

         $items = $tb->select(DB::raw('count(*) as application_count, status'))
                 ->groupBy('status')
                 ->get()
                 ->toArray();

         foreach($items as $item) {
             $key = array_search($item->status, $statusList, true);
             if ($key) {
                 $formatted[$key] = $item->application_count;
             }
         }

         $formatted['total'] = DB::table($table)->count();

         return $formatted;
     }

}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'cron'], function () {
    Route::get('/fetch/docs/{fromYear?}', 'CronController@fetchDocs')->name('docs.update');
    Route::get('/fetch/content/{docId?}', 'CronController@fetchContent')->name('content.update');
});
Route::get('documents',function (Request $request){

    ## Read value
    $draw = $request->get('draw');
    $start = $request->get("start");
    $rowperpage = $request->get("length"); // Rows display per page

    $columnIndex_arr = $request->get('order');
    $columnName_arr = $request->get('columns');
    $order_arr = $request->get('order');
    $search_arr = $request->get('search');


    $columnIndex = $columnIndex_arr[0]['column']; // Column index
    $columnName = $columnName_arr[$columnIndex]['data']; // Column name
    $columnSortOrder = $order_arr[0]['dir']; // asc or desc
    $searchValue = $search_arr['value']; // Search value

    $documents = \App\Models\Doc::orderBy($columnName,$columnSortOrder)
        ->where('slug', 'like', '%' .$searchValue . '%')
        ->select('*')
        ->skip($start)
        ->take($rowperpage)
        ->get();
    foreach ($documents as $doc)
    {
        $link = \route('documents.show',$doc->id);
        $doc->slug = "<a href='$link'>$doc->slug</a>";
        $doc->nvd_url = "<a class='btn btn-primary' href='".$doc->nvd_url."'>مشاهده</a>";
        $doc->month = \Carbon\Carbon::create()->startOfMonth()->month($doc->month)->format('M');
    }
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => \App\Models\Doc::count(),
        "iTotalDisplayRecords" =>  \App\Models\Doc::count(),
        "aaData" => $documents,
    );
   return json_encode($response);
})->name('docs.api');

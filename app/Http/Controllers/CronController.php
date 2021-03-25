<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doc;
class CronController extends Controller
{
    public function fetchContent(Request $request)
    {
        Doc::query()->each(function (Doc $doc) {
            $doc->fetchContent();
        });
    }


    public function fetchDocs(Request $request)
    {
        Doc::fetchNews($request->input('fromYear') ?? now()->year);
    }
}

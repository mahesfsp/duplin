<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FindAffiliate;


class FindAffiliateController extends Controller
{
    public function index()
    {

      
        return view('home');
    }

    public function uploadFile(Request $request)
    {

        // Validation
        $request->validate([
            'file' => 'required|max:2048'
        ]);

        if ($request->file('file') && $request->file('file')->getClientMimeType() == 'text/plain') {
            $fileContent = file_get_contents($request->file('file'));
            $AddToEnd = "]";
            $AddToStart = "[";
            $fileContent = $AddToStart.rtrim(str_replace("}","},", $fileContent),',').$AddToEnd;            
            $my_array = json_decode($fileContent, true);
            foreach ($my_array as $arr) {

                $affiliate = new FindAffiliate;
                $affiliate->affiliate_id = $arr['affiliate_id'];
                $affiliate->latitude = $arr['latitude'];
                $affiliate->longitude = $arr['longitude'];
                $affiliate->name = $arr['name'];
                $affiliate->save();
            }

            $latitude = 53.3340285;
            $longitude = -6.2535495;
            $distance = 100;
            $affiliateList = FindAffiliate::select(\DB::raw("*,(6371  *
            acos(
            cos( radians(" . $latitude . ") ) *
            cos( radians( latitude ) ) *
            cos( radians( longitude ) - radians(" . $longitude . ") ) +
            sin( radians(" . $latitude . ")) *
            sin( radians( latitude ) )
            )
        )AS distance"))
                ->having('distance', '<', $distance)
                ->orderBy('affiliate_id');
            $finalLists=$affiliateList->get()->toArray();
            return view('result', compact('finalLists'));
        }
    }
}

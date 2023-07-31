<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // =========================================================
    //                       RENDER VIEWS
    // =========================================================

    public function manageMemberView()
    {
        return view('admin.members.members', [
            'pageTitle' => "Manage Member",
        ]);
    }


    // =========================================================
    //                       RENDER APIS
    // =========================================================

    public function getListOfMembers(){
        $members = Member::where('active', true)->orderBy('name')->get();
        $finalMembers = array();
    
        if($members->isNotEmpty()){
            foreach($members as $member){
                array_push($finalMembers, [
                    'name' => $member->name,
                    'type' => $member->type,
                ]);
            }
        }

        return response()->json([
            'status' => '200',
            'data' => $finalMembers,
        ], 200);
    }
}

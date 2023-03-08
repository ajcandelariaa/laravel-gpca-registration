<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // RENDER VIEWS
    public function manageMemberView()
    {
        $members = [
            [
                'name' => "AT",
                'sector' => 'TEST',
            ],
        ];
        return view('admin.member.members', [
            'pageTitle' => "Manage Member",
            "members" => $members,
        ]);
    }


    public function addMember()
    {
    }
}

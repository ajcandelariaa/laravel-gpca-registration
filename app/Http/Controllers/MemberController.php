<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Carbon\Carbon;
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

    public function exportListOfMembers(){
        $members = Member::get();
        $finalExcelData = array();

        foreach($members as  $member){
            array_push($finalExcelData, [
                'name' => iconv(mb_detect_encoding($member->name), 'UTF-8//IGNORE', $member->name),
                'type' => $member->type,
                'status' => $member->active ? 'Active' : 'Inactive',
            ]);
        }

        $currentDate = Carbon::now()->format('Y-m-d');
        $fileName = 'Registration Member List ' . '[' . $currentDate . '].csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        
        $columns = array(
            'Company Name',
            'Type',
            'Status',
        );

        $callback = function () use ($finalExcelData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($finalExcelData as $data) {
                fputcsv(
                    $file,
                    array(
                        $data['name'],
                        $data['type'],
                        $data['status'],
                    )
                );
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}

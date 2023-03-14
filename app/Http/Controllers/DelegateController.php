<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DelegateController extends Controller
{
    // RENDER VIEWS
    public function manageDelegateView(){
        return view('admin.delegates.delegate', [
            'pageTitle' => 'Manage Delegate',
        ]);
    }
}

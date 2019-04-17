<?php

namespace App\Http\Controllers;

use App\Subscription;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $subscriptions = Subscription::all();
        return view('home')->with(compact('subscriptions', 'user'));
    }

    public function pdf() {
        $pdf = PDF::loadView('pdf');
        return $pdf->stream('document.pdf');
    }

    public function dashboard() {
        return 'kurac';
    }

    public function domains($name)
    {
        $user = User::where('name', $name)->firstOrFail();
        return view('home', compact('user'));
    }
}

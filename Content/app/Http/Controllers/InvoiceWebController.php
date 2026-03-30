<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceWebController extends Controller
{
    public function index()
    {
        return view('invoices.index');
    }
}
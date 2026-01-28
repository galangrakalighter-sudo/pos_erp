<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockItem;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $items = StockItem::all();
        return view('admin.dashboard', compact('items'));
    }
}

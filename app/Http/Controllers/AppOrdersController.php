<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class AppOrdersController extends Controller
{
    public function index(): View
    {
        $orders = Order::latest()->get();

        return view('app.orders.index', compact('orders'));
    }

    public function show(int $id): View
    {
        $order = Order::findOrFail($id);

        return view('app.orders.show', compact('order'));
    }
}

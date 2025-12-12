<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Member;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $category = Category::count();
        $product = Product::count();
        $supplier = Supplier::count();
        $member = Member::count();
        $sales = Sales::sum('diterima');
        $expense = Expense::sum('nominal');
        $purchase = Purchase::sum('pay');

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $data_tanggal = array();
        $data_pendapatan = array();

        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

            $total_sales = Sales::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('pay');
            $total_purchase = Purchase::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('pay');
            $total_expense = Expense::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

            $pendapatan = $total_sales - $total_purchase - $total_expense;
            $data_pendapatan[] += $pendapatan;

            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }

        $tanggal_awal = date('Y-m-01');

        if (auth()->user()->level == 1) {
            return view('admin.dashboard', compact('category', 'product', 'supplier', 'member', 'sales', 'expense', 'purchase', 'tanggal_awal', 'tanggal_akhir', 'data_tanggal', 'data_pendapatan'));
        } else {
            return view('kasir.dashboard');
        }
    }
}
// visit "codeastro" for more projects!
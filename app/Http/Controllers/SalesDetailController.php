<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Sales;
use App\Models\SalesDetail;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class SalesDetailController extends Controller
{
    public function index()
    {
        $product = Product::orderBy('name_product')->get();
        $member = Member::orderBy('name')->get();
        $discount = Setting::first()->discount ?? 0;

        // Clean up abandoned sales records (older than 1 hour with no items)
        $this->cleanupAbandonedSales();

        // Check whether there are any transactions in progress
        if ($id_sales = session('id_sales')) {
            $sales = Sales::find($id_sales);
            if (!$sales) {
                session()->forget('id_sales');
                if (auth()->user()->level == 1) {
                    return redirect()->route('transaksi.baru');
                } else {
                    return redirect()->route('home');
                }
            }
            $memberSelected = $sales->member ?? new Member();

            return view('sales_detail.index', compact('product', 'member', 'discount', 'id_sales', 'sales', 'memberSelected'));
        } else {
            if (auth()->user()->level == 1) {
                return redirect()->route('transaksi.baru');
            } else {
                return redirect()->route('home');
            }
        }
    }

    /**
     * Clean up abandoned sales records
     */
    private function cleanupAbandonedSales()
    {
        // Find sales records older than 1 hour that have no items
        $abandonedSales = Sales::where('created_at', '<', now()->subHour())
            ->whereDoesntHave('details')
            ->get();

        foreach ($abandonedSales as $sales) {
            $sales->delete();
        }
    }

    public function data($id)
    {
        // Handle invalid or zero ID
        if (!$id || $id == 0) {
            return datatables()
                ->of([])
                ->addIndexColumn()
                ->rawColumns(['aksi', 'product_code', 'jumlah'])
                ->make(true);
        }

        $detail = SalesDetail::with('product')
            ->where('id_sales', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['product_code'] = '<span class="label label-success">'. $item->product['product_code'] .'</span';
            $row['name_product'] = $item->product['name_product'];
            $row['selling_price']  = '$ '. format_uang($item->selling_price);
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->id_sales_detail .'" value="'. $item->jumlah .'">';
            $row['discount']      = $item->discount . '%';
            $row['subtotal']    = '$ '. format_uang($item->subtotal);
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('transaksi.destroy', $item->id_sales_detail) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            $total += $item->selling_price * $item->jumlah - (($item->discount * $item->jumlah) / 100 * $item->selling_price);;
            $total_item += $item->jumlah;
        }
        $data[] = [
            'product_code' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'name_product' => '',
            'selling_price'  => '',
            'jumlah'      => '',
            'discount'      => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'product_code', 'jumlah'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $product = Product::where('id_product', $request->id_product)->first();
        if (! $product) {
            return response()->json('Data failed to save', 400);
        }

        // Use the existing sales record from session
        $id_sales = session('id_sales');
        if (!$id_sales) {
            return response()->json('No active transaction found', 400);
        }

        $detail = new SalesDetail();
        $detail->id_sales = $id_sales;
        $detail->id_product = $product->id_product;
        $detail->selling_price = $product->selling_price;
        $detail->jumlah = 1;
        $detail->discount = $product->discount;
        $detail->subtotal = $product->selling_price - ($product->discount / 100 * $product->selling_price);
        $detail->save();

        return response()->json('Data saved successfully', 200);
    }
    // visit "codeastro" for more projects!
    public function update(Request $request, $id)
    {
        $detail = SalesDetail::find($id);
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->selling_price * $request->jumlah - (($detail->discount * $request->jumlah) / 100 * $detail->selling_price);;
        $detail->update();
    }

    public function destroy($id)
    {
        $detail = SalesDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($discount = 0, $total = 0, $diterima = 0)
    {
        $pay   = $total - ($discount / 100 * $total);
        $kembali = ($diterima != 0) ? $diterima - $pay : 0;
        $data    = [
            'totalrp' => format_uang($total),
            'pay' => $pay,
            'payrp' => format_uang($pay),
            'terbilang' => ucwords(terbilang($pay). ' Dollar'),
            'kembalirp' => format_uang($kembali),
            'kembali_terbilang' => ucwords(terbilang($kembali). ' Dollar'),
        ];

        return response()->json($data);
    }
}
// visit "codeastro" for more projects!
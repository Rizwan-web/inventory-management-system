<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\SalesDetail;
use App\Models\Product;
use App\Models\Setting;
use App\Models\InvoiceSequence;
use App\Models\Member;
use Illuminate\Http\Request;
use PDF;

class SalesController extends Controller
{
    public function index()
    {
        return view('sales.index');
    }

    public function data()
    {
        // Return all sales (including those without a member) so DataTable shows every order
        $sales = Sales::with('member')
            ->orderBy('id_sales', 'desc')
            ->get();
        return datatables()
            ->of($sales)
            ->addIndexColumn()
            ->addColumn('total_item', function ($sales) {
                return format_uang($sales->total_item);
            })
            ->addColumn('total_price', function ($sales) {
                return '$ '. format_uang($sales->total_price);
            })
            ->addColumn('pay', function ($sales) {
                return '$ '. format_uang($sales->pay);
            })
            ->addColumn('tanggal', function ($sales) {
                return tanggal_indonesia($sales->created_at, false);
            })
            ->addColumn('member_code', function ($sales) {
                $member = $sales->member->member_code ?? '';
                return '<span class="label label-success">'. $member .'</span>';
            })
            ->addColumn('dc_number', function ($sales) {
                return $sales->dc_number ?? '-';
            })
            ->editColumn('discount', function ($sales) {
                return $sales->discount . '%';
            })
            ->editColumn('kasir', function ($sales) {
                return $sales->user->name ?? '';
            })
            ->addColumn('aksi', function ($sales) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('sales.show', $sales->id_sales) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('sales.destroy', $sales->id_sales) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'member_code'])
            ->make(true);
    }
    // visit "codeastro" for more projects!
    public function create()
    {
        // Create a temporary sales record for the new transaction
        $sales = new Sales();
        $sales->id_member = null;
        $sales->total_item = 0;
        $sales->total_price = 0;
        $sales->discount = 0;
        $sales->pay = 0;
        $sales->diterima = 0;
        $sales->id_user = auth()->id();
        $sales->save();

        session(['id_sales' => $sales->id_sales]);
        return redirect()->route('transaksi.index');
    }

    public function store(Request $request)
    {
        $sales = Sales::findOrFail($request->id_sales);
        $sales->id_member = $request->id_member;
        $sales->total_item = $request->total_item;
        $sales->total_price = $request->total;
        $sales->discount = $request->discount;
        $sales->pay = $request->pay;
        $sales->diterima = $request->diterima;
        $sales->dc_number = $request->dc_number;
        $sales->po_number = $request->po_number;
        $sales->update();
        
        $detail = SalesDetail::where('id_sales', $sales->id_sales)->get();
        foreach ($detail as $item) {
            $item->discount = $request->discount;
            $item->update();

            $product = Product::find($item->id_product);
            $product->stok -= $item->jumlah;
            $product->update();
        }

        return redirect()->route('transaksi.selesai');
    }

    public function show($id)
    {
        $detail = SalesDetail::with('product')->where('id_sales', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('product_code', function ($detail) {
                return '<span class="label label-success">'. $detail->product->product_code .'</span>';
            })
            ->addColumn('name_product', function ($detail) {
                return $detail->product->name_product;
            })
            ->addColumn('selling_price', function ($detail) {
                return '$ '. format_uang($detail->selling_price);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return '$ '. format_uang($detail->subtotal);
            })
            ->rawColumns(['product_code'])
            ->make(true);
    }
    // visit "codeastro" for more projects!
    public function destroy($id)
    {
        $sales = Sales::find($id);
        $detail    = SalesDetail::where('id_sales', $sales->id_sales)->get();
        foreach ($detail as $item) {
            $product = Product::find($item->id_product);
            if ($product) {
                $product->stok += $item->jumlah;
                $product->update();
            }

            $item->delete();
        }

        $sales->delete();

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();

        return view('sales.selesai', compact('setting'));
    }

    public function cancel()
    {
        $id_sales = session('id_sales');
        
        if ($id_sales) {
            $sales = Sales::find($id_sales);
            if ($sales) {
                // Delete all sales details first
                SalesDetail::where('id_sales', $id_sales)->delete();
                // Delete the sales record
                $sales->delete();
            }
        }
        
        // Clear the session
        session()->forget('id_sales');
        
        return redirect()->route('dashboard')->with('success', 'Transaction cancelled successfully.');
    }

    public function notaKecil()
    {
        $setting = Setting::first();
        $sales = Sales::find(session('id_sales'));
        if (! $sales) {
            abort(404);
        }
        $detail = SalesDetail::with('product')
            ->where('id_sales', session('id_sales'))
            ->get();
        
        // Get the invoice number from the first detail record
        $invoiceNumber = $detail->first()->invoice_number ?? 0;
        
        return view('sales.nota_kecil', compact('setting', 'sales', 'detail', 'invoiceNumber'));
    }

    public function notaBesar()
    {
        $setting = Setting::first();
        $sales = Sales::find(session('id_sales'));
        if (! $sales) {
            abort(404);
        }
        $detail = SalesDetail::with('product')
            ->where('id_sales', session('id_sales'))
            ->get();
        
        // Get the invoice number from the first detail record
        $invoiceNumber = $detail->first()->invoice_number ?? 0;
        
        $pdf = Pdf::loadView('sales.nota_besar', [
            'setting' => $setting,
            'sales' => $sales,
            'detail' => $detail,
            'invoiceNumber' => $invoiceNumber,
        ])->setPaper('A4', 'portrait');
        return $pdf->stream('Transaction-'. date('Y-m-d-his') .'.pdf');
    }

    public function getDcNumbers()
    {
        $sales = Sales::whereNotNull('dc_number')
            ->where('dc_number', '!=', '')
            ->where('dc_number', '!=', 'null')
            ->select('dc_number')
            ->distinct()
            ->get();
        // Return as array of strings, not objects
        $dcNumbers = $sales->pluck('dc_number')->toArray();
        return response()->json($dcNumbers);
    }

    /**
     * Return a simple list of members (id and name) for modal select
     */
    public function getMembers()
    {
        $members = Member::orderBy('name')->get(['id_member', 'name']);
        return response()->json($members);
    }

    /**
     * Return sales for a specific member (used by modal when member selected)
     */
    public function getSalesByMember(Request $request)
    {
        $memberId = $request->query('member');
        if (! $memberId) {
            return response()->json([], 200);
        }

        // Load sales together with their details and product information
        $sales = Sales::with(['details.product'])
            ->where('id_member', $memberId)
            ->orderBy('id_sales', 'desc')
            ->get();

        // Transform to a lightweight payload for the modal
        $payload = $sales->map(function ($sale) {
            return [
                'id_sales' => $sale->id_sales,
                'dc_number' => $sale->dc_number,
                'total_item' => $sale->total_item,
                'total_price' => $sale->total_price,
                'created_at' => $sale->created_at->toDateTimeString(),
                'details' => $sale->details->map(function ($d) {
                    return [
                        'id' => $d->id,
                        'id_product' => $d->id_product,
                        'product_code' => $d->product->product_code ?? '',
                        'name_product' => $d->product->name_product ?? '',
                        'jumlah' => $d->jumlah,
                        'selling_price' => $d->selling_price,
                        'subtotal' => $d->subtotal,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json($payload);
    }

    // Return sales grouped by DC number (for modal listing)
    public function getSalesByDc(Request $request)
    {
        $dc = $request->query('dc');
        if (! $dc) {
            return response()->json([], 200);
        }

        $sales = Sales::where('dc_number', $dc)
            ->orderBy('id_sales', 'desc')
            ->get(['id_sales', 'dc_number', 'total_item', 'total_price', 'created_at']);

        return response()->json($sales);
    }

    /**
     * Return all sales (simple payload) to allow loading orders into the Generate Invoice modal
     */
    public function getAllSales(Request $request)
    {
        $sales = Sales::orderBy('id_sales', 'desc')
            ->get(['id_sales', 'dc_number', 'total_item', 'total_price', 'created_at']);

        return response()->json($sales);
    }

    public function generateInvoice(Request $request)
    {
        // Accept either dc_numbers[] (legacy) or sales_ids[] (new)
        $salesIds = $request->input('sales_ids', []);
        $dcNumbers = $request->input('dc_numbers', []);

        if (empty($salesIds) && empty($dcNumbers)) {
            // Preserve legacy message when caller provided dc_numbers[] (tests expect this)
            if ($request->has('dc_numbers')) {
                return redirect()->back()->with('error', 'Please select at least one DC number.');
            }

            return redirect()->back()->with('error', 'Please select at least one order or DC number.');
        }

        // Determine sales to update
        if (! empty($salesIds)) {
            $sales = Sales::whereIn('id_sales', $salesIds)->get();
        } else {
            $sales = Sales::whereIn('dc_number', $dcNumbers)->get();
        }

        if ($sales->isEmpty()) {
            // Use a different message when caller used dc_numbers[] (tests expect this)
            if ($request->has('dc_numbers')) {
                return redirect()->back()->with('error', 'No orders found with the specified DC numbers.');
            }

            return redirect()->back()->with('error', 'No orders found for the selected items.');
        }

        // Get the next invoice number from the sequence
        $nextInvoiceNumber = InvoiceSequence::getNextNumber();

        // Update all sales details with the invoice number (only those without invoice numbers)
        foreach ($sales as $sale) {
            SalesDetail::where('id_sales', $sale->id_sales)
                ->where('invoice_number', 0)
                ->update(['invoice_number' => $nextInvoiceNumber]);
        }

        // Store the invoice number, sales IDs, and optional fare
        $involvedSalesIds = $sales->pluck('id_sales')->values()->toArray();
        $involvedDc = $sales->pluck('dc_number')->unique()->values()->toArray();
        $fare = (float) $request->input('fare_amount', 0);
        session(['generated_invoice_number' => $nextInvoiceNumber]);
        session(['generated_sales_ids' => $involvedSalesIds]);
        session(['generated_dc_numbers' => $involvedDc]);
        session(['generated_fare' => $fare]);

        return redirect()->back()->with('success', 'Invoice generated successfully with number: INV-' . str_pad($nextInvoiceNumber, 4, '0', STR_PAD_LEFT));
    }

    public function downloadGeneratedInvoice()
    {
        $invoiceNumber = session('generated_invoice_number');
        $salesIds = session('generated_sales_ids');
        $dcNumbers = session('generated_dc_numbers');
        $fare = (float) session('generated_fare', 0);

        if (!$invoiceNumber || (empty($salesIds) && empty($dcNumbers))) {
            abort(404);
        }

        $setting = Setting::first();

        // Prefer lookup by sales IDs (works even when dc_number is null)
        if (!empty($salesIds)) {
            $sales = Sales::whereIn('id_sales', $salesIds)->get();
        } else {
            $sales = Sales::whereIn('dc_number', array_filter($dcNumbers))->get();
        }

        if ($sales->isEmpty()) {
            abort(404);
        }

        // Get all details for these sales
        $salesIds = $sales->pluck('id_sales');
        $detail = SalesDetail::with('product')
            ->whereIn('id_sales', $salesIds)
            ->get();

        $pdf = Pdf::loadView('sales.nota_besar', [
            'setting' => $setting,
            'sales' => $sales,
            'detail' => $detail,
            'invoiceNumber' => $invoiceNumber,
            'fare' => $fare,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('Invoice-'. str_pad($invoiceNumber, 4, '0', STR_PAD_LEFT) .'.pdf');
    }
}
// visit "codeastro" for more projects!
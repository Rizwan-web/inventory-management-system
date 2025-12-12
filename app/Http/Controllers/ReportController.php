<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Expense;
use App\Models\sales;
use Illuminate\Http\Request;
use PDF;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('report.index', compact('tanggalAwal', 'tanggalAkhir'));
    }

    public function getData($awal, $akhir)
    {
        $no = 1;
        $data = array();
        $pendapatan = 0;
        $total_pendapatan = 0;

        while (strtotime($awal) <= strtotime($akhir)) {
            $tanggal = $awal;
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            $total_sales = Sales::where('created_at', 'LIKE', "%$tanggal%")->sum('pay');
            $total_purchase = Purchase::where('created_at', 'LIKE', "%$tanggal%")->sum('pay');
            $total_expense = Expense::where('created_at', 'LIKE', "%$tanggal%")->sum('nominal');

            $pendapatan = $total_sales - $total_purchase - $total_expense;
            $total_pendapatan += $pendapatan;

            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['tanggal'] = tanggal_indonesia($tanggal, false);
            $row['sales'] = format_uang($total_sales);
            $row['purchase'] = format_uang($total_purchase);
            $row['expense'] = format_uang($total_expense);
            $row['pendapatan'] = format_uang($pendapatan);

            $data[] = $row;
        }
        // visit "codeastro" for more projects!
        $data[] = [
            'DT_RowIndex' => '',
            'tanggal' => '',
            'sales' => '',
            'purchase' => '',
            'expense' => 'Total Income',
            'pendapatan' => format_uang($total_pendapatan),
        ];

        return $data;
    }

    public function data($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);

        return datatables()
            ->of($data)
            ->make(true);
    }

    public function exportPDF($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);
        $pdf  = PDF::loadView('report.pdf', compact('awal', 'akhir', 'data'));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream('Report-pendapatan-'. date('Y-m-d-his') .'.pdf');
    }
}

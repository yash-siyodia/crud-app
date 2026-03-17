<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Services\ActivityLogService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class InvoiceController extends Controller
{
    /**
     * Display invoice list
     */
    public function index()
    {
        return view('invoices.index');
    }

    /**
     * Show create invoice form
     */
    public function create()
    {
        return view('invoices.create');
    }

    /**
     * Store new invoice
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'invoice_date'  => 'required',
            'product_name.*'=> 'required',
            'qty.*'         => 'required|numeric|min:1',
            'price.*'       => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }


        DB::beginTransaction();

        try {
            //dd($request->all());
            // 1️⃣ Create Invoice Master
            $invoice = Invoice::create([
                'invoice_number'    => $this->generateInvoiceNumber(),
                'customer_name' => $request->customer_name,
                'invoice_date'  => $request->invoice_date,
                'subtotal'     => $request->sub_total,
                'tax'           => $request->tax,
                'discount'      => $request->discount,
                'grand_total'   => $request->grand_total,
            ]);

            // 2️⃣ Create Invoice Items
            foreach ($request->product_name as $key => $value) {
                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'product_name'=> $value,
                    'description' => $request->description[$key],
                    'quantity'    => $request->qty[$key],
                    'price'       => $request->price[$key],
                    'total'       => $request->total[$key],
                ]);
            }

            DB::commit();

            // Log invoice creation
            ActivityLogService::logCrud('created', 'Invoice', $invoice, [
                'items_count' => count($request->product_name),
                'grand_total' => $request->grand_total
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Invoice saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                //'message' => 'Something went wrong'
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * Show invoice details
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('items');
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show edit invoice form
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update invoice
     */
    public function update(Request $request, Invoice $invoice)
    {
        DB::beginTransaction();

        try {

            // Update invoice master
            $invoice->update([
                'customer_name' => $request->customer_name,
                'invoice_date'  => $request->invoice_date,
                'sub_total'     => $request->sub_total,
                'tax'           => $request->tax,
                'discount'      => $request->discount,
                'grand_total'   => $request->grand_total,
            ]);

            // Remove old items
            $invoice->items()->delete();

            // Insert updated items
            foreach ($request->product_name as $key => $value) {
                InvoiceItem::create([
                    'invoice_id'   => $invoice->id,
                    'product_name' => $value,
                    'description'  => $request->description[$key],
                    'quantity'          => $request->qty[$key],
                    'price'        => $request->price[$key],
                    'total'        => $request->total[$key],
                ]);
            }

            DB::commit();

            // Log invoice update
            ActivityLogService::logCrud('updated', 'Invoice', $invoice, [
                'items_count' => count($request->product_name),
                'grand_total' => $request->grand_total
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Invoice updated successfully'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft delete invoice
     */
    public function destroy(Invoice $invoice)
    {
        // logic will be added later
    }

    private function generateInvoiceNumber()
    {
        $year = date('Y');

        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'INV-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function datatable(Request $request)
    {
        $invoices = Invoice::select([
            'id',
            'invoice_number',
            'customer_name',
            'grand_total',
            'invoice_date',
            'created_at'
        ]);

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                    <a href="'.route('invoices.show', $row->id).'" class="btn btn-sm btn-info">View</a>
                    <a href="'.route('invoices.edit', $row->id).'" class="btn btn-sm btn-warning">Edit</a>
                    <a href="'.route('invoices.pdf', $row->id).'" class="btn btn-sm btn-success">PDF</a>
                ';
            })
            ->editColumn('invoice_date', function ($row) {
                return date('d-m-Y', strtotime($row->invoice_date));
            })
            ->rawColumns(['action']) 
            ->make(true);
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('items');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('Invoice-'.$invoice->invoice_number.'.pdf');
    }

}

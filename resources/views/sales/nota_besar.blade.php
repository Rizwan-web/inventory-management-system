<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - Digital Computers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 15px;
            line-height: 1.2;
            position: relative;
            min-height: 100vh;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            min-height: calc(100vh - 30px);
            padding-bottom: 180px; /* Space for warranty + signature */
        }

        /* Header Section */
        .header {
            border: 2px solid #000;
            margin-bottom: 10px;
        }

        .header-top {
            background-color: #000;
            color: white;
            padding: 8px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 5px;
        }

        .logo-section {
            background-color: white;
            color: black;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            width: 60px;
        }

        .company-info {
            text-align: center;
            width: 60%;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-tagline {
            font-size: 10px;
            color: white;
        }

        .payment-options {
            text-align: right;
            font-size: 10px;
            width: 80px;
        }

        /* Address and Invoice Details */
        .header-details {
            padding: 8px;
            background-color: white;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            vertical-align: top;
            padding: 3px;
        }

        .address-section {
            text-align: center;
            font-size: 10px;
            line-height: 1.3;
        }

        .invoice-details {
            text-align: left;
            font-size: 10px;
        }

        .invoice-details div {
            margin-bottom: 2px;
        }

        /* Customer Section */
        .customer-section {
            margin-bottom: 10px;
            font-size: 16px;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }

        .data-table th {
            background-color: #000;
            color: white;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #000;
            font-weight: bold;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Total Section */
        .total-section {
            width: 100%;
            margin-bottom: 10px;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .total-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .total-row {
            background-color: #000;
            color: white;
            font-weight: bold;
        }

        .total-spacer {
            width: 75%;
        }

        .total-label {
            width: 15%;
            text-align: right;
        }

        .total-amount {
            width: 10%;
            text-align: right;
        }

        /* Footer */
        .footer-section {
            font-size: 10px;
            margin-bottom: 15px;
            clear: both;
        }

        /* Bottom section container */
        .bottom-section {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
        }

        /* Warranty Section - Positioned directly above signature */
        .warranty-section {
            background-color: #000;
            color: white;
            padding: 12px;
            font-size: 9px;
            margin-bottom: 0;
        }

        .warranty-section strong {
            display: block;
            margin-bottom: 5px;
        }

        .warranty-section ul {
            margin: 5px 0;
            padding-left: 15px;
        }

        .warranty-section li {
            margin-bottom: 2px;
        }

        /* Signatures - Fixed at bottom */
        .signature-section {
            padding: 20px 0;
            background-color: white;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            padding: 10px 0;
            vertical-align: bottom;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 200px;
            height: 15px;
            margin-left: 10px;
        }
        .payment-options #cash, .payment-options #credit{background: #fff;}
        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                padding: 15mm;
                min-height: 297mm;
            }
            
            .invoice-container {
                max-width: none;
                width: 100%;
                min-height: 267mm; /* A4 height minus margins */
                padding-bottom: 140px; /* Space for warranty + signature */
            }
            
            .bottom-section {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
            }
            
            .warranty-section {
                padding: 12px;
                margin-bottom: 0;
            }
            
            .signature-section {
                padding: 15px 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
                <div class="header-top">
                    <table class="header-table">
                        <tr>
                            <td style="width: 15%;">
                                <div class="logo-section">DC</div>
                            </td>
                            <td class="company-info">
                                <div class="company-name">DIGITAL COMPUTERS</div>
                                <div class="company-tagline">Computer, Laptops, Tablets & Accessories</div>
                            </td>
                            <td class="payment-options">
                                <table cellspacing="0" cellpadding="0" style="line-height: 1.4;">
                                    <tr>
                                        <td style="padding-right: 5px; vertical-align: middle;">
                                            <input type="checkbox" id="cash">
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <label for="cash">Cash</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-right: 5px; vertical-align: middle;">
                                            <input type="checkbox" id="credit">
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <label for="credit">Credit</label>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="header-details">
                    <table class="details-table">
                        <tr>
                            <td style="width: 15%;"></td>
                            <td class="address-section">
                                LG-56 AL Latif Centre 88/D-1 Main Boulevard Gulberg III Lahore.<br>
                                E-mail: digicomp5@hotmail.com | Ph: 0423-5781255
                            </td>
                            <td class="invoice-details" style="width: 25%;">
                                <div>Invoice #: <strong>INV-{{ str_pad($invoiceNumber, 4, '0', STR_PAD_LEFT) }}</strong></div>
                                @php
                                    $isCollection = isset($sales) && (is_array($sales) || $sales instanceof \Illuminate\Support\Collection);
                                @endphp

                                @if(!empty($isCollection) && $isCollection)
                                    @php
                                        $poList = collect($sales)->pluck('po_number')->filter()->unique()->values()->all();
                                        $dcList = collect($sales)->pluck('dc_number')->filter()->unique()->values()->all();
                                    @endphp
                                    <div>PO Number: {{ count($poList) ? implode(', ', $poList) : '' }}</div>
                                    <div>DC Number: {{ count($dcList) ? implode(', ', $dcList) : 'DC-001' }}</div>
                                @else
                                    <div>PO Number: {{ $sales->po_number ?? '' }}</div>
                                    <div>DC Number: {{ $sales->dc_number ?? 'DC-001' }}</div>
                                @endif

                                <div>Date: {{ date('F d, Y') }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="customer-section">
                    @if(!empty($isCollection) && $isCollection)
                        @php
                            $memberIds = collect($sales)->pluck('id_member')->filter()->unique()->values();
                        @endphp
                        @if($memberIds->count() === 1)
                            @php
                                $memberName = collect($sales)->first()->member->name ?? 'Walk-in Customer';
                            @endphp
                            M/S: {{ $memberName }}
                        @else
                            M/S: Multiple Customers
                        @endif
                    @else
                        M/S: {{ $sales->member->name ?? 'Walk-in Customer' }}
                    @endif
            </div>

            <!-- Items Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">Sr. #</th>
                        <th style="width: 40%;">Description</th>
                        <th style="width: 8%;">Qty.</th>
                        <th style="width: 15%;">Rate</th>
                        <th style="width: 15%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detail as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->product->name_product }}</td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td class="text-right">{{ format_uang($item->selling_price) }}</td>
                        <td class="text-right">{{ format_uang($item->subtotal) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Total Section -->
            <div class="total-section">
                <table class="total-table">
                    @php
                        $grandTotal = (!empty($isCollection) && $isCollection) ? collect($sales)->sum('total_price') : ($sales->total_price ?? 0);
                    @endphp
                    <tr>
                        <td class="total-spacer" style="border: none;"></td>
                        <td class="total-row total-label"><strong>Grand Total</strong></td>
                        <td class="total-row total-amount"><strong>{{ format_uang($grandTotal) }}</strong></td>
                    </tr>
                </table>
            </div>

            <!-- Footer -->
            

        <!-- Bottom Section - Contains warranty and signature together -->
        <div class="bottom-section">
            <div class="footer-section">
                Received above mentioned items in good condition.
            </div>
            <!-- Warranty Section - Directly above signature -->
            <div class="warranty-section">
                <strong>30 Days' Warranty</strong>
                Against any defect Provided:
                <ul>
                    <li>The warranty seals / stickers are not tempered</li>
                    <li>The Fault / Damage is not due to mishandling</li>
                    <li>The Fault in total or in part is not due to surge in Power Supply</li>
                </ul>
            </div>

            <!-- Signatures - Fixed at bottom of page -->
            <div class="signature-section">
                <table class="signature-table">
                    <tr>
                        <td style="width: 50%;">
                            Customers Signature: <span class="signature-line"></span>
                        </td>
                        <td style="width: 50%; text-align: right;">
                            Signature: <span class="signature-line"></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
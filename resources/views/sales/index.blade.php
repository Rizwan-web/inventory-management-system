@extends('layouts.master')

@section('title')
    Sales List
@endsection

<!-- @section('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection -->
@section('breadcrumb')
    @parent
    <li class="active">Sales List</li>
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sales Data</h3>
                    <div class="card-tools">
                        <a href="{{ route('transaksi.baru') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Order
                        </a>
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#generateInvoiceModal">
                            <i class="fas fa-file-invoice"></i> Generate Invoice
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="sales-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Member</th>
                                <th>DC Number</th>
                                <th>Total Item</th>
                                <th>Total Price</th>
                                <th>Discount</th>
                                <th>Pay</th>
                                <th>Cashier</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Generate Invoice Modal -->
    <div class="modal fade" id="generateInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="generateInvoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="generate-invoice-form" action="{{ route('transaksi.generate-invoice') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="generateInvoiceModalLabel">Generate Invoice</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label><strong>Select DC Numbers or Orders:</strong></label>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Select one or more DC numbers or individual orders to generate a single invoice for all selected items.
                            </div>

                            <ul class="nav nav-tabs" id="invoiceTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" id="dc-tab" data-toggle="tab" href="#dc-tab-panel" role="tab">By DC Number</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all-tab-panel" role="tab">All Orders</a>
                                </li>
                            </ul>

                            <div class="tab-content border p-3" style="max-height: 350px; overflow-y: auto;">
                                <div class="tab-pane fade" id="dc-tab-panel" role="tabpanel">
                                    <input type="text" id="dc-search" class="form-control mb-2" placeholder="Search DC number..." autocomplete="off">
                                    <div id="dc-numbers-container" class="p-1">
                                        <div class="text-center" id="dc-loading">
                                            <i class="fas fa-spinner fa-spin"></i> Loading DC numbers...
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="all-tab-panel" role="tabpanel">
                                    <div class="mb-2 row">
                                        <div class="col-md-6 mb-2">
                                            <select id="member-select" class="form-control">
                                                <option value="">-- Select Member (optional) --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-2 text-right">
                                            <button type="button" id="load-all-sales" class="btn btn-sm btn-primary">Load All Orders</button>
                                        </div>
                                    </div>
                                    <div id="all-sales-container" class="p-1 text-muted">No orders loaded.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="generateInvoiceBtn" disabled>
                            <i class="fas fa-file-invoice"></i> Generate Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@includeIf('penjualan.detail')
@endsection

@push('scripts')
    <!-- <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script> -->

    <script>
        $(function () {
            var table = $('#sales-table').DataTable({
                processing: true, 
                serverSide: true,
                ajax: '{{ route("sales.data") }}',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'tanggal', name: 'tanggal'},
                    {data: 'member_code', name: 'member_code'},
                    {data: 'dc_number', name: 'dc_number'},
                    {data: 'total_item', name: 'total_item'},
                    {data: 'total_price', name: 'total_price'},
                    {data: 'discount', name: 'discount'},
                    {data: 'pay', name: 'pay'},
                    {data: 'kasir', name: 'kasir'},
                    {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
                ]
            });

            // Load DC numbers for invoice generation and allow listing orders per DC
            var dcNumbersList = [];
            var dcNumbersLoaded = false;
            $('#generateInvoiceModal').on('show.bs.modal', function () {
                $('#dc-search').val("");
                $('#dc-numbers-container').html('<div class="text-center" id="dc-loading"><i class="fas fa-spinner fa-spin"></i> Loading DC numbers...</div>');
                // Fetch DC numbers
                $.get('{{ route("transaksi.dc-numbers") }}', function(data) {
                    if (Array.isArray(data) && typeof data[0] === 'object' && data[0] !== null) {
                        dcNumbersList = data.map(function(item) { return item.dc_number || item; });
                    } else {
                        dcNumbersList = data;
                    }
                    dcNumbersLoaded = true;
                    renderDcNumbers(dcNumbersList);
                }).fail(function() {
                    $('#dc-numbers-container').html(`<div class="text-center text-danger"><i class="fas fa-exclamation-circle"></i><p>Error loading DC numbers.</p><small>Please try again.</small></div>`);
                });

                // Fetch members for All Orders member select
                var memberSelect = $('#member-select');
                memberSelect.html('<option value="">-- Loading members... --</option>');
                $.get('{{ route("transaksi.members") }}', function(members) {
                    memberSelect.empty().append('<option value="">-- Select Member (optional) --</option>');
                    if (Array.isArray(members) && members.length) {
                        members.forEach(function(m) {
                            memberSelect.append('<option value="'+ m.id_member +'">'+ m.name +'</option>');
                        });
                    }
                }).fail(function() {
                    memberSelect.html('<option value="">-- Error loading members --</option>');
                });

                // Focus the member select so user can choose a member first
                $('#member-select').focus();
            });

            $(document).on('input', '#dc-search', function() {
                if (!dcNumbersLoaded) return;
                var search = $(this).val().toLowerCase();
                var filtered = dcNumbersList.filter(function(dc) {
                    return dc.toLowerCase().includes(search);
                });
                renderDcNumbers(filtered);
            });

            function renderDcNumbers(list) {
                var container = $('#dc-numbers-container');
                container.empty();
                if (list.length === 0) {
                    container.html(`<div class="text-center text-muted"><i class="fas fa-exclamation-triangle"></i><p>No DC numbers available for invoice generation.</p><small>Create some orders with DC numbers first.</small></div>`);
                    return;
                }
                // For each DC show header and a placeholder for orders
                list.forEach(function(dcNumber) {
                    var safeId = 'dc_' + dcNumber.replace(/[^a-zA-Z0-9]/g, '_');
                    var card = $(`
                        <div class="card mb-2">
                            <div class="card-header p-2" style="cursor:pointer;" data-dc="${dcNumber}" data-toggle="collapse" data-target="#${safeId}_body">
                                <strong>${dcNumber}</strong>
                                <button type="button" class="btn btn-xs btn-primary pull-right load-dc-sales" data-dc="${dcNumber}">Load Orders</button>
                            </div>
                            <div id="${safeId}_body" class="card-body collapse">
                                <div class="dc-sales-list text-muted">No orders loaded.</div>
                            </div>
                        </div>
                    `);
                    container.append(card);
                });
            }

            // When Load Orders clicked, fetch sales for that DC
            $(document).on('click', '.load-dc-sales', function(e) {
                e.preventDefault();
                var dc = $(this).data('dc');
                var btn = $(this);
                var cardBody = btn.closest('.card').find('.dc-sales-list');
                cardBody.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading orders...</div>');
                $.get('{{ route("transaksi.dc-sales") }}', { dc: dc }, function(data) {
                    if (!Array.isArray(data) || data.length === 0) {
                        cardBody.html('<div class="text-muted">No orders for this DC.</div>');
                        return;
                    }
                    var html = '<div class="list-group">';
                    data.forEach(function(sale) {
                        html += `
                            <label class="list-group-item">
                                <input type="checkbox" name="sales_ids[]" value="${sale.id_sales}" class="sale-checkbox"> 
                                <strong>#${sale.id_sales}</strong> — ${sale.total_item} items — $ ${sale.total_price} — ${sale.created_at}
                            </label>
                        `;
                    });
                    html += '</div>';
                    cardBody.html(html);
                }).fail(function() {
                    cardBody.html('<div class="text-danger">Error loading orders.</div>');
                });
            });

            // Load all sales into the All Orders tab
            $(document).on('click', '#load-all-sales', function(e) {
                e.preventDefault();
                var container = $('#all-sales-container');
                container.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading orders...</div>');
                $.get('{{ route("transaksi.all-sales") }}', function(data) {
                    if (!Array.isArray(data) || data.length === 0) {
                        container.html('<div class="text-muted">No orders available.</div>');
                        return;
                    }
                    var html = '<div class="list-group">';
                    data.forEach(function(sale) {
                        html += `
                            <label class="list-group-item">
                                <input type="checkbox" name="sales_ids[]" value="${sale.id_sales}" class="sale-checkbox"> 
                                <strong>#${sale.id_sales}</strong> — ${sale.total_item} items — $ ${sale.total_price} — ${sale.created_at}
                            </label>
                        `;
                    });
                    html += '</div>';
                    container.html(html);
                }).fail(function() {
                    container.html('<div class="text-danger">Error loading orders.</div>');
                });
            });

            // When a member is selected, load orders for that member
            $(document).on('change', '#member-select', function() {
                var memberId = $(this).val();
                var container = $('#all-sales-container');
                if (!memberId) {
                    container.html('<div class="text-muted">No orders loaded.</div>');
                    return;
                }
                container.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading orders for member...</div>');
                $.get('{{ route("transaksi.member-sales") }}', { member: memberId }, function(data) {
                    if (!Array.isArray(data) || data.length === 0) {
                        container.html('<div class="text-muted">No orders for this member.</div>');
                        return;
                    }

                    var html = '';
                    data.forEach(function(sale) {
                        var saleItems = '';
                        if (Array.isArray(sale.details) && sale.details.length) {
                            saleItems += '<table class="table table-sm table-striped mb-2"><thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead><tbody>';
                            sale.details.forEach(function(d) {
                                saleItems += '<tr>' +
                                    '<td>' + (d.name_product || d.product_code || 'N/A') + '</td>' +
                                    '<td>' + d.jumlah + '</td>' +
                                    '<td>$ ' + parseFloat(d.selling_price).toFixed(2) + '</td>' +
                                    '<td>$ ' + parseFloat(d.subtotal).toFixed(2) + '</td>' +
                                    '</tr>';
                            });
                            saleItems += '</tbody></table>';
                        }

                        html += `
                            <div class="card mb-2">
                                <div class="card-body p-2">
                                    <label class="mb-1 d-block">
                                        <input type="checkbox" name="sales_ids[]" value="${sale.id_sales}" class="sale-checkbox"> 
                                        <strong>#${sale.id_sales}</strong> — ${sale.total_item} items — $ ${parseFloat(sale.total_price).toFixed(2)} — ${sale.created_at}
                                    </label>
                                    <div class="sale-items">${saleItems}</div>
                                </div>
                            </div>
                        `;
                    });

                    container.html(html);
                }).fail(function() {
                    container.html('<div class="text-danger">Error loading orders.</div>');
                });
            });

            // Enable generate button when any sale checkbox checked
            $(document).on('change', '.sale-checkbox', function() {
                var checked = $('.sale-checkbox:checked').length;
                $('#generateInvoiceBtn').prop('disabled', checked === 0);
            });

            // Handle invoice generation success
            @if(session('success'))
                alert('{{ session("success") }}');
                @if(session('generated_invoice_number'))
                    if (confirm('Do you want to download the generated invoice?')) {
                        window.open('{{ route("transaksi.download-invoice") }}', '_blank');
                    }
                @endif
            @endif

            @if(session('error'))
                alert('{{ session("error") }}');
            @endif
        });

        function showDetail(url) {
            $('#modal-detail').modal('show');
            $('#modal-detail .modal-title').text('Sales Detail');
            $('#modal-detail .modal-body').load(url);
        }

        function deleteData(url) {
            if (confirm('Are you sure you want to delete this data?')) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    $('#sales-table').DataTable().ajax.reload();
                })
                .fail((errors) => {
                    alert('Error deleting data');
                    return;
                });
            }
        }
    </script>
@endpush
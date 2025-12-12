@extends('layouts.master')

@section('title')
Sales Transactions
@endsection

@push('css')
<style>
    .tampil-pay {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .tampil-terbilang {
        padding: 10px;
        background: #f0f0f0;
    }

    .table-sales tbody tr:last-child {
        display: none;
    }

    @media(max-width: 768px) {
        .tampil-pay {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Sales Transactions</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                    
                <form class="form-product">
                    @csrf
                    <div class="form-group row">
                        <label for="product_code" class="col-lg-2">Product Code</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="id_sales" id="id_sales" value="{{ $id_sales ?? '' }}">
                                <input type="hidden" name="id_product" id="id_product">
                                <input type="text" class="form-control" name="product_code" id="product_code">
                                <span class="input-group-btn">
                                    <button onclick="tampilProduct()" class="btn btn-success btn-flat" type="button"><i class="fa fa-search-plus"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-sales">
                    <thead>
                        <th width="5%">#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th width="15%">Quantity</th>
                        <th>Discount</th>
                        <th>Subtotal</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tampil-pay bg-primary"></div>
                        <div class="tampil-terbilang"></div>
                    </div>
                    <div class="col-lg-4">
                        <form action="{{ route('transaksi.simpan') }}" class="form-sales" method="post">
                            @csrf
                            <input type="hidden" name="id_sales" value="{{ $id_sales ?? '' }}">
                            <input type="hidden" name="total" id="total">
                            <input type="hidden" name="total_item" id="total_item">
                            <input type="hidden" name="pay" id="pay">
                            <input type="hidden" name="id_member" id="id_member" value="{{ $memberSelected->id_member }}">

                            <div class="form-group row">
                                <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                <div class="col-lg-8">
                                    <input type="text" id="totalrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="member_code" class="col-lg-2 control-label">Member</label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="member_code" value="{{ $memberSelected->member_code }}">
                                        <span class="input-group-btn">
                                            <button onclick="tampilMember()" class="btn btn-success btn-flat" type="button"><i class="fa fa-search-plus"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="po_number" class="col-lg-2 control-label">PO Number</label>
                                <div class="col-lg-8">
                                    <input type="text" id="po_number" class="form-control" name="po_number" value="{{ $sales->dc_number ?? '' }}" placeholder="Enter DC Number">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="dc_number" class="col-lg-2 control-label">DC Number</label>
                                <div class="col-lg-8">
                                    <input type="text" id="dc_number" class="form-control" name="dc_number" value="{{ $sales->dc_number ?? '' }}" placeholder="Enter DC Number">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="discount" class="col-lg-2 control-label">Discount</label>
                                <div class="col-lg-8">
                                    <input type="number" name="discount" id="discount" class="form-control" 
                                        value="{{ ! empty($memberSelected->id_member) ? $discount : 0 }}" 
                                        readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="pay" class="col-lg-2 control-label">Pay</label>
                                <div class="col-lg-8">
                                    <input type="text" id="payrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diterima" class="col-lg-2 control-label">Received</label>
                                <div class="col-lg-8">
                                    <input type="number" id="diterima" class="form-control" name="diterima" value="{{ $sales->diterima ?? 0 }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kembali" class="col-lg-2 control-label">Return</label>
                                <div class="col-lg-8">
                                    <input type="text" id="kembali" name="kembali" class="form-control" value="0" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" id="saveorder" class="btn btn-success btn-sm btn-flat pull-right btn-simpan"><i class="fa fa-floppy-o"></i> Save Transaction</button>
            </div>
        </div>
    </div>
</div>

@includeIf('sales_detail.product')
@includeIf('sales_detail.member')
@endsection

@push('scripts')
<script>
    let table, table2;

    $(function () {
        $('body').addClass('sidebar-collapse');

        table = $('.table-sales').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('transaksi.data', $id_sales ?? 0) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'product_code'},
                {data: 'name_product'},
                {data: 'selling_price'},
                {data: 'jumlah'},
                {data: 'discount'},
                {data: 'subtotal'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            dom: 'Brt',
            bSort: false,
            paginate: false
        })
        .on('draw.dt', function () {
            loadForm($('#discount').val());
            setTimeout(() => {
                $('#diterima').trigger('input');
            }, 300);
        });
        table2 = $('.table-product').DataTable();

        $(document).on('input', '.quantity', function () {
            let id = $(this).data('id');
            let jumlah = parseInt($(this).val());

            if (jumlah < 1) {
                $(this).val(1);
                alert('The number cannot be less than 1');
                return;
            }
            if (jumlah > 10000) {
                $(this).val(10000);
                alert('The number cannot exceed 10000');
                return;
            }

            $.post(`{{ url('/transaksi') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'jumlah': jumlah
                })
                .done(response => {
                    $(this).on('mouseout', function () {
                        table.ajax.reload(() => loadForm($('#discount').val()));
                    });
                })
                .fail(errors => {
                    alert('Unable to save data');
                    return;
                });
        });

        $(document).on('input', '#discount', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($(this).val());
        });

        $('#diterima').on('input', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($('#discount').val(), $(this).val());
        }).focus(function () {
            $(this).select();
        });

        $('.btn-simpan').on('click', function () {
            $('.form-sales').submit();
        });
    });

    function tampilProduct() {
        $('#modal-product').modal('show');
    }

    function hideProduct() {
        $('#modal-product').modal('hide');
    }

    function pilihProduct(id, kode) {
        $('#id_product').val(id);
        $('#product_code').val(kode);
        hideProduct();
        tambahProduct();
    }

    function tambahProduct() {
        $.post('{{ route('transaksi.store') }}', $('.form-product').serialize())
            .done(response => {
                $('#product_code').focus();
                table.ajax.reload(() => loadForm($('#discount').val()));
            })
            .fail(errors => {
                alert('Unable to save data');
                return;
            });
    }

    function tampilMember() {
        $('#modal-member').modal('show');
    }

    function pilihMember(id, kode) {
        $('#id_member').val(id);
        $('#member_code').val(kode);
        $('#discount').val('{{ $discount }}');
        loadForm($('#discount').val());
        $('#diterima').val(0).focus().select();
        hideMember();
    }

    function hideMember() {
        $('#modal-member').modal('hide');
    }

    function deleteData(url) {
        if (confirm('Are you sure you want to delete selected data?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload(() => loadForm($('#discount').val()));
                })
                .fail((errors) => {
                    alert('Unable to delete data');
                    return;
                });
        }
    }

    function loadForm(discount = 0, diterima = 0) {
        $('#total').val($('.total').text());
        $('#total_item').val($('.total_item').text());

        $.get(`{{ url('/transaksi/loadform') }}/${discount}/${$('.total').text()}/${diterima}`)
            .done(response => {
                $('#totalrp').val('$ '+ response.totalrp);
                $('#payrp').val('$ '+ response.payrp);
                $('#pay').val(response.pay);
                $('.tampil-pay').text('Pay: $ '+ response.payrp);
                $('.tampil-terbilang').text(response.terbilang);

                $('#kembali').val('$'+ response.kembalirp);
                if ($('#diterima').val() != 0) {
                    if (parseFloat(response.kembalirp) < 0) {
                        $('.tampil-pay').text('Payable Amount: $ ' + Math.abs(response.kembalirp));
                        $('#saveorder').prop('disabled', true); // Correct way to disable
                    } else {
                        $('.tampil-pay').text('Return: $ ' + response.kembalirp);
                        $('#saveorder').prop('disabled', false); // Correct way to disable
                    }
                    $('.tampil-terbilang').text(response.kembali_terbilang);
                }
            })
            .fail(errors => {
                alert('Unable to display data');
                return;
            })
    }
</script>
@endpush
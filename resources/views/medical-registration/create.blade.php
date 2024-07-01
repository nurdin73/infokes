@extends('layouts.app') @section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Pendaftaran Layanan poli</div>

                <div class="card-body">
                    @session('success')
                    <div class="alert alert-success">
                        {{ session("success") }}
                    </div>
                    @endsession 
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="fw-bold">Nama</label>
                        <p>{{ $patient->name }}</p>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold">NIK</label>
                        <p>{{ $patient->nik }}</p>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold">No Rekam Medis</label>
                        <p>{{ $patient->code }}</p>
                    </div>
                    <form
                        action="{{ route('register-pasien.poliRegistration') }}"
                        method="post"
                    >
                        @csrf
                        <input
                            type="hidden"
                            name="patient_id"
                            value="{{ $patient->id }}"
                        />
                        <div class="form-group">
                            <label class="fw-bold" for="service">Poli</label>
                            <select
                                name="service"
                                id="service"
                                class="form-select"
                            >
                                <option value="">Pilih Poli</option>
                                <option value="Poli Umum">Poli Umum</option>
                                <option value="Poli Gigi">Poli Gigi</option>
                                <option value="Poli Mata">Poli Mata</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="note" class="fw-bold">Catatan</label>
                            <textarea
                                name="note"
                                id=""
                                cols="30"
                                rows="2"
                                class="form-control"
                            ></textarea>
                        </div>
                        <div class="form-group mt-2">
                            <button class="btn btn-primary" type="submit">
                                Lanjutkan Pendaftaran layanan
                            </button>
                            <a class="btn btn-danger" href="{{ route('register-pasien') }}">
                                Kembali
                            </a>
                        </div>
                    </form>
                    <hr />
                    {{-- <h1>Riwayat Layanan</h1>
                    @if ($patient->medicals->count() === 0)
                    <div class="alert alert-info">
                        Tidak ada riwayat layanan
                    </div>
                    @endif
                    <div class="row">
                        @foreach ($patient->medicals as $medical)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="fw-bold"
                                            >Kode Pendaftaran</label
                                        >
                                        <p>{{ $medical->no_registration }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="fw-bold">Layanan</label>
                                        <p>{{ $medical->service }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="fw-bold d-block"
                                            >Status</label
                                        >
                                        <p
                                            class="badge @if ($medical->status == 'lunas') text-bg-success @else text-bg-danger @endif"
                                        >
                                            {{ $medical->status ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label class="fw-bold">No note</label>
                                        <p>{{ $medical->note ?? '-' }}</p>
                                    </div>
                                    @if ($medical->status != 'lunas')
                                    <a
                                        href="{{ route('register-pasien.payment', ['id' => $medical->id]) }}"
                                        class="btn d-block btn-primary"
                                        >Lakukan pembayaran</a
                                    >
                                    @else
                                    <button
                                        class="btn btn-primary detail d-block w-100"
                                        type="button"
                                        data-id="{{ $medical->id }}"
                                    >
                                        Lihat detail
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>

<div
    class="modal fade"
    id="detailModal"
    tabindex="-1"
    aria-labelledby="detailModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="createModalLabel">
                    Detail Pendaftaran & Pembayaran layanan
                </h1>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body" id="detail-medis">
                {{--  --}}
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection @section('script')
<script>
    $(".detail").on("click", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        $.ajax({
            type: "get",
            url: "{{ route('register-pasien') }}" + `/${id}`,
            success: function (response) {
                let items = '';
                if(response.payment.items.length === 0) {
                    items = `
                        <tr>
                            <td colspan="4" align="center" class="text-info">Item layanan tidak ditambahkan</td>
                        </tr>
                    `
                }
                response.payment.items.forEach((element, index) => {
                    items += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${element.name}</td>
                            <td>${element.qty}</td>
                            <td>Rp. ${element.price.toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.")}</td>
                        </tr>
                    `
                });
                $('#detailModal').modal('show')

                $('#detail-medis').html(`
                    <div class="form-group">
                        <label class="fw-bold">Kode Pendaftaran</label>
                        <p>${response.no_registration}</p>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold">Layanan</label>
                        <p>${response.service}</p>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold d-block">Status</label>
                        <p class="badge ${response.status == 'lunas' ? 'text-bg-success' : 'text-bg-error'}">${response.status}</p>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold">No note</label>
                        <p>${response.note}</p>
                    </div>
                    <h3 class="text-center">${response.payment.no}</h3>
                    <table class="table table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Tindakan/Obat</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${items}
                            <tr>
                                <td colspan="3" align="right">Metode Pembayaran</td>
                                <td colspan="1" align="right">${response.payment.payment_type}</td>
                            </tr>
                            <tr>
                                <td colspan="3" align="right">Dibayar Oleh</td>
                                <td colspan="1" align="right">${response.payment.paid_by}</td>
                            </tr>
                            <tr>
                                <td colspan="3" align="right">Total</td>
                                <td colspan="1" align="right">Rp. ${response.payment.price.toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.")}</td>
                            </tr>
                        </tbody>
                    </table>
                `)
            },
            error: function(err) {
                Swal.fire({
                    title: 'Error',
                    text: err.message,
                    icon: 'error'
                })
            }
        });
    });
</script>
@endsection

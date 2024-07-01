@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Form Pembayaran</div>

                <div class="card-body">
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
                      <label class="fw-bold">Kode Pendaftaran</label>
                      <p>{{ $medical->no_registration }}</p>
                  </div>
                  <div class="form-group">
                      <label class="fw-bold">Layanan</label>
                      <p>{{ $medical->service }}</p>
                  </div>
                  <div class="form-group">
                      <label class="fw-bold">Nama Pasien</label>
                      <p>{{ $medical->patient->name }}</p>
                  </div>
                  <div class="form-group">
                      <label class="fw-bold">No Rekam Medis</label>
                      <p>{{ $medical->patient->code }}</p>
                  </div>
                  <hr />
                  <form action="{{ route('register-pasien.storePayment') }}" method="post">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $medical->patient->id }}">
                    <input type="hidden" name="medical_registration_id" value="{{ $medical->id }}">
                    <div class="d-flex justify-content-between align-items-center">
                      <h3>List Tindakan/obat</h3>
                      <button class="btn btn-primary btn-sm" id="add-service">Tambah tindakan/obat</button>
                    </div>
                    <ul class="list-unstyled" id="list-tindakan">
                      {{--  --}}
                    </ul>
                    <hr />
                    <h3>Metode Pembayaran</h3>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="payment_type">Tipe Pembayaran</label>
                        <select name="payment_type" id="" class="form-select">
                          <option value="">Pilih pembayaran</option>
                          <option value="Uang Pribadi">Uang Pribadi</option>
                          <option value="BPJS">BPJS</option>
                          <option value="Asuransi Kesehatan">Asuransi Kesehatan</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label for="card_no">Nomor BPJS(jika memakai BPJS)</label>
                        <input type="number" class="form-control" name="card_no">
                      </div>
                      <div class="col-md-12">
                        <label for="paid_by">Dibayar Oleh</label>
                        <input type="text" name="paid_by" class="form-control">
                      </div>
                    </div>
                    <div class="mt-4">
                      <button class="btn btn-primary" type="submit">Lakukan Pembayaran</button>
                      <button class="btn btn-danger cancel" data-id="{{ $medical->id }}" data-code="{{ $medical->no_registration }}">Batalkan</button>
                      <a href="{{ route('register-pasien') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
  <script>
    $('#add-service').on('click', function(e) {
      e.preventDefault();
      const container = $('#list-tindakan');
      container.append(`
        <li class="row mb-2">
          <div class="col-md-4">
            <input type="text" name="name[]" placeholder="Nama Obat/Tindakan" class="form-control">
          </div>
          <div class="col-md-2">
            <input type="number" name="qty[]" min="1" value="1" placeholder="Jumlah" class="form-control">
          </div>
          <div class="col-md-4">
            <input type="number" name="price[]" min="0" placeholder="Harga" class="form-control">
          </div>
          <div class="col-md-2">
            <button class="btn w-100 d-block btn-danger hapus-layanan">Hapus</button>
          </div>
        </li>
      `)
    })

    $('#list-tindakan').on('click', 'li .hapus-layanan', function(e) {
      e.preventDefault();
      const container = $(this);
      container.parent().parent().remove();
    })


    $('.cancel').on('click', function(e) {
      e.preventDefault();
      const id = $(this).data('id');
      const code = $(this).data('code');
      Swal.fire({
        icon: 'question',
        title: 'Ingin membatalkan pendaftaran layanan?',
        text: `Pendaftaran layanan dengan kode ${code} akan dibatalkan`,
        showCancelButton: true,
        confirmButtonText: 'Ya, batalkan',
      }).then(result => {
        if(result.isConfirmed) {
          $.ajax({
            type: "put",
            url: "{{ route('register-pasien') }}" + `/${id}`,
            success: function (response) {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: response.message,
              }).then(() => {
                window.location.href = "{{ route('register-pasien') }}"
              })
            },
            error: function(err) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message,
              })
            }
          });
        }
      })
    })
  </script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
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
    <div class="d-flex justify-content-between align-items-center">
      <h1>Daftar Registrasi Layanan</h1>
      <div>
        <button type="button" class="btn btn-primary find-pasien" data-bs-toggle="modal" data-bs-target="#pasienModal">Cari Pasien</button>
        @if (auth()->user()->role === 'admin')
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Tambah Pasien Baru</button>
        @endif
        <a href="{{ route('register-pasien.export') }}" class="btn btn-success">Export Excel</a>
      </div>
    </div>
    <form action="{{ route('register-pasien') }}" method="get" class="mb-4">
      <div class="input-group">
        <input type="text" class="form-control" name="search" placeholder="Cari berdasarkan NIK, Nama, Nomor Rekam Medis, dan no pendaftaran" value="{{ request()->search }}">
        <button class="btn btn-primary w-25" id="search-data">Cari</button>
      </div>
    </form>
    @if ($registrations->count() === 0)
      <div class="alert alert-info">Layanan pasien tidak ditemukan</div>
    @endif
    <div class="row">
      @foreach ($registrations as $medical)
        <div class="col-md-3 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="form-group">
                <label class="fw-bold">Kode Pendaftaran</label>
                <p>{{ $medical->no_registration }}</p>
            </div>
            <div class="form-group">
              <label class="fw-bold">Nama Pasien</label>
              <p>{{ $medical->patient->name }}</p>
            </div>
            <div class="form-group">
              <label class="fw-bold">Nomor Rekam Medis</label>
              <p>{{ $medical->patient->code }}</p>
            </div>
            <div class="form-group">
                <label class="fw-bold">Layanan</label>
                <p>{{ $medical->service }}</p>
            </div>
            <div class="form-group">
                <label class="fw-bold d-block">Status</label>
                <p class="badge @if ($medical->status == 'lunas') text-bg-success @elseif($medical->status == 'belum lunas') text-bg-warning @else text-bg-danger @endif">
                    {{ $medical->status ?? '-' }}
                </p>
            </div>
            <div class="form-group">
                <label class="fw-bold">No note</label>
                <p>{{ $medical->note ?? '-' }}</p>
            </div>
            @if (in_array($medical->status, ['lunas', 'dibatalkan']))
              <button class="btn btn-primary detail d-block w-100" type="button" data-id="{{ $medical->id }}">Lihat detail</button>
            @else
              <a href="{{ route('register-pasien.payment', ['id' => $medical->id]) }}" class="btn d-block btn-success">Lakukan pembayaran</a>
              <button class="btn btn-danger d-block w-100 mt-2 cancel" data-code="{{ $medical->no_registration }}" data-id="{{ $medical->id }}">Batalkan</button>
            @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
    {!! $registrations->links() !!}
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

<div class="modal fade" id="pasienModal" tabindex="-1" aria-labelledby="pasienModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="pasienModalLabel">Data Pasien</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control" placeholder="Cari berdasarkan NIK, Nama, Nomor Rekam Medis" id="cari-pasien" name="search">
        <div id="list-pasien" class="mt-4"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createModalLabel">Tambah Data Pasien</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('register-pasien.store') }}" method="post" id="formAdd">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label for="name" class="form-label">Nama</label>
            <input type="text" class="form-control" name="name" placeholder="Masukkan nama sesuai KTP" required>
          </div>
          <div class="form-group">
            <label for="nik" class="form-label">No KTP</label>
            <input type="number" class="form-control" name="nik" placeholder="Masukkan NIK">
          </div>
          <div class="form-group">
            <label for="birthday" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" name="birthday">
          </div>
          <div class="form-group">
            <label for="gender" class="form-label">jenis kelamin</label>
            <select name="gender" class="form-select">
              <option value="Laki-Laki">Laki Laki</option>
              <option value="Perempuan">Perempuan</option>
            </select>
          </div>
          <div class="form-group">
            <label for="address" class="form-label">Alamat Lengkap</label>
            <textarea type="text" class="form-control" name="address" placeholder="Masukkan Alamat Lengkap"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Lanjutkan pendaftaran</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection


@section('script')
    <script>
      function searchPasien(search = '') {
        const container = $('#list-pasien');
        $.ajax({
          type: "get",
          url: "{{ route('pasien.search') }}",
          data: {
            search: search
          },
          beforeSend: function() {
            container.html(`
              <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>  
              </div>
            `)
          },
          success: function (response) {
            container.empty();
            if(response.data.length === 0) {
              container.html(`
                <div class="alert alert-info">Pasien tidak ditemukan</div>
              `)
              return;
            }
            let items = '';
            response.data.forEach(item => {
              items += `
                <div class="col-md-3 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="form-group">
                        <label class="fw-bold">Nama</label>
                        <p>${item.name}</p>
                      </div>
                      <div class="form-group">
                        <label class="fw-bold">NIK</label>
                        <p>${item.nik}</p>
                      </div>
                      <div class="form-group">
                        <label class="fw-bold">No Rekam Medis</label>
                        <p>${item.code}</p>
                      </div>
                      <a class="btn btn-primary w-100" href="{{ route('register-pasien') }}/${item.id}/medic">Pilih Pasien</a>
                    </div>
                  </div>  
                </div>  
              `
            })
            container.html(`
              <div class="row">
                ${items}
              </div>
            `)
          },
          error: function(err) {
            container.empty();
            container.html(`
              <div class="alert alert-danger">${err.message}</div>
            `)
          }
        });
      }
      $('.find-pasien').on('click', function(e) {
        e.preventDefault();
        searchPasien()
      })

      let timeout;
      $('#cari-pasien').on('keyup', function(e) {
        const val = e.target.value;
        clearTimeout(timeout)
        timeout = setTimeout(() => {
          searchPasien(val);
        }, 1000);
      })

      $(".detail").on("click", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        $.ajax({
            type: "get",
            url: "{{ route('register-pasien') }}" + `/${id}`,
            success: function (response) {
                let items = '';
                if(response.payment?.items.length === 0) {
                    items = `
                        <tr>
                            <td colspan="4" align="center" class="text-info">Item layanan tidak ditambahkan</td>
                        </tr>
                    `
                }
                response.payment?.items.forEach((element, index) => {
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
                        <p class="badge ${response.status == 'lunas' ? 'text-bg-success' : (response.status === 'dibatalkan' ? 'text-bg-danger' : 'text-bg-warning')}">${response.status}</p>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold">No note</label>
                        <p>${response.note}</p>
                    </div>
                    ${response.payment ? (
                      `<h3 class="text-center">${response.payment?.no ?? 'N/A'}</h3>
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
                                    <td colspan="1" align="right">${response.payment?.payment_type ?? 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right">Dibayar Oleh</td>
                                    <td colspan="1" align="right">${response.payment?.paid_by ?? 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right">Total</td>
                                    <td colspan="1" align="right">Rp. ${(response.payment?.price ?? 0).toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.")}</td>
                                </tr>
                            </tbody>
                        </table>`
                    ) : ''}
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
                window.location.reload();
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
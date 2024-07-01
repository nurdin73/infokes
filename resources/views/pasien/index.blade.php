@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @session('success')
              <div class="alert alert-success">
                  {{ session('success') }}
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
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                  <span>{{ __('Master Data Pasien') }}</span>
                  <div>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#createModal" class="btn btn-primary" id="createModalButton">Tambah Data</button>
                    <a href="{{ route('pasien.export') }}" class="btn btn-success">Export Excel</a>
                  </div>
                </div>

                <div class="card-body">
                  <div class="d-flex justify-content-end">
                    <form action="{{ route('pasien.index') }}" method="get">
                      <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Cari berdasarkan NIK, Nama" value="{{ request()->search }}">
                        <button class="btn btn-primary">Cari</button>
                      </div>
                    </form>
                  </div>
                  <table class="table table-sm table-striped table-hover">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>No Rekam Medis</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                          $no = $patients->firstItem();
                      @endphp
                      @if ($patients->count() === 0)
                        <tr>
                          <td colspan="6" align="center">Pasien tidak ditemukan</td>
                        </tr>
                      @endif
                      @foreach ($patients as $patien)
                        <tr>
                          <td>{{ $no++ }}</td>
                          <td>{{ $patien->code }}</td>
                          <td>{{ $patien->name }}</td>
                          <td>{{ $patien->nik }}</td>
                          <td>{{ $patien->address }}</td>
                          <td>
                            <div class="btn-group">
                              <button class="btn btn-primary btn-sm edit" data-id="{{ $patien->id }}">Edit</button>
                              <button class="btn btn-danger btn-sm hapus" data-id="{{ $patien->id }}">Hapus</button>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  {!! $patients->links() !!}
                </div>
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
      <form action="{{ route('pasien.store') }}" method="post" id="formAdd">
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
              <option value="">Pilih</option>
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
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="updateModalLabel">Edit Data Pasien</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('pasien.store') }}" method="post" id="formUpdate">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="form-group">
            <label for="name" class="form-label">Nama</label>
            <input type="text" class="form-control" name="name" placeholder="Masukkan nama sesuai KTP">
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
              <option value="">Pilih</option>
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
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection


@section('script')
  <script>
    $('.edit').on('click', function(e) {
      e.preventDefault();
      const id = $(this).data('id');
      $.ajax({
        type: "get",
        url: "{{ route('pasien.index') }}" + `/${id}`,
        success: function (response) {
          $('#updateModal').modal('show')
          $('#formUpdate').attr('action', "{{ route('pasien.index') }}" + `/${id}`)
          $('#formUpdate input[name=name]').val(response.name)
          $('#formUpdate input[name=nik]').val(response.nik)
          $('#formUpdate input[name=birthday]').val(response.birthday)
          $('#formUpdate select[name=gender]').val(response.gender)
          $('#formUpdate textarea[name=address]').val(response.address)
        },
        error: function(err) {
          Swal.fire({
            title: err.message,
            icon: 'error'
          })
        }
      });
    })
    $('.hapus').on('click', function(e) {
      e.preventDefault();
      const id = $(this).data('id');
      Swal.fire({
        title: 'Hapus Data',
        text: 'Data akan terhapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus'
      }).then(result => {
        if(result.isConfirmed) {
          $.ajax({
            type: "delete",
            url: "{{ route('pasien.index') }}" + "/" + id,
            success: function (response) {
              Swal.fire({
                title: response.message,
                icon: 'success'
              }).then(() => {
                window.location.reload();
              }) 
            },
            error: function(err) {
              Swal.fire({
                title: err.message,
                icon: 'error'
              })
            }
          });
        }
      })
    })
  </script>
@endsection
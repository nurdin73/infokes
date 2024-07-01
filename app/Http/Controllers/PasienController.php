<?php

namespace App\Http\Controllers;

use App\Exports\PasienExport;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role !== 'admin') return abort(403, "Anda tidak memiliki akses");
        $search = request()->search ?? '';
        $results = Patient::query();
        if ($search != '') $results = $results->where(function (Builder $q) use ($search) {
            return $q->where('name', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
        });
        $data['patients'] = $results->paginate();
        return view('pasien.index', $data);
    }

    public function getPasiens()
    {
        $search = request()->search ?? '';
        $results = Patient::query();
        if ($search != '') $results = $results->where(function (Builder $q) use ($search) {
            return $q->where('name', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
        });
        return $results->paginate();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required',
            'nik' => 'required|unique:patients,nik',
            'address' => 'required',
            'gender' => 'required',
            'birthday' => 'required',
        ]);
        $data['code'] = $this->createCode();
        $create = Patient::create($data);
        return redirect()->route('pasien.index')->with('success', "Pasien baru berhasil ditambahkan");
    }

    protected function createCode()
    {
        $query = "select max(substring(code, 3, 6)) as code from patients";
        $result = DB::select($query);
        $prefix = "RM";
        $urutan = $result[0]->code;
        $urutan++;
        $code = sprintf('%06s', $urutan);
        return $prefix . $code;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $find = Patient::find($id);
        if (!$find) return response(['message' => "Data tidak ditemukan"], 404);
        return $find;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->validate($request, [
            'name' => 'required',
            'nik' => "required|unique:patients,nik,{$id},id",
            'address' => 'required'
        ]);
        $find = Patient::find($id);
        $find->update($data);
        return redirect()->route('pasien.index')->with('success', "Data Pasien berhasil diubah");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $find = Patient::find($id);
        if (!$find) return response(['message' => "Data tidak ditemukan"], 404);
        $find->delete();
        return response(['message' => "Data berhasil dihapus"]);
    }

    public function export()
    {
        return (new PasienExport())->download("pasien.xlsx");
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\RegistrationPoliExport;
use App\Models\MedicalRegistration;
use App\Models\Patient;
use App\Models\PaymentMedicalRegistration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterPasienController extends Controller
{
    public function index()
    {
        $search = request()->search ?? '';
        $results = MedicalRegistration::query()->with('patient', 'payment');
        if ($search != '') $results = $results->whereHas('patient', function (Builder $q) use ($search) {
            return $q->where('name', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
        })->orWhere('no_registration', 'like', "%{$search}%");
        $data['registrations'] = $results->paginate(8);
        return view('medical-registration.index', $data);
    }

    public function create()
    {
        // 
    }

    public function show($id)
    {
        return MedicalRegistration::query()->with('payment.items')->find($id);
    }

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
        return redirect()->route('register-pasien.registerMedic', ['patient_id' => $create->id]);
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

    public function payment($id)
    {
        $find = MedicalRegistration::query()->with('patient')->find($id);
        if (!$find) return abort(404);
        if ($find->status !== 'belum lunas') return abort(404);
        return view('medical-registration.payment', ['medical' => $find]);
    }

    public function storePayment(Request $request)
    {
        $data = $this->validate($request, [
            'medical_registration_id' => 'required',
            'patient_id' => 'required',
            'payment_type' => 'required',
            'card_no' => 'nullable',
            'paid_by' => 'required',
            'name' => 'nullable',
            'qty' => 'nullable',
            'price' => 'nullable',
        ]);

        $find = MedicalRegistration::find($data['medical_registration_id']);
        if (!$find) return abort(404, "Riwayat layanan tidak ditemukan");
        $find->status = 'lunas';
        $find->save();

        $items = [];
        $priceTotal = 0;
        if (isset($data['name'])) {
            foreach ($data['name'] as $key => $value) {
                $items[] = [
                    'name' => $value,
                    'qty' => $data['qty'][$key],
                    'price' => $data['price'][$key],
                ];
                $priceTotal += $data['price'][$key] * $data['qty'][$key];
            }
        }
        $data['price'] = $priceTotal;
        $data['no'] = $this->createNoPayment();
        $create = PaymentMedicalRegistration::create($data);


        $mapItem = collect($items)->map(function ($q) use ($create) {
            $q['payment_id'] = $create->id;
            return $q;
        })->toArray();
        DB::table('item_services')->insert($mapItem);

        return redirect()->route('register-pasien')->with('success', 'Pembayaran layanan berhasil');
    }

    private function createNoPayment()
    {
        $query = "select max(substring(no, 4, 6)) as code from payment_medical_registrations";
        $result = DB::select($query);
        $prefix = "PAY";
        $urutan = $result[0]->code;
        $urutan++;
        $code = sprintf('%06s', $urutan);
        return $prefix . $code;
    }

    public function registerMedic($patient_id)
    {
        $find = Patient::query()->find($patient_id);
        if (!$find) return abort(404);
        return view('medical-registration.create', ['patient' => $find]);
    }

    public function poliRegistration(Request $request)
    {
        $data = $this->validate($request, [
            'patient_id' => 'required|exists:patients,id',
            'service' => 'required',
            'note' => 'nullable'
        ]);
        $data['status'] = 'belum lunas';
        $data['no_registration'] = $this->createCodeRegistration();
        $create = MedicalRegistration::create($data);
        return redirect()->route('register-pasien.payment', ['id' => $create->id]);
    }

    protected function createCodeRegistration()
    {
        $query = "select max(substring(no_registration, 4, 6)) as code from medical_registrations";
        $result = DB::select($query);
        $prefix = "REG";
        $urutan = $result[0]->code;
        $urutan++;
        $code = sprintf('%06s', $urutan);
        return $prefix . $code;
    }

    public function export()
    {
        return (new RegistrationPoliExport())->download('layanan.xlsx');
    }

    public function destroy($id)
    {
        $find = MedicalRegistration::find($id);
        if (!$find) return response(['message' => "Layanan pendaftaran tidak ditemukan"], 404);
        $find->delete();
        return response(['message' => "Layanan berhasil dibatalkan"]);
    }

    public function cancel($id)
    {
        $find = MedicalRegistration::find($id);
        if (!$find) return response(['message' => "Layanan pendaftaran tidak ditemukan"], 404);
        $find->status = 'dibatalkan';
        $find->save();
        return response(['message' => "Layanan berhasil dibatalkan"]);
    }
}
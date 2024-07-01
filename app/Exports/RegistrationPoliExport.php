<?php

namespace App\Exports;

use App\Models\MedicalRegistration;
use App\Models\Patient;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RegistrationPoliExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    public function query()
    {
        return MedicalRegistration::query()->with('patient', 'payment');
    }

    public function headings(): array
    {
        return [
            'No Pendaftaran',
            'Jenis Layanan',
            'Nama Pasien',
            'No Rekam Medis',
            'Status',
            'Biaya',
            'Note',
        ];
    }

    public function styles(Worksheet $sheet)
    {

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'italic' => true,
                    'color' => [
                        'rgb' => 'ffffff'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => Color::COLOR_DARKBLUE],
                ]
            ]
        ];
    }

    public function map($row): array
    {
        return [
            $row->no_registration,
            $row->service,
            $row->patient?->name,
            $row->patient?->code,
            $row->status,
            "Rp. " . number_format($row->payment?->price ?? 0),
            $row->note,
        ];
    }
}

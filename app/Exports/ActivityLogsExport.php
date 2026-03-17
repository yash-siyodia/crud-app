<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivityLogsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->with('user')->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'User Email',
            'Action',
            'Model',
            'Model ID',
            'Details',
            'IP Address',
            'Created At',
        ];
    }

    public function map($log): array
    {
        $details = $log->details ? json_encode($log->details, JSON_PRETTY_PRINT) : '';
        
        return [
            $log->id,
            $log->user ? $log->user->name : 'Guest',
            $log->user ? $log->user->email : '',
            $log->action,
            $log->model ?? '',
            $log->model_id ?? '',
            $details,
            $log->ip_address ?? '',
            $log->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

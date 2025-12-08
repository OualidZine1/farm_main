<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductUsageExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $transactions;
    protected $summary;

    public function __construct($transactions, $summary)
    {
        $this->transactions = $transactions;
        $this->summary = $summary;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Product',
            'Quantity',
            'Field',
            'Used By',
            'Notes'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->date->format('Y-m-d'),
            $transaction->product->name,
            $transaction->quantity,
            $transaction->field ? $transaction->field->bloc_number . ' (' . $transaction->field->crop_type . ')' : 'N/A',
            $transaction->usedBy ? $transaction->usedBy->name : 'N/A',
            $transaction->notes
        ];
    }

    public function title(): string
    {
        return 'Product Usage Report';
    }

    public function styles(Worksheet $sheet)
    {
        // Make the first row bold
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        // Add summary information
        $row = $this->transactions->count() + 3; // Add some space after the data
        
        // Add summary header
        $sheet->setCellValue('A' . $row, 'Summary');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        // Add summary headers
        $sheet->setCellValue('A' . $row, 'Product');
        $sheet->setCellValue('B' . $row, 'Field');
        $sheet->setCellValue('C' . $row, 'Used By');
        $sheet->setCellValue('D' . $row, 'Total Quantity');
        $sheet->setCellValue('E' . $row, 'Usage Count');
        $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
        $row++;
        
        // Add summary data
        foreach ($this->summary as $item) {
            $sheet->setCellValue('A' . $row, $item->product->name);
            $sheet->setCellValue('B' . $row, $item->field ? $item->field->bloc_number . ' (' . $item->field->crop_type . ')' : 'N/A');
            $sheet->setCellValue('C' . $row, $item->usedBy ? $item->usedBy->name : 'N/A');
            $sheet->setCellValue('D' . $row, $item->total_quantity);
            $sheet->setCellValue('E' . $row, $item->usage_count);
            $row++;
        }
        
        // Add grand total
        $sheet->setCellValue('C' . $row, 'Grand Total:');
        $sheet->setCellValue('D' . $row, collect($this->summary)->sum('total_quantity'));
        $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
    }
}

<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $userId;

    public function __construct($startDate, $endDate, $userId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userId = $userId;
    }

    public function collection()
    {
        $query = Sale::with(['user', 'details.product'])
            ->select('sales.*', 'users.name as user_name')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->whereBetween('sales.created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);

        if ($this->userId !== 'all') {
            $query->where('sales.user_id', $this->userId);
        }

        return $query->orderBy('sales.created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Información de Venta',
            'ID',
            'Usuario',
            'Fecha',
            'Total Venta (Q)',
            'Costo Total (Q)',
            'Ganancia (Q)',
            'Detalles de Productos',
            'Código',
            'Nombre',
            'Marca',
            'Cantidad',
            'Precio Unitario (Q)',
            'Subtotal (Q)'
        ];
    }

    public function map($sale): array
    {
        $rows = [];
        $first = true;
        $costoTotal = $sale->details->sum('cost_total');
        $ganancia = $sale->total - $costoTotal;

        foreach ($sale->details as $detail) {
            if ($first) {
                $rows[] = [
                    'Información de Venta',
                    $sale->id,
                    $sale->user_name,
                    $sale->created_at->format('d/m/Y H:i:s'),
                    'Q ' . number_format($sale->total, 2),
                    'Q ' . number_format($costoTotal, 2),
                    'Q ' . number_format($ganancia, 2),
                    'Detalles de Productos',
                    $detail->product->code,
                    $detail->product->name,
                    $detail->product->brand,
                    $detail->quantity,
                    'Q ' . number_format($detail->price, 2),
                    'Q ' . number_format($detail->subtotal, 2)
                ];
                $first = false;
            } else {
                $rows[] = [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $detail->product->code,
                    $detail->product->name,
                    $detail->product->brand,
                    $detail->quantity,
                    'Q ' . number_format($detail->price, 2),
                    'Q ' . number_format($detail->subtotal, 2)
                ];
            }
        }

        // Agregar una línea en blanco después de cada venta
        $rows[] = array_fill(0, 14, '');

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para encabezados
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3A86FF'],
            ],
        ]);

        // Obtener la última fila
        $lastRow = $sheet->getHighestRow();

        // Identificar y aplicar estilo a las filas con "Detalles de Productos"
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($sheet->getCell('H' . $row)->getValue() === 'Detalles de Productos') {
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '3A86FF'],
                    ],
                ]);
            }
        }

        // Aplicar bordes y alineación
        $sheet->getStyle('A1:N' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Ajustar el ancho de las columnas
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Alinear las columnas numéricas a la derecha
        $sheet->getStyle('E2:G' . $lastRow)->getAlignment()->setHorizontal('right');
        $sheet->getStyle('L2:N' . $lastRow)->getAlignment()->setHorizontal('right');

        // Resaltar las filas de información principal y aplicar colores a las ganancias
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($sheet->getCell('A' . $row)->getValue() === 'Información de Venta') {
                // Estilo para la fila de información principal
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F2F5'],
                    ],
                    'font' => ['bold' => true],
                ]);

                // Colorear la ganancia según si es positiva o negativa
                $ganancia = str_replace(['Q', ',', ' '], '', $sheet->getCell('G' . $row)->getValue());
                if ($ganancia > 0) {
                    $sheet->getStyle('G' . $row)->applyFromArray([
                        'font' => ['color' => ['rgb' => '008000']], // Verde para ganancias positivas
                    ]);
                } elseif ($ganancia < 0) {
                    $sheet->getStyle('G' . $row)->applyFromArray([
                        'font' => ['color' => ['rgb' => 'FF0000']], // Rojo para ganancias negativas
                    ]);
                }
            }
        }

        return $sheet;
    }
}

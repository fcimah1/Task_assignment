<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\ItemsTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemsTransactionsImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    private int $processedCount = 0;

    public function __construct()
    {
        // زيادة الـ memory والوقت للملفات الكبيرة
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '0');
    }

    public function collection(Collection $rows)
    {
        $batchData = [];
        $isFirstRow = true;

        foreach ($rows as $row) {
            // Log the first row to see actual column names
            if ($isFirstRow) {
                Log::info('Excel columns detected', ['keys' => $row->keys()->toArray()]);
                Log::info('First row data', ['data' => $row->toArray()]);
                $isFirstRow = false;
            }
            if ($row->filter()->isEmpty()) {
                continue;
            }

            try {
                // Franco-Arabic names (from actual log) + Arabic + English fallbacks
                $quantity = $row['alkmy'] ?? $row['الكمية'] ?? $row['quantity'] ?? 0;
                $unitCost = $row['tklf_alohdat'] ?? $row['تكلفة_الوحدات'] ?? $row['unit_cost'] ?? 0;
                $total = $row['alagmaly_2'] ?? $row['الاجمالي_2'] ?? $row['total'] ?? 0;
                $itemCode = $row['kod_alsnf'] ?? $row['كود_الصنف'] ?? $row['item_code'] ?? '';
                $itemName = $row['osf_alsnf'] ?? $row['وصف_الصنف'] ?? $row['item_name'] ?? '';
                $unit = $row['alohdh'] ?? $row['الوحده'] ?? $row['unit'] ?? '';
                $date = $row['altarykh'] ?? $row['التاريخ'] ?? $row['date'] ?? null;
                $time = $row['alokt'] ?? $row['الوقت'] ?? $row['time'] ?? null;
                $costCenter = $row['mrkz_altklf'] ?? $row['مركز_التكلفة'] ?? $row['cost_center'] ?? 0;

                // Log first few rows for debugging
                if ($this->processedCount < 3) {
                    Log::info('Row values', [
                        'quantity_raw' => $quantity,
                        'quantity_type' => gettype($quantity),
                        'time_raw' => $time,
                        'time_type' => gettype($time),
                    ]);
                }

                // تنظيف الأرقام - Excel يعيدها كـ float مباشرة
                $quantity = is_numeric($quantity) ? floatval($quantity) : floatval(str_replace(',', '', (string) $quantity));
                $unitCost = is_numeric($unitCost) ? floatval($unitCost) : floatval(str_replace(',', '', (string) $unitCost));
                $total = is_numeric($total) ? floatval($total) : floatval(str_replace(',', '', (string) $total));

                // حفظ التاريخ والوقت
                $formattedDate = null;
                $formattedTime = null;

                // التاريخ - تحويله لصيغة MySQL
                if (!empty($date)) {
                    try {
                        if (is_numeric($date)) {
                            // Excel serial number
                            $formattedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                        } else {
                            $formattedDate = Carbon::parse($date)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        $formattedDate = null;
                    }
                }

                // الوقت - تحويله لصيغة MySQL
                if (!empty($time)) {
                    try {
                        if (is_numeric($time)) {
                            // Excel time fraction (0.5 = 12:00:00)
                            $formattedTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($time)->format('H:i');
                        } else {
                            $formattedTime = (string) $time;
                        }
                    } catch (\Exception $e) {
                        $formattedTime = null;
                    }
                }

                $batchData[] = [
                    'item_code'   => $itemCode,
                    'item_name'   => $itemName,
                    'quantity'    => $quantity,
                    'unit'        => $unit,
                    'trx_date'    => $formattedDate,
                    'trx_time'    => $formattedTime,
                    'cost_center' => intval($costCenter),
                    'unit_cost'   => $unitCost,
                    'total'       => $total,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            } catch (\Exception $e) {
                Log::error('Error preparing row: ' . $e->getMessage());
                continue;
            }
        }

        // Batch insert for performance
        if (!empty($batchData)) {
            try {
                DB::table('items_transactions')->insert($batchData);
                $this->processedCount += count($batchData);
                Log::info('Batch inserted', ['count' => count($batchData), 'total' => $this->processedCount]);
            } catch (\Exception $e) {
                Log::error('Batch insert error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Process 1000 rows at a time to reduce memory usage
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Batch insert size
     */
    public function batchSize(): int
    {
        return 1000;
    }
}

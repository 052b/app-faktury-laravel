<?php

namespace App\Repository;

use App\Enums\InvStatus;
use App\Enums\InvType;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getOverdue($nextDays = null, $paginate = true): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
    {
        $conditions = [
            ['site_invoices.type', '=', InvType::SELL],
            ['site_invoices.deleted', '=', 0],
            ['site_invoices.status', '=', InvStatus::UNPAID],
        ];

        $query = DB::table('site_invoices')
            ->select(
                'site_invoices.id',
                'site_invoices.invoice_number',
                'site_contractor.name',
                DB::raw('sum(site_invoices_positions.price_netto) AS sum_netto'),
                'site_invoices.invoice_term_date'
            )
            ->join('site_invoices_positions', 'site_invoices.id', '=', 'site_invoices_positions.id_invoice')
            ->join('site_contractor', 'site_invoices.id_contractor', '=', 'site_contractor.id')
            ->where($conditions)
            ->groupBy('site_invoices.id', 'site_invoices.invoice_number', 'site_contractor.name', 'site_invoices.invoice_term_date')
            ->orderBy('create_date');


        $from = now()->format('Y-m-d');
        if ($nextDays) {
            $to = now()->addDays(7)->format('Y-m-d');
            $query->whereBetween('site_invoices.invoice_term_date', [$from, $to]);
        } else {
            $query->where('site_invoices.invoice_term_date', '<', $from);
        }

        if ($paginate) {
            return $query->paginate();
        }

        return $query->get();
    }

    public function getTotals(): array
    {
        $unpaidNettoSum        = 0;
        $unpaidVatSum          = 0;
        $currentMonthAmount    = 0;
        $currentMonthVatAmount = 0;
        $invoicesPaidNetto     = 0;
        $invoicesPaidGross     = 0;
        $invoicesUnpaidNet     = 0;
        $invoicesUnpaidGross   = 0;
        $costNetto             = 0;
        $costVat               = 0;
        $costFixedNetto        = 0;
        $costFixedVat          = 0;

        $month = now()->month;

        $unpaidTotal = $this->getUnpaidSellTotal();
        if ($unpaidTotal) {
            $unpaidNettoSum = $unpaidTotal->sum_netto ?? 0;
            $unpaidVatSum   = $unpaidTotal->sum_vat ?? 0;
        }

        $currentMonth = $this->getCurrentMonthAmount();
        if ($currentMonth) {
            $currentMonthAmount    = $currentMonth->sum_netto ?? 0;
            $currentMonthVatAmount = $currentMonth->sum_vat ?? 0;
        }

        $paidUnpaid = $this->getPaidUnpaidThisMonthTotal();
        foreach ($paidUnpaid as $item) {
            if ($item->status == InvStatus::UNPAID) {
                $invoicesUnpaidNet   = $item->sum_netto;
                $invoicesUnpaidGross = $item->sum_brutto;
            }
            if ($item->status == InvStatus::PAID) {
                $invoicesPaidNetto = $item->sum_netto;
                $invoicesPaidGross = $item->sum_brutto;
            }
        }

        $costsTotal = $this->getCostsTotal();
        if ($costsTotal) {
            foreach ($costsTotal as $item) {
                if ($item->type == InvType::COST) {
                    $costNetto = $item->sum_netto;
                    $costVat   = $item->sum_vat;
                }

                if ($item->type == InvType::FIXED_COST) {
                    $costFixedNetto = $item->sum_netto;
                    $costFixedVat   = $item->sum_vat;
                }
            }
        }

        $costNetto += $costFixedNetto;
        $costVat   += $costFixedVat;

        return [
            'Suma przeterminowanych faktur'                             => [$unpaidNettoSum . ' zł (+' . $unpaidVatSum . ' VAT)'],
            'Suma faktur wystawionych w tym miesiącu'                   => [$currentMonthAmount . ' zł (+ ' . $currentMonthVatAmount . ' zł VAT)'],
            'Suma kosztów w tym miesiącu'                               => [
                $costNetto.' zł (+ '.$costVat.'zł VAT)',
                'W tym kosztów stałych: '.$costFixedNetto.' zł(+ '.$costFixedVat.' zł VAT)'
            ],
            'Faktury opłacone w miesiącu ' . sprintf('%02d', $month)    => [$invoicesPaidNetto . ' zł (brutto: ' . $invoicesPaidGross . 'zł)'],
            'Faktury nieopłacone w miesiącu ' . sprintf('%02d', $month) => [$invoicesUnpaidNet . ' zł (brutto: ' . $invoicesUnpaidGross . ' zł)'],
            'Wpływy w miesiącu 09 - TODO'                                      => ['0,00zł (brutto: 0,00zł)']
        ];
    }

    public function getUnpaidSellTotal(): object|null
    {
        $conditions = [
            ['site_invoices.type', '=', InvType::SELL],
            ['site_invoices.deleted', '=', 0],
            ['site_invoices.status', '=', InvStatus::UNPAID],
        ];

        return DB::table('site_invoices')
            ->select(
                DB::raw('sum(site_invoices_positions.price_netto) AS sum_netto'),
                DB::raw('sum(site_invoices_positions.price_brutto - site_invoices_positions.price_netto) AS sum_vat'),
            )
            ->join('site_invoices_positions', 'site_invoices.id', '=', 'site_invoices_positions.id_invoice')
            ->where($conditions)
            ->first();
    }

    public function getCurrentMonthAmount(): object|null
    {
        $conditions = [
            ['site_invoices.type', '=', InvType::SELL],
            ['site_invoices.deleted', '=', 0],
            [DB::raw('MONTH(site_invoices.create_date)'), '=', now()->month],
            [DB::raw('YEAR(site_invoices.create_date)'), '=', now()->year]
        ];

        return DB::table('site_invoices')
            ->select(
                DB::raw('sum(site_invoices_positions.price_netto) AS sum_netto'),
                DB::raw('sum(site_invoices_positions.price_brutto - site_invoices_positions.price_netto) AS sum_vat'),
            )
            ->join('site_invoices_positions', 'site_invoices.id', '=', 'site_invoices_positions.id_invoice')
            ->where($conditions)
            ->first();
    }

    public function getPaidUnpaidThisMonthTotal(): object|null
    {
        $paid_conditions = [
            ['site_invoices.deleted', '=', 0],
            ['site_invoices.status', '=', InvStatus::PAID],
            [DB::raw('MONTH(site_invoices.payment_date)'), '=', now()->month],
            [DB::raw('YEAR(site_invoices.payment_date)'), '=', now()->year]
        ];

        $paid = DB::table('site_invoices')
            ->select(
                'site_invoices.status',
                DB::raw('sum(site_invoices_positions.price_netto) AS sum_netto'),
                DB::raw('sum(site_invoices_positions.price_brutto) AS sum_brutto'),
            )
            ->join('site_invoices_positions', 'site_invoices.id', '=', 'site_invoices_positions.id_invoice')
            ->where($paid_conditions)
            ->groupBy('site_invoices.status');


        $unpaid_conditions = [
            ['site_invoices.deleted', '=', 0],
            ['site_invoices.status', '=', InvStatus::UNPAID],
            [DB::raw('MONTH(site_invoices.invoice_term_date)'), '=', now()->month],
            [DB::raw('YEAR(site_invoices.invoice_term_date)'), '=', now()->year]
        ];

        return DB::table('site_invoices')
            ->select(
                'site_invoices.status',
                DB::raw('sum(site_invoices_positions.price_netto) AS sum_netto'),
                DB::raw('sum(site_invoices_positions.price_brutto) AS sum_brutto'),
            )
            ->join('site_invoices_positions', 'site_invoices.id', '=', 'site_invoices_positions.id_invoice')
            ->where($unpaid_conditions)
            ->union($paid)
            ->groupBy('site_invoices.status')
            ->get();
    }

    public function getCostsTotal(): object|null
    {
        $conditions_costs = [
            ['site_invoices.deleted', '=', 0],
            ['site_invoices.type', '=', InvType::COST],
            [DB::raw('MONTH(site_invoices.sell_date)'), '=', now()->month],
            [DB::raw('YEAR(site_invoices.sell_date)'), '=', now()->year]
        ];

        $cost = DB::table('site_invoices')
            ->select(
                'site_invoices.type',
                DB::raw('sum(site_invoices_positions.price_netto) AS sum_netto'),
                DB::raw('sum(site_invoices_positions.price_brutto) AS sum_brutto'),
                DB::raw('sum(site_invoices_positions.price_brutto - site_invoices_positions.price_netto) AS sum_vat'),
            )
            ->join('site_invoices_positions', 'site_invoices.id', '=', 'site_invoices_positions.id_invoice')
            ->where($conditions_costs)
            ->groupBy('site_invoices.type');

        $conditions_fixed = [
            ['site_invoices.deleted', '=', 0],
            ['site_invoices.type', '=', InvType::FIXED_COST],
            ['site_invoices.start_date', '<=', now()->format('Y-m-d')],
            ['site_invoices.end_date', '>=', now()->format('Y-m-d')],
        ];

        return DB::table('site_invoices')
            ->select(
                'site_invoices.type',
                DB::raw('sum(site_invoices_positions.price_netto) AS sum_netto'),
                DB::raw('sum(site_invoices_positions.price_brutto) AS sum_brutto'),
                DB::raw('sum(site_invoices_positions.price_brutto - site_invoices_positions.price_netto) AS sum_vat'),
            )
            ->join('site_invoices_positions', 'site_invoices.id', '=', 'site_invoices_positions.id_invoice')
            ->where($conditions_fixed)
            ->union($cost)
            ->groupBy('site_invoices.type')
            ->get();
    }
}

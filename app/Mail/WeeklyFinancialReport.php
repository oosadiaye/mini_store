<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyFinancialReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tenantName;
    public $tenantSlug;
    public $startDate;
    public $endDate;
    public $totalRevenue;
    public $totalExpenses;
    public $netProfit;
    public $topProduct;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tenantName, $tenantSlug, $startDate, $endDate, $totalRevenue, $totalExpenses, $netProfit, $topProduct = null)
    {
        $this->tenantName = $tenantName;
        $this->tenantSlug = $tenantSlug;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalRevenue = $totalRevenue;
        $this->totalExpenses = $totalExpenses;
        $this->netProfit = $netProfit;
        $this->topProduct = $topProduct;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Weekly Financial Snapshot - ' . $this->tenantName)
                    ->view('emails.weekly-financial-report');
    }
}

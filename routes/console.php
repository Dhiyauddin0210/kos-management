<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Generate tagihan otomatis tiap tanggal 1 jam 00:00
Schedule::command('invoices:generate-monthly')
    ->monthlyOn(1, '00:00')
    ->timezone('Asia/Jakarta')
    ->onSuccess(function () {
        logger()->info('[Scheduler] Generate tagihan bulanan berhasil.');
    })
    ->onFailure(function () {
        logger()->error('[Scheduler] Generate tagihan bulanan GAGAL.');
    });
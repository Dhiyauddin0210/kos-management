<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankAccountController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name'      => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:100'],
            'is_primary'     => ['boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            if (! empty($validated['is_primary'])) {
                BankAccount::where('is_primary', true)->update(['is_primary' => false]);
            }

            if (BankAccount::count() === 0) {
                $validated['is_primary'] = true;
            }

            BankAccount::create($validated);
        });

        return redirect()->route('admin.settings.index', ['tab' => 'bank'])
            ->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'bank_name'      => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:100'],
            'is_primary'     => ['boolean'],
        ]);

        DB::transaction(function () use ($validated, $bankAccount) {
            if (! empty($validated['is_primary']) && ! $bankAccount->is_primary) {
                BankAccount::where('is_primary', true)->update(['is_primary' => false]);
            }

            $bankAccount->update($validated);
        });

        return redirect()->route('admin.settings.index', ['tab' => 'bank'])
            ->with('success', 'Rekening berhasil diperbarui.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $wasPrimary = $bankAccount->is_primary;

        $bankAccount->delete();

        if ($wasPrimary) {
            $next = BankAccount::first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        return redirect()->route('admin.settings.index', ['tab' => 'bank'])
            ->with('success', 'Rekening berhasil dihapus.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTopupWalletRequest;
use App\Http\Requests\StoreWithdrawWalletRequest;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function wallet(){
        $user = Auth::user();

        $wallet_transactions = WalletTransaction::where('user_id', $user->id)
        ->orderByDesc('id')
        ->paginate(10);

        return view('dashboard.wallet', compact('wallet_transactions'));

    }

    public function withdraw_wallet(){
        return view('dashboard.withdraw_wallet');
    }

    public function withdraw_wallet_store(StoreWithdrawWalletRequest $request){
        $user = Auth::user();

            // Periksa apakah user dan wallet tidak null
        if ($user === null || $user->wallet === null) {
            return redirect()->back()->withErrors([
                'amount' => 'User atau wallet tidak ditemukan'
            ]);
        }

        if($user->wallet->balance < 100000){
            return redirect()->back()->withErrors([
                'amount' => 'Balance anda tidak cukup'
            ]);
        }

        DB::transaction(function() use ($request, $user){
            $validated = $request->validated();
             
            if($request->hasFile('proof')){
                $proofPath = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }

            $validated['type'] = 'Withdraw';
            $validated['amount'] = $user->wallet->balance;
            $validated['is_paid'] = false;
            $validated['user_id'] = $user->id;

            $newTopupWallet = WalletTransaction::create($validated);

            $user->wallet->update([
                'balance' => 0
            ]);
        });

        return redirect()->route('dashboard.wallet');
    }
    

    public function topup_wallet()
    {
        return view('dashboard.topup_wallet');
    }

    public function topup_wallet_store(StoreTopupWalletRequest $request)
    {
        $user = Auth::user();
        DB::transaction(function() use ($request, $user){
            $validated = $request->validated();
            
            if($request->hasFile('proof')){
                $proofPath = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }

            $validated['type'] = 'Topup';
            $validated['is_paid'] = false;
            $validated['user_id'] = $user->id;

            $newTopupWallet = WalletTransaction::create($validated);
        });

        return redirect()->route('dashboard.wallet');

    }
}

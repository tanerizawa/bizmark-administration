<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\Kbli;
use App\Models\BankReconciliation;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'cash-accounts');
        $allowedTabs = ['cash-accounts', 'kbli', 'reconciliations'];
        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'cash-accounts';
        }
        
        // Get notifications/counts
        $notifications = $this->getNotifications();
        
        // Preload all tab data so switching never requires a refresh
        $cashAccountsData = $this->getCashAccountsData($request);
        $kbliData = $this->getKbliData($request);
        $reconciliationsData = $this->getReconciliationsData($request);
        
        // Get summary statistics
        $totalCashAccounts = CashAccount::count();
        $totalKbli = Kbli::count();
        $totalReconciliations = BankReconciliation::count();
        
        // Additional stats
        $activeCashAccounts = CashAccount::where('is_active', true)->count();
        $pendingReconciliations = BankReconciliation::where('status', 'pending')->count();
        $totalBalance = CashAccount::where('is_active', true)->sum('current_balance');
        
        return view('admin.master-data.index', array_merge(
            $cashAccountsData,
            $kbliData,
            $reconciliationsData,
            [
                'activeTab' => $activeTab,
                'notifications' => $notifications,
                'totalCashAccounts' => $totalCashAccounts,
                'totalKbli' => $totalKbli,
                'totalReconciliations' => $totalReconciliations,
                'activeCashAccounts' => $activeCashAccounts,
                'pendingReconciliations' => $pendingReconciliations,
                'totalBalance' => $totalBalance,
            ]
        ));
    }
    
    private function getNotifications()
    {
        return [
            'reconciliations' => BankReconciliation::where('status', 'pending')->count(),
            'cash_accounts' => CashAccount::where('is_active', false)->count(),
        ];
    }
    
    private function getCashAccountsData(Request $request)
    {
        $query = CashAccount::latest();
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%");
            });
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $cashAccounts = $query->paginate(20, ['*'], 'cash_accounts_page')->withQueryString();
        
        return compact('cashAccounts');
    }
    
    private function getKbliData(Request $request)
    {
        $query = Kbli::orderBy('code');
        
        if ($request->filled('category')) {
            $query->where('code', 'like', $request->category . '%');
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%");
            });
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $kbliData = $query->paginate(20, ['*'], 'kbli_page')->withQueryString();
        
        // Get categories (first character of code - A to U)
        $categories = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
        
        return compact('kbliData', 'categories');
    }
    
    private function getReconciliationsData(Request $request)
    {
        $query = BankReconciliation::with(['cashAccount'])->latest();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('cash_account_id')) {
            $query->where('cash_account_id', $request->cash_account_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('cashAccount', function($sq) use ($search) {
                      $sq->where('bank_name', 'like', "%{$search}%")
                         ->orWhere('account_number', 'like', "%{$search}%");
                  });
            });
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $reconciliations = $query->paginate(20, ['*'], 'reconciliations_page')->withQueryString();
        $cashAccountsList = CashAccount::where('is_active', true)->get();
        
        return compact('reconciliations', 'cashAccountsList');
    }
}

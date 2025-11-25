<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kbli;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        // Master Data now only handles KBLI
        // Cash Accounts and Reconciliations moved to /cash-accounts route
        
        // Get KBLI data
        $kbliData = $this->getKbliData($request);
        
        // Get summary statistics
        $totalKbli = Kbli::count();
        
        return view('admin.master-data.index', array_merge(
            $kbliData,
            [
                'totalKbli' => $totalKbli,
            ]
        ));
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
        
        // Dedicated pagination parameter
        $kbliData = $query->paginate(20, ['*'], 'kbli_page')->withQueryString();
        
        // Get categories (first character of code - A to U)
        $categories = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
        
        return compact('kbliData', 'categories');
    }
}

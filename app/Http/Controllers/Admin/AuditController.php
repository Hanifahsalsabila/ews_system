<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use OwenIt\Auditing\Models\Audit;

use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')->latest();

        if ($request->filled('category')) {
            $query->where('auditable_type', $request->category);
        }

        $audits = $query->paginate(15)->withQueryString();

        // Get unique categories for dropdown
        $categories = Audit::select('auditable_type')->distinct()->pluck('auditable_type');

        return view('admin.audits.index', compact('audits', 'categories'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index()
    {
        $audits = Audit::with('user')->latest()->paginate(15);
        return view('admin.audits.index', compact('audits'));
    }
}

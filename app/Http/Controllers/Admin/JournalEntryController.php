<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index()
    {
        $entries = JournalEntry::with(['lines.account', 'lines.entity'])->latest('entry_date')->latest('id')->paginate(20);
        return view('admin.accounting.journal_entries.index', compact('entries'));
    }
}

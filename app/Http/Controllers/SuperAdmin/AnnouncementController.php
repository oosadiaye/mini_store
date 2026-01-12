<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\SecureFileUploader;

class AnnouncementController extends Controller
{
    /**
     * @var SecureFileUploader
     */
    protected $uploader;

    public function __construct(SecureFileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('superadmin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenants = Tenant::all(['id', 'name']); 
        return view('superadmin.announcements.create', compact('tenants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:onboarding,announcement',
            'attachment' => 'nullable|file|mimes:jpeg,png,gif,mp4,webm|max:10240', // 10MB max
            'action_url' => 'nullable|url',
            'target_type' => 'required|in:all,selected',
            'tenants' => 'required_if:target_type,selected|array',
            'duration_val' => 'required|integer|min:1',
            'duration_unit' => 'required|in:hours,days,weeks,months',
        ]);

        $data = $request->only(['title', 'content', 'type', 'action_url', 'target_type']);
        
        // Handle Attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $mime = $file->getMimeType();
            
            if (str_contains($mime, 'video')) {
                $data['attachment_type'] = 'video';
            } elseif (str_contains($mime, 'image')) {
                $data['attachment_type'] = 'image';
            }
            
            $data['attachment_path'] = $this->uploader->upload($file, 'announcements', 'local');
        } else {
            $data['attachment_type'] = 'none';
        }

        // Calculate Dates
        $data['start_at'] = now();
        // Calculate end_at based on duration
        $durationMethod = 'add' . ucfirst($request->duration_unit); // e.g., addHours
        $data['end_at'] = now()->$durationMethod((int)$request->duration_val);

        $announcement = Announcement::create($data);

        // Sync Tenants
        if ($data['target_type'] === 'selected' && !empty($request->tenants)) {
            $announcement->tenants()->sync($request->tenants);
        }

        return redirect()->route('superadmin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        $tenants = Tenant::all();
        $selectedTenants = $announcement->tenants->pluck('id')->toArray();
        return view('superadmin.announcements.edit', compact('announcement', 'tenants', 'selectedTenants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
         $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:onboarding,announcement',
            'attachment' => 'nullable|file|mimes:jpeg,png,gif,mp4,webm|max:10240',
            'action_url' => 'nullable|url',
            // Allow changing target (be careful with sync)
            'target_type' => 'required|in:all,selected',
            'tenants' => 'required_if:target_type,selected|array',
        ]);

        $data = $request->only(['title', 'content', 'type', 'action_url', 'target_type']);

         // Handle Attachment
        if ($request->hasFile('attachment')) {
            // Delete old
            if ($announcement->attachment_path) {
                Storage::disk('local')->delete($announcement->attachment_path);
            }
            
            $file = $request->file('attachment');
            $mime = $file->getMimeType();
             if (str_contains($mime, 'video')) {
                $data['attachment_type'] = 'video';
            } elseif (str_contains($mime, 'image')) {
                $data['attachment_type'] = 'image';
            }
            $data['attachment_path'] = $this->uploader->upload($file, 'announcements', 'local');
        }

        $announcement->update($data);

         // Sync Tenants
        if ($data['target_type'] === 'selected') {
            $announcement->tenants()->sync($request->tenants);
        } else {
            $announcement->tenants()->detach(); // clear selected if 'all'
        }

        return redirect()->route('superadmin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        if ($announcement->attachment_path) {
            Storage::disk('local')->delete($announcement->attachment_path);
        }
        
        $announcement->delete();

        return redirect()->route('superadmin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}

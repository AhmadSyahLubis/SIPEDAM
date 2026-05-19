<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();
        
        $query = Report::with(['category', 'user', 'attachments', 'statusHistories.changedBy']);
        
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('ticket_number', 'like', "%{$request->search}%")
                  ->orWhere('title', 'like', "%{$request->search}%");
            });
        }

        $reports = $query->latest()->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $report = Report::create([
                'user_id' => auth('api')->id(),
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'status' => 'menunggu'
            ]);

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('attachments/reports', 'public');
                
                $report->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);
            }

            $report->statusHistories()->create([
                'status' => 'menunggu',
                'notes' => 'Laporan baru dibuat',
                'changed_by' => auth('api')->id()
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dibuat',
                'data' => $report->load('attachments')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        if (auth('api')->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:diproses,selesai,ditolak',
            'admin_notes' => 'nullable|string'
        ]);

        $report = Report::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $report->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes
            ]);

            $report->statusHistories()->create([
                'status' => $request->status,
                'notes' => $request->admin_notes ?? 'Status diperbarui oleh Admin',
                'changed_by' => auth('api')->id()
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Status laporan berhasil diperbarui',
                'data' => $report
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        
        if ($report->user_id !== auth('api')->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($report->status !== 'menunggu') {
            return response()->json(['success' => false, 'message' => 'Tidak dapat membatalkan laporan yang sudah diproses'], 400);
        }

        $report->delete(); // Status histories and attachments cascade on delete from DB level/Logic

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dibatalkan'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceRequestController extends Controller
{
    // Get all services (Admin) or user's services (User)
    public function index(Request $request)
    {
        $user = auth('api')->user();
        
        $query = Service::with(['category', 'user', 'attachments', 'statusHistories.changedBy']);
        
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('ticket_number', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $services = $query->latest()->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    // Create new service request
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120' // Max 5MB
        ]);

        DB::beginTransaction();
        try {
            $service = Service::create([
                'user_id' => auth('api')->id(),
                'category_id' => $request->category_id,
                'description' => $request->description,
                'status' => 'menunggu'
            ]);

            // Handle required attachment
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('attachments/services', 'public');
                
                $service->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);
            }

            // Create Status History
            $service->statusHistories()->create([
                'status' => 'menunggu',
                'notes' => 'Permohonan layanan baru diajukan',
                'changed_by' => auth('api')->id()
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Permohonan layanan berhasil diajukan',
                'data' => $service->load('attachments')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses permohonan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update status (Admin only)
    public function updateStatus(Request $request, $id)
    {
        if (auth('api')->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:diproses,selesai,ditolak',
            'admin_notes' => 'nullable|string'
        ]);

        $service = Service::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $service->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes
            ]);

            $service->statusHistories()->create([
                'status' => $request->status,
                'notes' => $request->admin_notes ?? 'Status diperbarui oleh Admin',
                'changed_by' => auth('api')->id()
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Status permohonan berhasil diperbarui',
                'data' => $service
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
        $service = Service::findOrFail($id);
        
        if ($service->user_id !== auth('api')->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($service->status !== 'menunggu') {
            return response()->json(['success' => false, 'message' => 'Tidak dapat membatalkan permohonan yang sudah diproses'], 400);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permohonan berhasil dibatalkan'
        ]);
    }
}

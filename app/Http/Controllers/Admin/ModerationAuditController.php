<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModerationAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ModerationAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('can:viewAny,App\Models\ModerationAction');
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', ModerationAction::class);

        $query = ModerationAction::with(['moderator', 'moderatable'])
            ->latest();

        if ($moderatorId = $request->input('moderator')) {
            $query->where('moderator_id', $moderatorId);
        }

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        $actions = $query->paginate(50);

        // Statystyki per moderator
        $stats = ModerationAction::select('moderator_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN action = "approve" THEN 1 ELSE 0 END) as approvals')
            ->selectRaw('SUM(CASE WHEN action = "reject" THEN 1 ELSE 0 END) as rejections')
            ->selectRaw('SUM(CASE WHEN action = "escalate" THEN 1 ELSE 0 END) as escalations')
            ->groupBy('moderator_id')
            ->with('moderator')
            ->get();

        return view('admin.moderation.audit', compact('actions', 'stats'));
    }
}

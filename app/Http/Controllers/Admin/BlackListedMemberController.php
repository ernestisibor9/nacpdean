<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlacklistedMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlacklistedMemberController extends Controller
{
    /**
     * Public upload directory (absolute path).
     */
    private function uploadPath(): string
    {
        return public_path('uploads/blacklist');
    }


    /**
     * List all blacklisted members (active + lifted).
     */
    public function index(): View
    {
        $blacklistedMembers = BlacklistedMember::query()
            ->orderByRaw("
                CASE
                    WHEN status = 'blacklisted' THEN 1
                    WHEN status = 'lifted' THEN 2
                    ELSE 3
                END
            ")
            ->orderByDesc('effective_date')
            ->get();

        return view(
            'admin.blacklist.index',
            compact('blacklistedMembers')
        );
    }


    /**
     * Show the "add to blacklist" form, optionally pre-filled
     * from an existing member.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $prefill = [
            'member_id'         => null,
            'member_name'       => null,
            'company_name'      => null,
            'membership_number' => null,
            'state'             => null,
            'photo'             => null,
        ];

        if ($request->filled('member_id')) {

            $member = User::with(['profile'])->find($request->member_id);

            if (!$member || $member->role !== 'member') {
                return redirect()
                    ->route('admin.blacklist.index')
                    ->with('error', 'Invalid member selected.');
            }

            $profile = $member->profile;

            $prefill = [
                'member_id'         => $member->id,
                'member_name'       => trim(
                    ($profile->first_name ?? '') . ' ' .
                    ($profile->middle_name ?? '') . ' ' .
                    ($profile->surname ?? '')
                ) ?: ($member->username ?? 'Member'),
                'company_name'      => $profile->business_name ?? null,
                'membership_number' => $profile->membership_number ?? null,
                'state'             => $profile->state ?? null,
                'photo'             => $profile->photo ?? null,
            ];
        }

        return view(
            'admin.blacklist.create',
            compact('prefill')
        );
    }


    /**
     * Store a new blacklist entry.
     */
    public function store(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATE
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'member_id'          => ['nullable', 'integer', 'exists:users,id'],
            'member_name'        => ['required', 'string', 'max:255'],
            'company_name'       => ['nullable', 'string', 'max:255'],
            'membership_number'  => ['nullable', 'string', 'max:255'],
            'state'              => ['nullable', 'string', 'max:255'],
            'effective_date'     => ['required', 'date'],
            'blacklisted_until'  => ['nullable', 'date', 'after_or_equal:effective_date'],
            'reason'             => ['required', 'string', 'max:2000'],
            'photo'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | DUPLICATE CHECK
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['membership_number'])) {
            $exists = BlacklistedMember::where('membership_number', $validated['membership_number'])
                ->where('status', 'blacklisted')
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->with('error', 'This membership number is already blacklisted.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PHOTO UPLOAD
        |--------------------------------------------------------------------------
        |
        | Stored in: public/uploads/blacklist/
        | DB stores:  bare filename (e.g. "blk_12_a1b2c3.jpg")
        |
        */

        if ($request->hasFile('photo')) {

            $uploadPath = $this->uploadPath();

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file      = $request->file('photo');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename  = 'blk_' . ($validated['member_id'] ?? 'x') . '_' . Str::random(16) . '.' . $extension;

            $file->move($uploadPath, $filename);

            $validated['photo'] = $filename;
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        $validated['status'] = 'blacklisted';

        BlacklistedMember::create($validated);

        return redirect()
            ->route('admin.blacklist.index')
            ->with('success', 'Member added to the blacklist successfully.');
    }


    /**
     * Lift a blacklist entry (unblacklist).
     */
    public function lift(int $id): RedirectResponse
    {
        $entry = BlacklistedMember::findOrFail($id);

        if ($entry->status === 'lifted') {
            return back()->with('error', 'This entry has already been lifted.');
        }

        $entry->update(['status' => 'lifted']);

        return redirect()
            ->route('admin.blacklist.index')
            ->with('success', 'Member has been removed from the blacklist.');
    }


    /**
     * Permanently delete a blacklist entry.
     */
    public function destroy(int $id): RedirectResponse
    {
        $entry = BlacklistedMember::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | DELETE PHOTO FROM DISK
        |--------------------------------------------------------------------------
        */

        if ($entry->photo) {
            $file = $this->uploadPath() . DIRECTORY_SEPARATOR . $entry->photo;
            if (is_file($file)) {
                @unlink($file);
            }
        }

        $entry->delete();

        return redirect()
            ->route('admin.blacklist.index')
            ->with('success', 'Blacklist entry deleted successfully.');
    }
}
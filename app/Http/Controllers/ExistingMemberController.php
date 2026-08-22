<?php

namespace App\Http\Controllers;

use App\Models\ExistingMember;
use Illuminate\Http\Request;

class ExistingMemberController extends Controller
{
    /**
     * Verify an existing NACPDEAN exporter.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'membership_number' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $membershipNumber = strtoupper(
            trim($request->membership_number)
        );

        $member = ExistingMember::where(
            'membership_number',
            $membershipNumber
        )
        ->where('category', 'Exporter')
        ->where('active', true)
        ->first();

        if (!$member) {
            return response()->json([
                'status' => false,
                'message' => 'Existing exporter membership could not be verified.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Existing exporter membership verified successfully.',
            'member' => [
                'membership_number' => $member->membership_number,
                'full_name' => $member->full_name,
                'category' => $member->category,
                'state_code' => $member->state_code,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MemberProfile;
use Illuminate\Http\Request;

class PublicMemberController extends Controller
{
    /**
     * Display approved Exporter members.
     */
    public function exporters(Request $request)
    {
        $search = trim($request->query('search', ''));

        $exporters = MemberProfile::with('membershipCategory')
            ->where('status', 'approved')
            ->whereHas('membershipCategory', function ($query) {
                $query->where('code', 'EXP')
                    ->where('status', 1);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('membership_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('surname')
            ->orderBy('first_name')
            ->paginate(9)
            ->withQueryString();

        return view('frontend.exporters', compact('exporters'));
    }


        public function producers(Request $request)
    {
        $search = trim($request->query('search', ''));

        $producers = MemberProfile::with('membershipCategory')
            ->where('status', 'approved')
            ->whereHas('membershipCategory', function ($query) {
                $query->where('code', 'PRD')
                    ->where('status', 1);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('membership_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('surname')
            ->orderBy('first_name')
            ->paginate(9)
            ->withQueryString();

        return view('frontend.producers', compact('producers'));
    }



        public function dealers(Request $request)
    {
        $search = trim($request->query('search', ''));

        $dealers = MemberProfile::with('membershipCategory')
            ->where('status', 'approved')
            ->whereHas('membershipCategory', function ($query) {
                $query->where('code', 'DEA')
                    ->where('status', 1);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('membership_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('surname')
            ->orderBy('first_name')
            ->paginate(9)
            ->withQueryString();

        return view('frontend.dealers', compact('dealers'));
    }



        public function suppliers(Request $request)
    {
        $search = trim($request->query('search', ''));

        $suppliers = MemberProfile::with('membershipCategory')
            ->where('status', 'approved')
            ->whereHas('membershipCategory', function ($query) {
                $query->where('code', 'SLR')
                    ->where('status', 1);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('membership_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('surname')
            ->orderBy('first_name')
            ->paginate(9)
            ->withQueryString();

        return view('frontend.suppliers', compact('suppliers'));
    }


}

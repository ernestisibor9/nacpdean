<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlacklistedMember;
use App\Models\MembershipOfficerAppointment;
use App\Models\HomeVideo;

class HomeController extends Controller
{
    //
    public function Index()
    {
        $videos = HomeVideo::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $latestVideo = HomeVideo::where('is_active', true)
            ->orderByDesc('id')
            ->first();
        return view('frontend.index', compact('videos', 'latestVideo'));
    }

    public function About()
    {
        return view('frontend.about');
    }

    public function Bot()
    {
        return view('frontend.bot');
    }

    public function History()
    {
        return view('frontend.history');
    }

    public function Association()
    {
        return view('frontend.association');
    }

    public function Partnership()
    {
        return view('frontend.partnership');
    }

    public function  Compliance()
    {
        return view('frontend.compliance');
    }

    public function  Contact()
    {
        return view('frontend.contact');
    }


    public function StateExecutive()
    {
        $executives = MembershipOfficerAppointment::with([
            'membership.profile',
            'membership.user',
            'membership.membershipCategory',
        ])
            ->where('appointment_type', 'task_force')
            ->where('level', 'state')
            ->where('status', 'approved')
            ->whereHas('membership', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('state')
            ->orderBy('position')
            ->get();

        return view(
            'frontend.state-executive',
            compact('executives')
        );
    }

    public function BlackListed()
    {
        $blacklistedMembers = BlacklistedMember::orderBy('effective_date', 'desc')
            ->orderBy('member_name', 'asc')
            ->get();

        return view('frontend.blacklisted-members', compact('blacklistedMembers'));
    }

    public function NationalExecutive()
    {
        $executives = MembershipOfficerAppointment::with([
            'membership.profile',
            'membership.user',
            'membership.membershipCategory',
        ])
            ->where('appointment_type', 'national_executive')
            ->where('status', 'approved')
            ->whereHas('membership', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('executive_id')
            ->get();

        return view(
            'frontend.national-executive',
            compact('executives')
        );
    }
}

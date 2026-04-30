@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<style>
    /* Dashboard contrast overrides – fix low-opacity inline styles */
    .dashboard-root [style*="rgba(235,235,245,0.5)"] { color: rgba(235,235,245,0.72) !important; }
    .dashboard-root [style*="rgba(235,235,245,0.65)"] { color: rgba(235,235,245,0.82) !important; }
    .dashboard-root [style*="color: rgba(255,255,255,0.3)"] { color: rgba(255,255,255,0.52) !important; }
    .dashboard-root [style*="color: rgba(255,255,255,0.4)"] { color: rgba(255,255,255,0.62) !important; }
    .dashboard-root [style*="color: rgba(255,255,255,0.5)"] { color: rgba(255,255,255,0.72) !important; }
    .dashboard-root [style*="color: rgba(255,255,255,0.6)"] { color: rgba(255,255,255,0.8) !important; }
    .dashboard-root [style*="color: rgba(142,142,147,0.6)"] { color: rgba(210,210,215,0.9) !important; }
</style>
<div class="dashboard-root space-y-4">
    @include('admin.dashboard.hero')
    @include('admin.dashboard.kpi-cards')
    @include('admin.dashboard.critical-focus')
    @include('admin.dashboard.financial-intelligence')
    @include('admin.dashboard.operational-monitoring')
</div>
@endsection

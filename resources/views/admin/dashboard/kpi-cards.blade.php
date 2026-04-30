    {{-- Compact KPI Cards --}}
    <div class="grid grid-cols-4 gap-3">
        {{-- Urgent Actions --}}
        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-3 bg-apple-red/10 border-apple-red/20">
            <div class="admin-stat-icon rounded flex items-center justify-center bg-apple-red/20">
                <i class="fas fa-exclamation-triangle text-apple-red" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-red uppercase tracking-wider">Urgent</p>
                {{-- FIX (BUG-07): Null coalescing to prevent undefined array key errors --}}
                <p class="admin-stat text-white">{{ $criticalAlerts['total_urgent'] ?? 0 }}</p>
            </div>
        </article>
        
        {{-- Cash Runway --}}
        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-3 bg-apple-blue/10 border-apple-blue/20">
            <div class="admin-stat-icon rounded flex items-center justify-center bg-apple-blue/20">
                <i class="fas fa-wallet text-apple-blue" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-blue uppercase tracking-wider">Kas</p>
                {{-- FIX (BUG-07): Null coalescing --}}
                <p class="admin-stat text-apple-blue">{{ ($cashFlowStatus['runway_months'] ?? 0) }} bln</p>
            </div>
        </article>
        
        {{-- Pending Approvals --}}
        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-3 bg-apple-orange/10 border-apple-orange/20">
            <div class="admin-stat-icon rounded flex items-center justify-center bg-apple-orange/20">
                <i class="fas fa-clock text-apple-orange" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-orange uppercase tracking-wider">Pending</p>
                {{-- FIX (BUG-07): Null coalescing --}}
                <p class="admin-stat text-white">{{ $pendingApprovals['total_pending'] ?? 0 }}</p>
            </div>
        </article>
        
        {{-- Upcoming Tasks --}}
        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-3 bg-apple-green/10 border-apple-green/20">
            <div class="admin-stat-icon rounded flex items-center justify-center bg-apple-green/20">
                <i class="fas fa-calendar-check text-apple-green" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-green uppercase tracking-wider">30 Hari</p>
                {{-- FIX (BUG-07): Null coalescing --}}
                <p class="admin-stat text-apple-green">{{ $thisWeek['total_items'] ?? 0 }}</p>
            </div>
        </article>
    </div>


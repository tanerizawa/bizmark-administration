# 📋 DASHBOARD REDESIGN - IMPLEMENTATION TODO

## 🎯 PHASE 1: CRITICAL ALERTS (Priority: HIGHEST)

### Backend Tasks

#### [ ] 1. Enhance DashboardController
```php
Location: app/Http/Controllers/DashboardController.php

Add new methods:
- [ ] getCriticalAlerts() - Overdue items & due today
- [ ] getCashFlowStatus() - Balance, burn rate, runway
- [ ] getPendingActions() - Documents, approvals

New queries needed:
- [ ] Overdue projects with days_overdue calculation
- [ ] Overdue tasks with assignee name
- [ ] Tasks & projects due today
- [ ] Monthly expense average (last 3 months)
- [ ] Overdue invoices sum from Invoice model
- [ ] Documents with status='review' + age
```

#### [ ] 2. Create Helper Methods
```php
Location: app/Http/Controllers/DashboardController.php

- [ ] calculateDaysOverdue($date) - Returns integer
- [ ] calculateBurnRate() - Returns monthly average
- [ ] calculateCashRunway($balance, $burnRate) - Returns months
- [ ] getReceivablesAging() - Returns array by age buckets
```

#### [ ] 3. Update index() Method
```php
Replace current dashboard data with:

$criticalAlerts = $this->getCriticalAlerts();
$cashFlowStatus = $this->getCashFlowStatus();
$pendingActions = $this->getPendingActions();

return view('dashboard', compact(
    'criticalAlerts',
    'cashFlowStatus', 
    'pendingActions'
));
```

---

### Frontend Tasks

#### [ ] 4. Backup Current Dashboard
```bash
cp resources/views/dashboard.blade.php resources/views/dashboard-backup-phase0.blade.php
```

#### [ ] 5. Create Blade Components (Optional but recommended)
```
resources/views/components/dashboard/
├── critical-alert.blade.php
├── cash-flow-alert.blade.php
├── pending-actions.blade.php
└── alert-item.blade.php
```

#### [ ] 6. Redesign Dashboard Layout
```blade
Structure:
- Header: "Dashboard - Critical Overview"
- Alert Banner: If any critical issues
- 3 Main Cards (stacked on mobile, side-by-side on desktop)
  1. Urgent Actions
  2. Cash Flow Status
  3. Pending Approvals
```

#### [ ] 7. Build Card 1: Urgent Actions
```blade
Features:
- [ ] Section title: "🔴 Urgent Actions"
- [ ] Count badge (red if > 0)
- [ ] List overdue projects
  - [ ] Project name (truncated)
  - [ ] Days overdue (e.g., "3 days overdue")
  - [ ] Link to project detail
- [ ] List overdue tasks
  - [ ] Task title
  - [ ] Assigned to: User name
  - [ ] Link to task detail
- [ ] List due today
  - [ ] Combined projects + tasks
  - [ ] "Due today" badge (orange)
- [ ] Empty state: "✅ No urgent items"
```

#### [ ] 8. Build Card 2: Cash Flow Status
```blade
Features:
- [ ] Current balance (large number)
- [ ] Color coded:
  - Red if runway < 2 months
  - Yellow if runway < 6 months
  - Green if runway >= 6 months
- [ ] Monthly burn rate
- [ ] Runway: "X months of runway"
- [ ] Overdue invoices total
  - [ ] "Rp X.XM overdue" (red text)
  - [ ] Click → Invoice page filtered
- [ ] Quick action: "Collect Payments" button
```

#### [ ] 9. Build Card 3: Pending Approvals
```blade
Features:
- [ ] Count of pending documents
- [ ] Oldest pending item (with age in days)
- [ ] List view (max 5 items):
  - [ ] Document name
  - [ ] Submitted by
  - [ ] Days waiting
  - [ ] Approve/Reject buttons (inline)
- [ ] Link: "View all pending" → Documents page
- [ ] Empty state: "✅ All caught up!"
```

---

### Testing Tasks

#### [ ] 10. Unit Tests
```
tests/Unit/DashboardControllerTest.php

Test cases:
- [ ] getCriticalAlerts() returns correct structure
- [ ] Days overdue calculation is accurate
- [ ] Burn rate calculation is correct
- [ ] Cash runway handles zero burn rate
- [ ] Receivables aging groups correctly
```

#### [ ] 11. Integration Tests
```
tests/Feature/DashboardTest.php

Test cases:
- [ ] Dashboard loads without errors
- [ ] Shows overdue items if exist
- [ ] Shows empty state if no alerts
- [ ] Links to correct detail pages
- [ ] Mobile responsive (visual test)
```

#### [ ] 12. Manual Testing
```
Test scenarios:
- [ ] Create overdue project, verify appears
- [ ] Create overdue task, verify appears  
- [ ] Complete overdue task, verify disappears
- [ ] Create invoice, verify in receivables
- [ ] Upload document, verify in pending
- [ ] Approve document, verify disappears
- [ ] Test with 0 data (empty state)
- [ ] Test with 100+ items (performance)
```

---

### Deployment Tasks

#### [ ] 13. Code Review
- [ ] Self-review all changes
- [ ] Check for N+1 queries
- [ ] Verify no hardcoded values
- [ ] Ensure proper eager loading
- [ ] Check mobile responsive CSS

#### [ ] 14. Database Checks
```sql
-- Ensure data quality
- [ ] All projects have deadline
- [ ] All tasks have due_date
- [ ] All invoices have status
- [ ] No null values in critical fields
```

#### [ ] 15. Performance Optimization
- [ ] Add database indexes if needed:
  ```sql
  - projects.deadline
  - tasks.due_date
  - invoices.due_date
  - documents.status
  ```
- [ ] Cache dashboard data (5 min TTL)
- [ ] Optimize queries (use EXPLAIN)

#### [ ] 16. Deploy to Staging
```bash
git checkout -b feature/dashboard-phase1
git add .
git commit -m "feat: Phase 1 - Critical Alerts Dashboard"
git push origin feature/dashboard-phase1
# Create PR for review
```

#### [ ] 17. User Acceptance Testing
- [ ] Show to 2-3 project managers
- [ ] Ask for feedback:
  - Is critical info visible?
  - Is it actionable?
  - Any missing info?
- [ ] Document feedback
- [ ] Iterate if needed

#### [ ] 18. Deploy to Production
```bash
# After approval
git checkout main
git merge feature/dashboard-phase1
git push origin main
php artisan migrate --force (if any migrations)
php artisan cache:clear
php artisan view:clear
```

---

## 🎯 PHASE 2: FINANCIAL DASHBOARD (Priority: HIGH)

### Backend Tasks

#### [ ] 19. Add Financial Methods
```php
Location: app/Http/Controllers/DashboardController.php

- [ ] getCashFlowSummary() - In/Out/Net this month + last month
- [ ] getReceivablesAging() - Group invoices by 0-30, 31-60, 61-90, 90+ days
- [ ] getBudgetStatus() - Top 5 projects budget variance
```

### Frontend Tasks

#### [ ] 20. Build Card 4: Cash Flow Summary
```blade
- [ ] This month: In / Out / Net
- [ ] Last month comparison (%)
- [ ] Simple horizontal bar chart
- [ ] Color: Green if positive, Red if negative
```

#### [ ] 21. Build Card 5: Receivables Aging
```blade
- [ ] 4 buckets: 0-30, 31-60, 61-90, 90+ days
- [ ] Amount per bucket
- [ ] Color code: Green → Yellow → Orange → Red
- [ ] Click → Filter invoices by age
```

#### [ ] 22. Build Card 6: Budget Status
```blade
- [ ] Top 5 projects by budget variance
- [ ] Horizontal progress bar per project
- [ ] Show: Budget / Actual / Variance %
- [ ] Color: Red if over, Yellow if >80%, Green if ok
```

#### [ ] 23. Test & Deploy Phase 2
- [ ] Same testing process as Phase 1
- [ ] Deploy after approval

---

## 🎯 PHASE 3: OPERATIONAL INSIGHTS (Priority: MEDIUM)

### Backend Tasks

#### [ ] 24. Add Operational Methods
```php
- [ ] getWeekTimeline() - Tasks/projects for next 7 days
- [ ] getTeamPerformance() - Completed tasks per user this week
- [ ] getRecentActivities() - Last 10 significant events
```

### Frontend Tasks

#### [ ] 25. Build Card 7: This Week Timeline
```blade
- [ ] Visual 7-day calendar
- [ ] Dots for tasks/projects per day
- [ ] Color by priority
- [ ] Hover tooltip with details
```

#### [ ] 26. Build Card 8: Team Performance
```blade
- [ ] Tasks completed this week per user
- [ ] On-time delivery rate
- [ ] Leaderboard style
```

#### [ ] 27. Build Card 9: Recent Activities
```blade
- [ ] Last 10 events
- [ ] Status changes, payments, approvals
- [ ] Timestamp + user + action
```

#### [ ] 28. Test & Deploy Phase 3

---

## 🎯 PHASE 4: POLISH & OPTIMIZATION (Priority: LOW)

#### [ ] 29. Performance Optimization
- [ ] Implement caching strategy
- [ ] Add lazy loading for charts
- [ ] Optimize images/icons
- [ ] Minify CSS/JS

#### [ ] 30. Accessibility
- [ ] Proper ARIA labels
- [ ] Keyboard navigation
- [ ] Screen reader testing
- [ ] Color contrast check

#### [ ] 31. Documentation
- [ ] Update README with dashboard info
- [ ] Add inline code comments
- [ ] Create user guide
- [ ] Record demo video

#### [ ] 32. Analytics
- [ ] Track dashboard views
- [ ] Track card clicks
- [ ] Measure engagement
- [ ] A/B test variations

---

## 📊 PROGRESS TRACKING

### Phase 1 Status: ⏳ Pending
- Start Date: _______
- Completion: 0%
- Blockers: None

### Phase 2 Status: ⏳ Not Started
- Start Date: _______
- Completion: 0%
- Blockers: Phase 1

### Phase 3 Status: ⏳ Not Started
- Start Date: _______
- Completion: 0%
- Blockers: Phase 2

---

## 🚨 CRITICAL NOTES

### Before Starting:
1. ✅ Create backup of current dashboard
2. ✅ Create feature branch
3. ✅ Review database schema
4. ✅ Check available routes for linking

### During Development:
1. 🔄 Test incrementally (don't wait till end)
2. 🔄 Commit frequently with clear messages
3. 🔄 Document any assumptions or decisions
4. 🔄 Ask for feedback early and often

### After Completion:
1. ✅ Update this TODO with actual completion dates
2. ✅ Document any deviations from plan
3. ✅ Collect user feedback
4. ✅ Plan Phase 2 based on learnings

---

**Last Updated:** October 4, 2025  
**Status:** Ready to Start  
**Next Action:** Begin Phase 1 Backend Tasks

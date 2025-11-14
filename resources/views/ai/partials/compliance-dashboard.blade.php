<!-- Compliance Dashboard Component -->
<div x-data="complianceDashboard()" x-init="init()" class="compliance-dashboard">
    <!-- Loading State -->
    <div x-show="loading" class="text-center py-4">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
        <p class="mt-2 text-sm text-gray-600">Checking compliance...</p>
    </div>

    <!-- Compliance Results -->
    <div x-show="!loading && hasCheck && check !== null" class="space-y-4">
        <!-- Overall Score Card -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Compliance Score</h3>
                    <p class="text-sm text-gray-600" x-text="check?.summary || ''"></p>
                </div>
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center w-24 h-24">
                        <svg class="w-24 h-24 transform -rotate-90">
                            <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-gray-200"></circle>
                            <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" 
                                    :class="getScoreColor(check?.overall_score || 0)"
                                    :stroke-dasharray="251.2"
                                    :stroke-dashoffset="251.2 - (251.2 * (check?.overall_score || 0) / 100)"
                                    class="transition-all duration-1000 ease-out"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div>
                                <div class="text-3xl font-bold" :style="{color: check?.status_color || '#000'}" x-text="Math.round(check?.overall_score || 0)"></div>
                                <div class="text-xs text-gray-500">/ 100</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                              :style="{backgroundColor: (check?.status_color || '#000') + '20', color: check?.status_color || '#000'}"
                              x-text="check?.status_label || ''"></span>
                    </div>
                </div>
            </div>

            <!-- Score Breakdown -->
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600">Structure</div>
                    <div class="text-2xl font-bold mt-1" x-text="Math.round(check?.structure_score || 0)"></div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600">Compliance</div>
                    <div class="text-2xl font-bold mt-1" x-text="Math.round(check?.compliance_score || 0)"></div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600">Formatting</div>
                    <div class="text-2xl font-bold mt-1" x-text="Math.round(check?.formatting_score || 0)"></div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600">Completeness</div>
                    <div class="text-2xl font-bold mt-1" x-text="Math.round(check?.completeness_score || 0)"></div>
                </div>
            </div>
        </div>

        <!-- Issues Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Issues Found</h3>
                <div class="flex items-center space-x-4 text-sm">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                        <span x-text="(check?.critical_issues || 0) + ' Critical'"></span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                        <span x-text="(check?.warning_issues || 0) + ' Warning'"></span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                        <span x-text="(check?.info_issues || 0) + ' Info'"></span>
                    </div>
                </div>
            </div>

            <!-- Issues by Category -->
            <div class="space-y-4">
                <template x-for="(issues, category) in (check?.issues_by_category || {})" :key="category">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button @click="toggleCategory(category)" 
                                class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas mr-3" :class="getCategoryIcon(category)"></i>
                                <span class="font-medium capitalize" x-text="category"></span>
                                <span class="ml-2 text-sm text-gray-500" x-text="'(' + issues.length + ')'"></span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform" :class="{'rotate-180': expandedCategories.includes(category)}"></i>
                        </button>
                        
                        <div x-show="expandedCategories.includes(category)" 
                             x-transition
                             class="p-4 space-y-3">
                            <template x-for="(issue, index) in issues" :key="index">
                                <div class="flex items-start space-x-3 p-3 rounded-lg"
                                     :class="getSeverityClass(issue.severity)">
                                    <div class="flex-shrink-0 mt-1">
                                        <i class="fas" :class="getSeverityIcon(issue.severity)"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900" x-text="issue.message"></p>
                                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full"
                                                  :class="getSeverityBadgeClass(issue.severity)"
                                                  x-text="issue.severity"></span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600" x-text="issue.location"></p>
                                        <div class="mt-2 flex items-start space-x-2 p-2 bg-blue-50 rounded">
                                            <i class="fas fa-lightbulb text-blue-600 mt-0.5"></i>
                                            <p class="text-sm text-blue-800" x-text="issue.suggestion"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Last checked: <span x-text="check?.checked_at || ''"></span>
            </div>
            <div class="flex space-x-2">
                <a :href="'{{ route('ai.drafts.compliance-report', [$project, $draft]) }}'" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-download mr-2"></i>
                    Export Report
                </a>
                <button @click="runCheck()" 
                        :disabled="checking"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-sync-alt mr-2" :class="{'fa-spin': checking}"></i>
                    <span x-text="checking ? 'Checking...' : 'Re-check Compliance'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- No Check State -->
    <div x-show="!loading && !hasCheck" class="text-center py-8">
        <i class="fas fa-clipboard-check text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Compliance Check Yet</h3>
        <p class="text-gray-600 mb-4">Run a compliance check to validate your document against UKL-UPL standards</p>
        <button @click="runCheck()" 
                :disabled="checking"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
            <i class="fas fa-play mr-2"></i>
            Run Compliance Check
        </button>
    </div>
</div>

<script>
function complianceDashboard() {
    return {
        loading: true,
        checking: false,
        hasCheck: false,
        check: null,
        expandedCategories: [],
        
        init() {
            this.loadResults();
        },
        
        async loadResults() {
            this.loading = true;
            try {
                const response = await fetch('{{ route("ai.drafts.compliance-results", [$project, $draft]) }}');
                const data = await response.json();
                
                if (data.success && data.has_check) {
                    this.hasCheck = true;
                    this.check = data.check;
                    
                    // Auto-expand critical issues
                    if (this.check.critical_issues > 0) {
                        Object.keys(this.check.issues_by_category).forEach(category => {
                            const hasCritical = this.check.issues_by_category[category].some(
                                issue => issue.severity === 'critical'
                            );
                            if (hasCritical && !this.expandedCategories.includes(category)) {
                                this.expandedCategories.push(category);
                            }
                        });
                    }
                } else {
                    this.hasCheck = false;
                }
            } catch (error) {
                console.error('Failed to load compliance results:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async runCheck() {
            this.checking = true;
            try {
                const response = await fetch('{{ route("ai.drafts.check-compliance", [$project, $draft]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Poll for results
                    this.pollResults();
                } else {
                    alert(data.message || 'Failed to start compliance check');
                }
            } catch (error) {
                console.error('Failed to run compliance check:', error);
                alert('Failed to start compliance check');
            } finally {
                this.checking = false;
            }
        },
        
        pollResults() {
            const interval = setInterval(async () => {
                await this.loadResults();
                
                if (this.hasCheck && this.check.status === 'completed') {
                    clearInterval(interval);
                }
            }, 2000);
            
            // Stop polling after 30 seconds
            setTimeout(() => clearInterval(interval), 30000);
        },
        
        toggleCategory(category) {
            const index = this.expandedCategories.indexOf(category);
            if (index > -1) {
                this.expandedCategories.splice(index, 1);
            } else {
                this.expandedCategories.push(category);
            }
        },
        
        getScoreColor(score) {
            if (score >= 80) return 'text-green-500';
            if (score >= 70) return 'text-yellow-500';
            return 'text-red-500';
        },
        
        getCategoryIcon(category) {
            const icons = {
                structure: 'fa-sitemap',
                compliance: 'fa-balance-scale',
                formatting: 'fa-align-left',
                completeness: 'fa-check-circle',
            };
            return icons[category] || 'fa-exclamation-circle';
        },
        
        getSeverityIcon(severity) {
            const icons = {
                critical: 'fa-times-circle text-red-600',
                warning: 'fa-exclamation-triangle text-yellow-600',
                info: 'fa-info-circle text-blue-600',
            };
            return icons[severity] || 'fa-info-circle';
        },
        
        getSeverityClass(severity) {
            const classes = {
                critical: 'bg-red-50 border-l-4 border-red-500',
                warning: 'bg-yellow-50 border-l-4 border-yellow-500',
                info: 'bg-blue-50 border-l-4 border-blue-500',
            };
            return classes[severity] || 'bg-gray-50';
        },
        
        getSeverityBadgeClass(severity) {
            const classes = {
                critical: 'bg-red-100 text-red-800',
                warning: 'bg-yellow-100 text-yellow-800',
                info: 'bg-blue-100 text-blue-800',
            };
            return classes[severity] || 'bg-gray-100 text-gray-800';
        },
    }
}
</script>

<style>
.compliance-dashboard {
    min-height: 400px;
}

.rotate-180 {
    transform: rotate(180deg);
}
</style>

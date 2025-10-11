{{-- Roles & Permissions Tab --}}
<div>
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold" style="color: #FFFFFF;">Roles & Permissions</h3>
            <p class="text-sm mt-1" style="color: rgba(235, 235, 245, 0.6);">
                Manage user roles and their permissions
            </p>
        </div>
        <button onclick="openRoleModal()" class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
            <i class="fas fa-plus text-xs mr-2"></i>
            Add Role
        </button>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($roles as $role)
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <h4 class="text-base font-semibold flex items-center" style="color: #FFFFFF;">
                        <i class="fas fa-user-shield mr-2" style="color: rgba(0, 122, 255, 0.8);"></i>
                        {{ $role->display_name }}
                        @if($role->is_system)
                            <span class="ml-2 px-2 py-0.5 text-xs rounded-apple" style="background: rgba(255, 149, 0, 0.15); color: rgba(255, 149, 0, 1);">
                                System
                            </span>
                        @endif
                    </h4>
                    <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">
                        {{ $role->description }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between py-2 border-t border-b my-3" style="border-color: rgba(255, 255, 255, 0.1);">
                <div class="text-center flex-1">
                    <div class="text-lg font-bold" style="color: #FFFFFF;">{{ $role->users_count }}</div>
                    <div class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Users</div>
                </div>
                <div class="text-center flex-1">
                    <div class="text-lg font-bold" style="color: rgba(0, 122, 255, 1);">{{ $role->permissions->count() }}</div>
                    <div class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Permissions</div>
                </div>
            </div>

            <div class="flex space-x-2">
                <button onclick="editRole({{ $role->id }})" 
                        class="flex-1 px-3 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                        style="background: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 1);">
                    <i class="fas fa-pencil mr-1"></i> Edit
                </button>
                @if(!$role->is_system)
                    <form action="{{ route('settings.roles.delete', $role) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('Are you sure? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full px-3 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                                style="background: rgba(255, 59, 48, 0.15); color: rgba(255, 59, 48, 1);">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Role Modal -->
<div id="roleModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background: rgba(0, 0, 0, 0.7);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="card-elevated rounded-apple-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <form id="roleForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="roleMethod" value="POST">
                
                <div class="p-6 border-b sticky top-0 card-elevated z-10" style="border-color: rgba(255, 255, 255, 0.1);">
                    <h3 class="text-lg font-semibold" style="color: #FFFFFF;" id="roleModalTitle">Add New Role</h3>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Role Name (slug) <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <input type="text" name="name" id="roleName" required
                                   class="input-apple w-full" placeholder="e.g., project_manager">
                            <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">Lowercase, no spaces</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Display Name <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <input type="text" name="display_name" id="roleDisplayName" required
                                   class="input-apple w-full" placeholder="e.g., Project Manager">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                            Description
                        </label>
                        <textarea name="description" id="roleDescription" rows="2"
                                  class="input-apple w-full" placeholder="Brief description of this role"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-3" style="color: rgba(235, 235, 245, 0.9);">
                            Permissions
                        </label>
                        <div class="space-y-4">
                            @foreach($permissions as $group => $perms)
                                <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
                                    <div class="flex items-center justify-between mb-3">
                                        <h5 class="text-sm font-semibold" style="color: #FFFFFF;">
                                            <i class="fas fa-folder mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                                            {{ ucfirst($group) }}
                                        </h5>
                                        <label class="flex items-center">
                                            <input type="checkbox" class="group-checkbox rounded" 
                                                   data-group="{{ $group }}"
                                                   style="color: rgba(0, 122, 255, 1);">
                                            <span class="ml-2 text-xs" style="color: rgba(235, 235, 245, 0.7);">Select All</span>
                                        </label>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        @foreach($perms as $permission)
                                            <label class="flex items-center p-2 rounded hover:bg-opacity-50 transition-colors"
                                                   style="background: rgba(255, 255, 255, 0.02);">
                                                <input type="checkbox" name="permissions[]" 
                                                       value="{{ $permission->id }}"
                                                       data-group="{{ $group }}"
                                                       class="permission-checkbox rounded" 
                                                       style="color: rgba(0, 122, 255, 1);">
                                                <span class="ml-2 text-xs" style="color: rgba(235, 235, 245, 0.8);">
                                                    {{ $permission->display_name }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t flex justify-end space-x-3 sticky bottom-0 card-elevated" style="border-color: rgba(255, 255, 255, 0.1);">
                    <button type="button" onclick="closeRoleModal()"
                            class="px-4 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                            style="background: rgba(255, 255, 255, 0.1); color: rgba(235, 235, 245, 0.9);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                            style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                        Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const roles = @json($roles);

function openRoleModal() {
    document.getElementById('roleModal').classList.remove('hidden');
    document.getElementById('roleForm').action = '{{ route('settings.roles.store') }}';
    document.getElementById('roleMethod').value = 'POST';
    document.getElementById('roleModalTitle').textContent = 'Add New Role';
    document.getElementById('roleForm').reset();
    document.getElementById('roleName').readOnly = false;
}

function editRole(roleId) {
    const role = roles.find(r => r.id === roleId);
    if (!role) return;

    document.getElementById('roleModal').classList.remove('hidden');
    document.getElementById('roleForm').action = `/settings/roles/${roleId}`;
    document.getElementById('roleMethod').value = 'PUT';
    document.getElementById('roleModalTitle').textContent = 'Edit Role';
    
    document.getElementById('roleName').value = role.name;
    document.getElementById('roleName').readOnly = true;
    document.getElementById('roleDisplayName').value = role.display_name;
    document.getElementById('roleDescription').value = role.description || '';
    
    // Clear all checkboxes first
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    
    // Check permissions that belong to this role
    role.permissions.forEach(perm => {
        const checkbox = document.querySelector(`input[value="${perm.id}"]`);
        if (checkbox) checkbox.checked = true;
    });
    
    updateGroupCheckboxes();
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.add('hidden');
}

// Group checkbox functionality
document.querySelectorAll('.group-checkbox').forEach(groupCheckbox => {
    groupCheckbox.addEventListener('change', function() {
        const group = this.dataset.group;
        const checked = this.checked;
        document.querySelectorAll(`input[data-group="${group}"].permission-checkbox`).forEach(cb => {
            cb.checked = checked;
        });
    });
});

// Update group checkbox when individual permissions change
document.querySelectorAll('.permission-checkbox').forEach(permCheckbox => {
    permCheckbox.addEventListener('change', updateGroupCheckboxes);
});

function updateGroupCheckboxes() {
    document.querySelectorAll('.group-checkbox').forEach(groupCheckbox => {
        const group = groupCheckbox.dataset.group;
        const permCheckboxes = document.querySelectorAll(`input[data-group="${group}"].permission-checkbox`);
        const checkedCount = Array.from(permCheckboxes).filter(cb => cb.checked).length;
        groupCheckbox.checked = checkedCount === permCheckboxes.length;
        groupCheckbox.indeterminate = checkedCount > 0 && checkedCount < permCheckboxes.length;
    });
}

// Close modal on outside click
document.getElementById('roleModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRoleModal();
    }
});
</script>

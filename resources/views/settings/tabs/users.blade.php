{{-- Users Tab --}}
<div>
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold" style="color: #FFFFFF;">User Management</h3>
            <p class="text-sm mt-1" style="color: rgba(235, 235, 245, 0.6);">
                Manage system users and assign roles (Total: {{ $users->count() }} users)
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- Search Box -->
            <div class="relative">
                <input type="text" id="userSearch" placeholder="Search users..." 
                       class="input-apple w-64 pl-10" 
                       style="background: rgba(255, 255, 255, 0.05);">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-xs" 
                   style="color: rgba(235, 235, 245, 0.4);"></i>
            </div>
            
            <!-- Filter by Role -->
            <select id="roleFilter" class="input-apple" style="background: rgba(255, 255, 255, 0.05);">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                @endforeach
            </select>
            
            <button onclick="openUserModal()" class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                    style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                <i class="fas fa-plus text-xs mr-2"></i>
                Add User
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
        <table class="min-w-full divide-y" style="border-color: rgba(255, 255, 255, 0.08);">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.02);">
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        User
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        Username
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        Role & Position
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        Contact
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        Joined / Last Login
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: rgba(255, 255, 255, 0.05);">
                @forelse($users as $user)
                <tr class="hover:bg-opacity-50 transition-colors duration-200 user-row" 
                    style="background: transparent;"
                    data-name="{{ strtolower($user->full_name ?? $user->name) }}"
                    data-email="{{ strtolower($user->email) }}"
                    data-username="{{ strtolower($user->username ?? $user->name) }}"
                    data-role="{{ $user->role_id }}">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($user->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center" style="background: rgba(0, 122, 255, 0.2);">
                                        <span class="text-sm font-medium" style="color: rgba(0, 122, 255, 1);">
                                            {{ strtoupper(substr($user->full_name ?? $user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium" style="color: #FFFFFF;">{{ $user->full_name ?? $user->name }}</div>
                                <div class="text-xs" style="color: rgba(235, 235, 245, 0.5);">{{ $user->email }}</div>
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center text-xs" style="color: rgba(52, 199, 89, 0.8);">
                                        <i class="fas fa-check-circle mr-1"></i>Verified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm font-mono" style="color: rgba(235, 235, 245, 0.9);">{{ $user->username ?? $user->name }}</div>
                        @if($user->employee_id)
                            <div class="text-xs" style="color: rgba(235, 235, 245, 0.4);">ID: {{ $user->employee_id }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($user->role)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-apple"
                                  style="background: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 1);">
                                <i class="fas fa-user-shield mr-1"></i>
                                {{ $user->role->display_name }}
                            </span>
                        @else
                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.4);">No role</span>
                        @endif
                        @if($user->position)
                            <div class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.6);">{{ $user->position }}</div>
                        @endif
                        @if($user->department)
                            <div class="text-xs" style="color: rgba(235, 235, 245, 0.5);">{{ $user->department }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm" style="color: rgba(235, 235, 245, 0.7);">
                            @if($user->phone)
                                <div><i class="fas fa-phone text-xs mr-1"></i>{{ $user->phone }}</div>
                            @else
                                <span style="color: rgba(235, 235, 245, 0.4);">-</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        @if($user->is_active)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-apple"
                                  style="background: rgba(52, 199, 89, 0.15); color: rgba(52, 199, 89, 1);">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-apple"
                                  style="background: rgba(255, 59, 48, 0.15); color: rgba(255, 59, 48, 1);">
                                <i class="fas fa-times-circle mr-1"></i>Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                            <div><i class="fas fa-calendar-plus mr-1"></i>{{ $user->created_at->format('d M Y') }}</div>
                            <div class="mt-1"><i class="fas fa-clock mr-1"></i>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button onclick="editUser({{ $user->id }})" 
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-apple transition-all duration-300"
                                    style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.6);"
                                    title="Edit">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('settings.users.toggle-status', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-apple transition-all duration-300"
                                            style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.6);"
                                            title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} text-xs"></i>
                                    </button>
                                </form>
                                <form action="{{ route('settings.users.delete', $user) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-apple transition-all duration-300"
                                            style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.6);"
                                            title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-users text-3xl mb-2" style="color: rgba(235, 235, 245, 0.25);"></i>
                            <p class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.5);">No users found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background: rgba(0, 0, 0, 0.7);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="card-elevated rounded-apple-lg w-full max-w-2xl">
            <form id="userForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="userMethod" value="POST">
                
                <div class="p-6 border-b" style="border-color: rgba(255, 255, 255, 0.1);">
                    <h3 class="text-lg font-semibold" style="color: #FFFFFF;" id="modalTitle">Add New User</h3>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Error Alert -->
                    <div id="userFormErrors" class="hidden p-4 rounded-lg" style="background: rgba(255, 59, 48, 0.15); border: 1px solid rgba(255, 59, 48, 0.3);">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                            <div class="flex-1">
                                <h4 class="font-semibold text-red-500 mb-1">Validation Error</h4>
                                <ul id="errorList" class="text-sm space-y-1" style="color: rgba(255, 59, 48, 0.9);"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Username (for login) -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Username <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <input type="text" name="username" id="userUsername" required
                                   class="input-apple w-full" placeholder="username_for_login">
                            <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.4);">Digunakan untuk login (lowercase, tanpa spasi)</p>
                        </div>

                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Full Name <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <input type="text" name="full_name" id="userFullName" required
                                   class="input-apple w-full" placeholder="Nama lengkap untuk display">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Email <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <input type="email" name="email" id="userEmail" required
                                   class="input-apple w-full" placeholder="user@bizmark.id">
                        </div>

                        <!-- Employee ID -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Employee ID / NIP
                            </label>
                            <input type="text" name="employee_id" id="userEmployeeId"
                                   class="input-apple w-full" placeholder="EMP-001">
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Password <span style="color: rgba(255, 59, 48, 1);" id="passwordRequired">*</span>
                            </label>
                            <input type="password" name="password" id="userPassword"
                                   autocomplete="new-password"
                                   class="input-apple w-full" placeholder="Minimal 8 karakter">
                            <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.4);">Kosongkan jika tidak ingin mengubah password</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Confirm Password <span style="color: rgba(255, 59, 48, 1);" id="confirmRequired">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="userPasswordConfirm"
                                   autocomplete="new-password"
                                   class="input-apple w-full" placeholder="Ulangi password">
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Role <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <select name="role_id" id="userRole" required class="input-apple w-full">
                                <option value="">Pilih role...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Position -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Position / Jabatan
                            </label>
                            <input type="text" name="position" id="userPosition"
                                   class="input-apple w-full" placeholder="Project Manager">
                        </div>

                        <!-- Department -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Department / Divisi
                            </label>
                            <input type="text" name="department" id="userDepartment"
                                   class="input-apple w-full" placeholder="IT Department">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Phone Number
                            </label>
                            <input type="text" name="phone" id="userPhone"
                                   class="input-apple w-full" placeholder="+62 838 7960 2855">
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Internal Notes
                            </label>
                            <textarea name="notes" id="userNotes" class="input-apple w-full" rows="2" 
                                      placeholder="Catatan internal tentang user ini"></textarea>
                        </div>

                        <!-- Avatar -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Profile Photo
                            </label>
                            <input type="file" name="avatar" id="userAvatar" accept="image/*"
                                   class="input-apple w-full">
                            <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.4);">JPG, PNG. Max 2MB</p>
                        </div>
                    </div>

                    <!-- Active Status (Edit Only) -->
                    <div id="editOnlyFields" class="hidden mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="userIsActive" value="1" checked
                                   class="rounded" style="color: rgba(0, 122, 255, 1);">
                            <span class="ml-2 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                                <i class="fas fa-check-circle mr-1"></i>Active User
                            </span>
                        </label>
                        <p class="text-xs mt-1 ml-6" style="color: rgba(235, 235, 245, 0.4);">Nonaktifkan untuk mencegah login tanpa menghapus data</p>
                    </div>
                </div>

                <div class="p-6 border-t flex justify-end space-x-3" style="border-color: rgba(255, 255, 255, 0.1);">
                    <button type="button" onclick="closeUserModal()"
                            class="px-4 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                            style="background: rgba(255, 255, 255, 0.1); color: rgba(235, 235, 245, 0.9);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                            style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                        Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const users = @json($users);

// ========== SEARCH AND FILTER ==========
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearch');
    const roleFilter = document.getElementById('roleFilter');
    const userRows = document.querySelectorAll('.user-row');
    const userCount = document.getElementById('userCount');

    function filterUsers() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedRole = roleFilter.value;
        let visibleCount = 0;

        userRows.forEach(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const username = row.dataset.username || '';
            const role = row.dataset.role || '';

            const matchesSearch = name.includes(searchTerm) || 
                                 email.includes(searchTerm) || 
                                 username.includes(searchTerm);
            const matchesRole = !selectedRole || role === selectedRole;

            if (matchesSearch && matchesRole) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        userCount.textContent = `Showing ${visibleCount} of ${userRows.length} users`;
    }

    if (searchInput) searchInput.addEventListener('input', filterUsers);
    if (roleFilter) roleFilter.addEventListener('change', filterUsers);
});

// ========== MODAL MANAGEMENT ==========
function openUserModal() {
    document.getElementById('userModal').classList.remove('hidden');
    document.getElementById('userForm').action = '{{ route('settings.users.store') }}';
    document.getElementById('userMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('userForm').reset();
    document.getElementById('userNotes').value = '';
    document.getElementById('editOnlyFields').classList.add('hidden');
    document.getElementById('userPassword').required = true;
    document.getElementById('userPasswordConfirm').required = true;
    document.getElementById('passwordRequired').style.display = 'inline';
    document.getElementById('confirmRequired').style.display = 'inline';
}

function editUser(userId) {
    const user = users.find(u => u.id === userId);
    if (!user) return;

    document.getElementById('userModal').classList.remove('hidden');
    document.getElementById('userForm').action = `/settings/users/${userId}`;
    document.getElementById('userMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Edit User';
    
    // Populate all fields including new ones
    document.getElementById('userUsername').value = user.username || user.name; // fallback to name
    document.getElementById('userFullName').value = user.full_name || '';
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userEmployeeId').value = user.employee_id || '';
    document.getElementById('userRole').value = user.role_id || '';
    document.getElementById('userPosition').value = user.position || '';
    document.getElementById('userDepartment').value = user.department || '';
    document.getElementById('userPhone').value = user.phone || '';
    document.getElementById('userNotes').value = user.notes || '';
    document.getElementById('userIsActive').checked = user.is_active;
    
    document.getElementById('userPassword').value = '';
    document.getElementById('userPasswordConfirm').value = '';
    document.getElementById('userPassword').required = false;
    document.getElementById('userPasswordConfirm').required = false;
    document.getElementById('passwordRequired').style.display = 'none';
    document.getElementById('confirmRequired').style.display = 'none';
    
    document.getElementById('editOnlyFields').classList.remove('hidden');
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.getElementById('userFormErrors').classList.add('hidden');
}

// Form submission handler
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';
    
    // Hide previous errors
    document.getElementById('userFormErrors').classList.add('hidden');
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw data;
            });
        }
        return response.json();
    })
    .then(data => {
        // Success - reload page
        window.location.href = '{{ route('settings.index') }}?tab=users';
    })
    .catch(error => {
        // Show errors
        if (error.errors) {
            const errorList = document.getElementById('errorList');
            errorList.innerHTML = '';
            
            Object.values(error.errors).forEach(messages => {
                messages.forEach(message => {
                    const li = document.createElement('li');
                    li.textContent = message;
                    errorList.appendChild(li);
                });
            });
            
            document.getElementById('userFormErrors').classList.remove('hidden');
        } else if (error.message) {
            alert(error.message);
        } else {
            alert('An error occurred. Please try again.');
        }
        
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Close modal on outside click
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUserModal();
    }
});
</script>

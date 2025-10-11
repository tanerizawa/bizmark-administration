{{-- Users Tab --}}
<div>
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold" style="color: #FFFFFF;">User Management</h3>
            <p class="text-sm mt-1" style="color: rgba(235, 235, 245, 0.6);">
                Manage system users and assign roles
            </p>
        </div>
        <button onclick="openUserModal()" class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
            <i class="fas fa-plus text-xs mr-2"></i>
            Add User
        </button>
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
                        Role
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        Contact
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        Last Login
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.5);">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: rgba(255, 255, 255, 0.05);">
                @forelse($users as $user)
                <tr class="hover:bg-opacity-50 transition-colors duration-200" style="background: transparent;">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($user->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center" style="background: rgba(0, 122, 255, 0.2);">
                                        <span class="text-sm font-medium" style="color: rgba(0, 122, 255, 1);">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium" style="color: #FFFFFF;">{{ $user->name }}</div>
                                <div class="text-xs" style="color: rgba(235, 235, 245, 0.5);">{{ $user->email }}</div>
                                @if($user->position)
                                    <div class="text-xs" style="color: rgba(235, 235, 245, 0.4);">{{ $user->position }}</div>
                                @endif
                            </div>
                        </div>
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
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
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
                    <td colspan="6" class="px-4 py-8 text-center">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Full Name <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <input type="text" name="name" id="userName" required
                                   class="input-apple w-full" placeholder="John Doe">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Email <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <input type="email" name="email" id="userEmail" required
                                   class="input-apple w-full" placeholder="john@example.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Password <span style="color: rgba(255, 59, 48, 1);" id="passwordRequired">*</span>
                            </label>
                            <input type="password" name="password" id="userPassword"
                                   class="input-apple w-full" placeholder="Min. 8 characters">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Confirm Password <span style="color: rgba(255, 59, 48, 1);" id="confirmRequired">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="userPasswordConfirm"
                                   class="input-apple w-full" placeholder="Confirm password">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Role <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <select name="role_id" id="userRole" required class="input-apple w-full">
                                <option value="">Select role...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Position
                            </label>
                            <input type="text" name="position" id="userPosition"
                                   class="input-apple w-full" placeholder="e.g., Project Manager">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Phone
                            </label>
                            <input type="text" name="phone" id="userPhone"
                                   class="input-apple w-full" placeholder="+62 xxx xxx xxx">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                                Avatar
                            </label>
                            <input type="file" name="avatar" id="userAvatar" accept="image/*"
                                   class="input-apple w-full">
                        </div>
                    </div>

                    <div id="editOnlyFields" class="hidden">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="userIsActive" value="1" checked
                                   class="rounded" style="color: rgba(0, 122, 255, 1);">
                            <span class="ml-2 text-sm" style="color: rgba(235, 235, 245, 0.9);">Active</span>
                        </label>
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

function openUserModal() {
    document.getElementById('userModal').classList.remove('hidden');
    document.getElementById('userForm').action = '{{ route('settings.users.store') }}';
    document.getElementById('userMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('userForm').reset();
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
    
    document.getElementById('userName').value = user.name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userRole').value = user.role_id || '';
    document.getElementById('userPosition').value = user.position || '';
    document.getElementById('userPhone').value = user.phone || '';
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
}

// Close modal on outside click
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUserModal();
    }
});
</script>

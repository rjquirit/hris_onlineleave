@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>User Management</h2>
    <button class="btn btn-primary" onclick="openCreateModal()">Create User</button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Personnel</th>
                        <th>Current Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Create User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="userName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="userEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span id="passwordHint" class="text-muted small">(Leave blank to keep current)</span></label>
                        <input type="password" class="form-control" id="userPassword">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Personnel Link</label>
                        <select class="form-select" id="userPersonnel">
                            <option value="">None</option>
                            <!-- Personnel will be populated here -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" id="userRole">
                            <option value="">None</option>
                            <!-- Roles will be populated here -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let userModal;
    let allRoles = [];
    let allPersonnel = [];

    document.addEventListener('DOMContentLoaded', function() {
        userModal = new bootstrap.Modal(document.getElementById('userModal'));
        loadUsers();
        loadRoles();
        loadPersonnel();
    });

    async function loadRoles() {
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch('/api/roles', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            allRoles = await response.json();
            
            const select = document.getElementById('userRole');
            select.innerHTML = '<option value="">None</option>' + 
                allRoles.map(r => `<option value="${r.name}">${r.name}</option>`).join('');
        } catch (error) {
            console.error('Error loading roles:', error);
        }
    }

    async function loadPersonnel() {
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch('/api/personnel', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            allPersonnel = await response.json();
            
            const select = document.getElementById('userPersonnel');
            select.innerHTML = '<option value="">None</option>' + 
                allPersonnel.map(p => `<option value="${p.id}">${p.first_name} ${p.last_name}</option>`).join('');
        } catch (error) {
            console.error('Error loading personnel:', error);
        }
    }

    async function loadUsers() {
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch('/api/users', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const users = await response.json();
            
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>${user.id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.personnel ? user.personnel.first_name + ' ' + user.personnel.last_name : '<span class="text-muted">None</span>'}</td>
                    <td>${user.roles && user.roles.length > 0 ? '<span class="badge bg-primary">'+user.roles[0].name+'</span>' : '<span class="badge bg-secondary">No Role</span>'}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="openEditModal(${user.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    function openCreateModal() {
        document.getElementById('userModalTitle').textContent = 'Create User';
        document.getElementById('userId').value = '';
        document.getElementById('userForm').reset();
        document.getElementById('passwordHint').style.display = 'none';
        userModal.show();
    }

    async function openEditModal(id) {
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch(`/api/users/${id}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const user = await response.json();

            document.getElementById('userModalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = user.id;
            document.getElementById('userName').value = user.name;
            document.getElementById('userEmail').value = user.email;
            document.getElementById('userPassword').value = '';
            document.getElementById('passwordHint').style.display = 'inline';
            document.getElementById('userPersonnel').value = user.personnel_id || '';
            document.getElementById('userRole').value = user.roles && user.roles.length > 0 ? user.roles[0].name : '';
            
            userModal.show();
        } catch (error) {
            console.error('Error loading user details:', error);
        }
    }

    async function saveUser() {
        const id = document.getElementById('userId').value;
        const name = document.getElementById('userName').value;
        const email = document.getElementById('userEmail').value;
        const password = document.getElementById('userPassword').value;
        const personnel_id = document.getElementById('userPersonnel').value;
        const role = document.getElementById('userRole').value;

        const data = { name, email, personnel_id, role };
        if (password) data.password = password;

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/users/${id}` : '/api/users';

        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                userModal.hide();
                loadUsers();
            } else {
                const error = await response.json();
                alert('Error: ' + (error.message || JSON.stringify(error)));
            }
        } catch (error) {
            console.error('Error saving user:', error);
        }
    }

    async function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) return;

        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch(`/api/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                loadUsers();
            } else {
                const error = await response.json();
                alert('Error: ' + (error.message || JSON.stringify(error)));
            }
        } catch (error) {
            console.error('Error deleting user:', error);
        }
    }
</script>
@endsection

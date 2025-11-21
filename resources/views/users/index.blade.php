@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>User Management</h2>
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

<!-- Assign Role Modal -->
<div class="modal fade" id="assignRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignRoleForm">
                    <input type="hidden" id="userId">
                    <div class="mb-3">
                        <label class="form-label">User: <span id="userNameDisplay" class="fw-bold"></span></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Role</label>
                        <select class="form-select" id="roleSelect" required>
                            <option value="">Select a role...</option>
                            <!-- Roles will be populated here -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveUserRole()">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let assignRoleModal;
    let allRoles = [];

    document.addEventListener('DOMContentLoaded', function() {
        assignRoleModal = new bootstrap.Modal(document.getElementById('assignRoleModal'));
        loadUsers();
        loadRolesForSelect();
    });

    async function loadRolesForSelect() {
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch('/api/roles', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            allRoles = await response.json();
            
            const select = document.getElementById('roleSelect');
            select.innerHTML = '<option value="">Select a role...</option>' + 
                allRoles.map(r => `<option value="${r.name}">${r.name}</option>`).join('');
        } catch (error) {
            console.error('Error loading roles:', error);
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
                    <td>${user.roles && user.roles.length > 0 ? '<span class="badge bg-primary">'+user.roles[0].name+'</span>' : '<span class="badge bg-secondary">No Role</span>'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="openAssignRoleModal(${user.id}, '${user.name}', '${user.roles && user.roles.length > 0 ? user.roles[0].name : ''}')">Assign Role</button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    function openAssignRoleModal(id, name, currentRole) {
        document.getElementById('userId').value = id;
        document.getElementById('userNameDisplay').textContent = name;
        document.getElementById('roleSelect').value = currentRole || '';
        assignRoleModal.show();
    }

    async function saveUserRole() {
        const id = document.getElementById('userId').value;
        const role = document.getElementById('roleSelect').value;

        if (!role) {
            alert('Please select a role');
            return;
        }

        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch(`/api/users/${id}/assign-role`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ role })
            });

            if (response.ok) {
                assignRoleModal.hide();
                loadUsers();
            } else {
                const error = await response.json();
                alert('Error: ' + (error.message || JSON.stringify(error)));
            }
        } catch (error) {
            console.error('Error assigning role:', error);
        }
    }
</script>
@endsection

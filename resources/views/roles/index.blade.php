@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Role Management</h2>
    <button type="button" class="btn btn-primary" onclick="openRoleModal()">
        Add Role
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="roles-table-body">
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="roleForm">
                    <input type="hidden" id="roleId">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="roleName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div id="permissionsList" class="form-check">
                            <!-- Checkboxes will be populated here -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveRole()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let roleModal;
    let allPermissions = [];

    document.addEventListener('DOMContentLoaded', function() {
        roleModal = new bootstrap.Modal(document.getElementById('roleModal'));
        loadRoles();
        loadPermissions();
    });

    async function loadPermissions() {
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch('/api/permissions', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            allPermissions = await response.json();
            
            const container = document.getElementById('permissionsList');
            container.innerHTML = allPermissions.map(p => `
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" value="${p.name}" id="perm_${p.id}">
                    <label class="form-check-label" for="perm_${p.id}">
                        ${p.name}
                    </label>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading permissions:', error);
        }
    }

    async function loadRoles() {
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch('/api/roles', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const roles = await response.json();
            
            const tbody = document.getElementById('roles-table-body');
            tbody.innerHTML = roles.map(role => `
                <tr>
                    <td>${role.id}</td>
                    <td>${role.name}</td>
                    <td>${role.permissions ? role.permissions.map(p => '<span class="badge bg-secondary me-1">'+p.name+'</span>').join('') : ''}</td>
                    <td>
                        <button class="btn btn-sm btn-info text-white" onclick="editRole(${role.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteRole(${role.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading roles:', error);
        }
    }

    function openRoleModal() {
        document.getElementById('roleForm').reset();
        document.getElementById('roleId').value = '';
        document.getElementById('roleModalTitle').textContent = 'Add Role';
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        roleModal.show();
    }

    async function editRole(id) {
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch(`/api/roles/${id}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const role = await response.json();

            document.getElementById('roleId').value = role.id;
            document.getElementById('roleName').value = role.name;
            
            // Reset checkboxes
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
            
            // Check assigned permissions
            role.permissions.forEach(p => {
                const checkbox = Array.from(document.querySelectorAll('.permission-checkbox')).find(cb => cb.value === p.name);
                if (checkbox) checkbox.checked = true;
            });

            document.getElementById('roleModalTitle').textContent = 'Edit Role';
            roleModal.show();
        } catch (error) {
            console.error('Error fetching role:', error);
        }
    }

    async function saveRole() {
        const id = document.getElementById('roleId').value;
        const name = document.getElementById('roleName').value;
        const permissions = Array.from(document.querySelectorAll('.permission-checkbox:checked')).map(cb => cb.value);

        const token = localStorage.getItem('access_token');
        const url = id ? `/api/roles/${id}` : '/api/roles';
        const method = id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name, permissions })
            });

            if (response.ok) {
                roleModal.hide();
                loadRoles();
            } else {
                const error = await response.json();
                alert('Error: ' + (error.message || JSON.stringify(error)));
            }
        } catch (error) {
            console.error('Error saving role:', error);
        }
    }

    async function deleteRole(id) {
        if(!confirm('Are you sure?')) return;
        
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch(`/api/roles/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (response.ok) {
                loadRoles();
            } else {
                alert('Error deleting role');
            }
        } catch (error) {
            console.error('Error deleting role:', error);
        }
    }
</script>
@endsection

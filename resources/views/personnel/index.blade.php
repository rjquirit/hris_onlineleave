@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Personnel Management</h2>
    <div>
        <button type="button" class="btn btn-success me-2" onclick="exportExcel()">Export Excel</button>
        <button type="button" class="btn btn-danger me-2" onclick="downloadPdf()">Download PDF</button>
        <button type="button" class="btn btn-primary" onclick="openModal()">
            Add Personnel
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="personnel-table-body">
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="personnelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Personnel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="personnelForm">
                    <input type="hidden" id="personnelId">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" class="form-control" id="position" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" id="department" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Salary</label>
                        <input type="number" step="0.01" class="form-control" id="salary" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="savePersonnel()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this personnel?</p>
                <input type="hidden" id="deleteId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let deleteModal;

    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('access_token');
        if (!token) {
            window.location.href = '/login';
            return;
        }
        
        personnelModal = new bootstrap.Modal(document.getElementById('personnelModal'));
        deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        // Event Delegation
        document.getElementById('personnel-table-body').addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn')) {
                editPersonnel(e.target.getAttribute('data-id'));
            } else if (e.target.classList.contains('delete-btn')) {
                deletePersonnel(e.target.getAttribute('data-id'));
            }
        });

        loadPersonnel();
    });

    // ... (loadPersonnel, openModal, editPersonnel, savePersonnel remain unchanged)

    function deletePersonnel(id) {
        console.log('deletePersonnel called with id:', id);
        document.getElementById('deleteId').value = id;
        deleteModal.show();
    }

    async function confirmDelete() {
        const id = document.getElementById('deleteId').value;
        const token = localStorage.getItem('access_token');
        
        try {
            const response = await fetch(`/api/personnel/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                console.log('Delete successful, reloading...');
                deleteModal.hide();
                loadPersonnel();
            } else {
                const errorData = await response.json();
                console.error('Delete failed:', errorData);
                alert('Error deleting personnel: ' + (errorData.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error deleting personnel:', error);
        }
    }

    async function loadPersonnel() {
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch('/api/personnel?t=' + new Date().getTime(), {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if (response.status === 401) {
                logout(new Event('click'));
                return;
            }

            const data = await response.json();
            const tbody = document.getElementById('personnel-table-body');
            tbody.innerHTML = '';

            let rows = '';
            data.forEach(person => {
                rows += `
                    <tr>
                        <td>${person.first_name} ${person.last_name}</td>
                        <td>${person.email}</td>
                        <td>${person.position}</td>
                        <td>${person.department}</td>
                        <td>${parseFloat(person.salary).toFixed(2)}</td>
                        <td>
                            <button class="btn btn-sm btn-info text-white edit-btn" data-id="${person.id}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${person.id}">Delete</button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = rows;
        } catch (error) {
            console.error('Error loading personnel:', error);
        }
    }

    function openModal() {
        document.getElementById('personnelForm').reset();
        document.getElementById('personnelId').value = '';
        document.getElementById('modalTitle').textContent = 'Add Personnel';
        personnelModal.show();
    }

    async function editPersonnel(id) {
        const token = localStorage.getItem('access_token');
        try {
            const response = await fetch(`/api/personnel/${id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const person = await response.json();

            document.getElementById('personnelId').value = person.id;
            document.getElementById('firstName').value = person.first_name;
            document.getElementById('lastName').value = person.last_name;
            document.getElementById('email').value = person.email;
            document.getElementById('position').value = person.position;
            document.getElementById('department').value = person.department;
            document.getElementById('salary').value = person.salary;

            document.getElementById('modalTitle').textContent = 'Edit Personnel';
            personnelModal.show();
        } catch (error) {
            console.error('Error fetching personnel:', error);
        }
    }

    async function savePersonnel() {
        const id = document.getElementById('personnelId').value;
        const data = {
            first_name: document.getElementById('firstName').value,
            last_name: document.getElementById('lastName').value,
            email: document.getElementById('email').value,
            position: document.getElementById('position').value,
            department: document.getElementById('department').value,
            salary: document.getElementById('salary').value
        };

        const token = localStorage.getItem('access_token');
        const url = id ? `/api/personnel/${id}` : '/api/personnel';
        const method = id ? 'PUT' : 'POST';

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
                personnelModal.hide();
                loadPersonnel();
            } else {
                const errorData = await response.json();
                alert('Error: ' + JSON.stringify(errorData.message || errorData));
            }
        } catch (error) {
            console.error('Error saving personnel:', error);
        }
    }


    function exportExcel() {
        const token = localStorage.getItem('access_token');
        // For file downloads with auth, we can't easily use fetch/ajax directly to trigger download
        // A common workaround is to use window.open if the route is protected by cookie/session,
        // but since we use Sanctum API token, we might need a different approach or just use a temporary signed URL.
        // However, for simplicity in this context, if we assume the browser has the session (if using Sanctum SPA auth), window.open works.
        // If using pure API token, we'd need to pass it as a query param or handle the blob response.
        
        // Let's try handling the blob response for better security with tokens.
        fetch('/api/personnel/export', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'personnel.xlsx';
            document.body.appendChild(a);
            a.click();
            a.remove();
        })
        .catch(error => console.error('Error exporting Excel:', error));
    }

    function downloadPdf() {
        const token = localStorage.getItem('access_token');
        fetch('/api/personnel/pdf', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'personnel.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
        })
        .catch(error => console.error('Error downloading PDF:', error));
    }
</script>
@endsection

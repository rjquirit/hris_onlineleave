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
<!-- Delete Modal -->
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

<!-- Leave Card Modal -->
<div class="modal fade" id="leaveCardModal" tabindex="-1" aria-labelledby="leaveCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveCardModalLabel">Leave Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <div class="mb-3 text-end">
                    <button class="btn btn-success btn-sm" onclick="exportLeaveCardExcel()">Export Excel</button>
                    <button class="btn btn-danger btn-sm" onclick="exportLeaveCardPdf()">Export PDF</button>
                </div>
                <div id="leave-card-personnel-info" class="mb-3"></div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Particulars</th>
                                <th>VL Earned</th>
                                <th>VL Abs/Und Pay</th>
                                <th>VL Bal</th>
                                <th>VL Abs/Und No Pay</th>
                                <th>SL Earned</th>
                                <th>SL Abs/Und Pay</th>
                                <th>SL Bal</th>
                                <th>SL Abs/Und No Pay</th>
                                <th>CTO Earned</th>
                                <th>CTO Abs/Und Pay</th>
                                <th>CTO Bal</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="leave-card-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let personnelModal;
    let deleteModal;
    let leaveCardModal;
    let currentPersonnelIdForLeaveCard;

    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('access_token');
        if (!token) {
            window.location.href = '/login';
            return;
        }
        
        // Initialize Modals
        personnelModal = new bootstrap.Modal(document.getElementById('personnelModal'));
        deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        leaveCardModal = new bootstrap.Modal(document.getElementById('leaveCardModal'));
        
        // Event Delegation for Table Actions
        document.getElementById('personnel-table-body').addEventListener('click', function(e) {
            const target = e.target;
            
            // Edit Button
            if (target.classList.contains('edit-btn')) {
                editPersonnel(target.getAttribute('data-id'));
                return;
            }
            
            // Delete Button
            if (target.classList.contains('delete-btn')) {
                deletePersonnel(target.getAttribute('data-id'));
                return;
            }

            // View Leave Card Button (handle icon click too)
            const viewBtn = target.closest('.view-leave-card-btn');
            if (viewBtn) {
                viewLeaveCard(viewBtn.getAttribute('data-id'));
                return;
            }
        });

        loadPersonnel();
    });

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

    function deletePersonnel(id) {
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
                deleteModal.hide();
                loadPersonnel();
            } else {
                const errorData = await response.json();
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

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            const tbody = document.getElementById('personnel-table-body');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No personnel found.</td></tr>';
                return;
            }

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
                            <button class="btn btn-sm btn-secondary view-leave-card-btn" data-id="${person.personnel_id || person.id}" title="View Leave Card"><i class="bi bi-card-list"></i></button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = rows;
        } catch (error) {
            console.error('Error loading personnel:', error);
        }
    }

    async function viewLeaveCard(personnelId) {
        currentPersonnelIdForLeaveCard = personnelId;
        const token = localStorage.getItem('access_token');
        
        // Clear previous data
        document.getElementById('leave-card-table-body').innerHTML = '<tr><td colspan="14" class="text-center">Loading...</td></tr>';
        document.getElementById('leave-card-personnel-info').innerHTML = '';
        
        if (leaveCardModal) {
            leaveCardModal.show();
        } else {
            console.error('Leave Card Modal not initialized');
            alert('Error: Leave Card Modal not initialized. Please refresh the page.');
            return;
        }

        try {
            const response = await fetch(`/leave-card/${personnelId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const result = await response.json();
                const data = result.leave_cards;
                const personnel = result.personnel;

                // Populate Personnel Info
                if (personnel) {
                    document.getElementById('leave-card-personnel-info').innerHTML = `
                        <div class="row">
                            <div class="col-md-4"><strong>Name:</strong> ${personnel.first_name} ${personnel.last_name}</div>
                            <div class="col-md-4"><strong>Position:</strong> ${personnel.position}</div>
                            <div class="col-md-4"><strong>Department:</strong> ${personnel.department}</div>
                        </div>
                    `;
                } else {
                    document.getElementById('leave-card-personnel-info').innerHTML = '<div class="text-danger">Personnel information not found.</div>';
                }

                const tbody = document.getElementById('leave-card-table-body');
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="14" class="text-center">No records found.</td></tr>';
                    return;
                }

                let rows = '';
                data.forEach(card => {
                    rows += `
                        <tr>
                            <td>${card.PERIOD || ''}</td>
                            <td>${card.PARTICULARS || ''}</td>
                            <td>${card.VL_EARNED || ''}</td>
                            <td>${card.VL_ABSENCE_UNDERTIMEWITHPAY || ''}</td>
                            <td>${card.VL_BALANCE || ''}</td>
                            <td>${card.VL_ABSENCE_UNDERTIMEWITHOUTPAY || ''}</td>
                            <td>${card.SL_EARNED || ''}</td>
                            <td>${card.SL_ABSENCE_UNDERTIMEWITHPAY || ''}</td>
                            <td>${card.SL_BALANCE || ''}</td>
                            <td>${card.SL_ABSENCE_UNDERTIMEWITHOUTPAY || ''}</td>
                            <td>${card.CTO_EARNED_HRS || ''}</td>
                            <td>${card.CTO_ABSENCE_UNDERTIMEWITHPAY_HRS || ''}</td>
                            <td>${card.CTO_BALANCE_HRS || ''}</td>
                            <td>${card.CTO_REMARK || ''}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = rows;
            } else {
                console.error('Failed to fetch leave card data');
                document.getElementById('leave-card-table-body').innerHTML = '<tr><td colspan="14" class="text-center text-danger">Failed to load data.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching leave card:', error);
            document.getElementById('leave-card-table-body').innerHTML = '<tr><td colspan="14" class="text-center text-danger">Error loading data.</td></tr>';
        }
    }

    function exportExcel() {
        const token = localStorage.getItem('access_token');
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

    function exportLeaveCardExcel() {
        if (!currentPersonnelIdForLeaveCard) return;
        window.location.href = `/leave-card/export/excel/${currentPersonnelIdForLeaveCard}`;
    }

    function exportLeaveCardPdf() {
        if (!currentPersonnelIdForLeaveCard) return;
        window.location.href = `/leave-card/export/pdf/${currentPersonnelIdForLeaveCard}`;
    }
</script>
@endsection

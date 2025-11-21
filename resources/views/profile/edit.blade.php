@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Profile') }}</div>

                <div class="card-body">
                    <div id="alert-container"></div>

                    <form id="profileForm">
                        <div class="mb-3 row">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" required>
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3 text-center">Change Password (Optional)</h5>

                        <div class="mb-3 row">
                            <label for="current_password" class="col-md-4 col-form-label text-md-end">{{ __('Current Password') }}</label>

                            <div class="col-md-6">
                                <input id="current_password" type="password" class="form-control">
                                <div class="form-text">Required only if changing password</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('New Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Profile') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadProfile();
        
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            updateProfile();
        });
    });

    async function loadProfile() {
        const token = localStorage.getItem('access_token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        try {
            const response = await fetch('/api/user', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const user = await response.json();
                document.getElementById('name').value = user.name;
                document.getElementById('email').value = user.email;
            } else {
                console.error('Failed to load profile');
                if (response.status === 401) {
                    window.location.href = '/login';
                }
            }
        } catch (error) {
            console.error('Error loading profile:', error);
        }
    }

    async function updateProfile() {
        const token = localStorage.getItem('access_token');
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const current_password = document.getElementById('current_password').value;
        const password = document.getElementById('password').value;
        const password_confirmation = document.getElementById('password-confirm').value;

        const data = { name, email };
        if (password) {
            data.current_password = current_password;
            data.password = password;
            data.password_confirmation = password_confirmation;
        }

        try {
            const response = await fetch('/api/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            const alertContainer = document.getElementById('alert-container');

            if (response.ok) {
                alertContainer.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                document.getElementById('current_password').value = '';
                document.getElementById('password').value = '';
                document.getElementById('password-confirm').value = '';
            } else {
                let errorMessage = result.message || 'Update failed';
                if (result.errors) {
                    errorMessage = Object.values(result.errors).flat().join('<br>');
                }
                alertContainer.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            document.getElementById('alert-container').innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
        }
    }
</script>
@endsection

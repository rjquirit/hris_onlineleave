@extends('tyro-login::layouts.auth')

@section('content')
<div class="auth-container {{ $layout }}" @if($layout==='fullscreen' ) style="background-image: url('{{ $backgroundImage }}');" @endif>
    @if(in_array($layout, ['split-left', 'split-right']))
    <div class="background-panel" style="background-image: url('{{ $backgroundImage }}');">
        <div class="background-panel-content">
            <h1>{{ $pageContent['background_title'] ?? 'Forgot Your Password?' }}</h1>
            <p>{{ $pageContent['background_description'] ?? 'No worries! Enter your email and we\'ll send you a link to reset your password.' }}</p>
        </div>
    </div>
    @endif

    <div class="form-panel">
        <div class="form-card">
            <!-- Logo -->
            <div class="logo-container">
                @if($branding['logo'] ?? false)
                <img src="{{ $branding['logo'] }}" alt="{{ $branding['app_name'] ?? config('app.name') }}">
                @else
                <div class="app-logo">
                    <svg viewBox="0 0 50 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 0 1-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 0 1 0 39.25V6.334c0-.072.01-.142.028-.21.006-.023.02-.044.028-.067.015-.042.029-.085.051-.124.015-.026.037-.047.055-.071.023-.032.044-.065.071-.093.023-.023.053-.04.079-.06.029-.024.055-.05.088-.069h.001l9.61-5.533a.802.802 0 0 1 .8 0l9.61 5.533h.002c.032.02.059.045.088.068.026.02.055.038.078.06.028.029.048.062.072.094.017.024.04.045.054.071.023.04.036.082.052.124.008.023.022.044.028.068a.809.809 0 0 1 .028.209v20.559l8.008-4.611v-10.51c0-.07.01-.141.028-.208.007-.024.02-.045.028-.068.016-.042.03-.085.052-.124.015-.026.037-.047.054-.071.024-.032.044-.065.072-.093.023-.023.052-.04.078-.06.03-.024.056-.05.088-.069h.001l9.611-5.533a.801.801 0 0 1 .8 0l9.61 5.533c.034.02.06.045.09.068.025.02.054.038.077.06.028.029.048.062.072.094.018.024.04.045.054.071.023.039.036.082.052.124.009.023.022.044.028.068zm-1.574 10.718v-9.124l-3.363 1.936-4.646 2.675v9.124l8.01-4.611zm-9.61 16.505v-9.13l-4.57 2.61-13.05 7.448v9.216l17.62-10.144zM1.602 7.719v31.068L19.22 48.93v-9.214l-9.204-5.209-.003-.002-.004-.002c-.031-.018-.057-.044-.086-.066-.025-.02-.054-.036-.076-.058l-.002-.003c-.026-.025-.044-.056-.066-.084-.02-.027-.044-.05-.06-.078l-.001-.003c-.018-.03-.029-.066-.042-.1-.013-.03-.03-.058-.038-.09v-.001c-.01-.038-.012-.078-.016-.117-.004-.03-.012-.06-.012-.09v-.002-21.481L4.965 9.654 1.602 7.72zm8.81-5.994L2.405 6.334l8.005 4.609 8.006-4.61-8.006-4.608zm4.164 28.764l4.645-2.674V7.719l-3.363 1.936-4.646 2.675v20.096l3.364-1.937zM39.243 7.164l-8.006 4.609 8.006 4.609 8.005-4.61-8.005-4.608zm-.801 10.605l-4.646-2.675-3.363-1.936v9.124l4.645 2.674 3.364 1.937v-9.124zM20.02 38.33l11.743-6.704 5.87-3.35-8-4.606-9.211 5.303-8.395 4.833 7.993 4.524z" fill="currentColor" />
                    </svg>
                </div>
                @endif
            </div>

            <!-- Header -->
            <div class="form-header">
                <h2>{{ $pageContent['title'] ?? 'Forgot Password?' }}</h2>
                <p>{{ $pageContent['subtitle'] ?? 'Enter your email and we\'ll send you a reset link.' }}</p>
            </div>

            <!-- Success Message Container -->
            <div id="api-success-message" class="success-message" style="display: none;">
                <p></p>
            </div>

            <!-- Error Message Container -->
            <div id="api-error-message" class="error-message-container" style="display: none;">
                <span class="error-message"></span>
            </div>

            <!-- Forgot Password Form -->
            <form id="forgot-password-form">
                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="email@example.com">
                </div>

                <!-- Submit Button -->
                <button type="submit" id="forgot-password-submit-btn" class="btn btn-primary">
                    <span id="forgot-password-btn-text">Send Reset Link</span>
                    <span id="forgot-password-btn-spinner" style="display: none;">
                        <svg class="spinner" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="form-footer">
                <p>
                    Remember your password?
                    <a href="{{ route('tyro-login.login') }}" class="form-link">Back to Login</a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Error Message Container */
    .error-message-container {
        margin-bottom: 1.25rem;
        padding: 0.75rem 1rem;
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 0.5rem;
    }

    .error-message-container .error-message {
        color: rgb(239, 68, 68);
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Success Message */
    .success-message {
        background-color: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    html.dark .success-message {
        background-color: #052e16;
        border-color: #166534;
    }

    .success-message p {
        color: #059669;
        font-size: 0.875rem;
        margin: 0;
    }

    html.dark .success-message p {
        color: #34d399;
    }

    /* Loading Spinner */
    .spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Button disabled state */
    #forgot-password-submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgot-password-form');
    const submitBtn = document.getElementById('forgot-password-submit-btn');
    const btnText = document.getElementById('forgot-password-btn-text');
    const btnSpinner = document.getElementById('forgot-password-btn-spinner');
    const errorContainer = document.getElementById('api-error-message');
    const errorMessage = errorContainer.querySelector('.error-message');
    const successContainer = document.getElementById('api-success-message');
    const successMessage = successContainer.querySelector('p');
    const emailInput = document.getElementById('email');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Hide previous messages
        errorContainer.style.display = 'none';
        successContainer.style.display = 'none';
        errorMessage.textContent = '';
        successMessage.textContent = '';

        const email = emailInput.value;

        // Show loading state
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnSpinner.style.display = 'inline-flex';

        try {
            const response = await fetch('/api/password/email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email })
            });

            const data = await response.json();

            if (response.ok) {
                // Show success message
                successMessage.textContent = data.message || 'Password reset link sent to your email.';
                successContainer.style.display = 'block';
                emailInput.value = '';
            } else {
                // Show error message
                errorMessage.textContent = data.message || 'Failed to send reset link. Please try again.';
                errorContainer.style.display = 'block';
            }

            // Reset button state
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnSpinner.style.display = 'none';

        } catch (error) {
            console.error('Forgot password error:', error);
            errorMessage.textContent = 'An error occurred. Please try again.';
            errorContainer.style.display = 'block';
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnSpinner.style.display = 'none';
        }
    });
});
</script>
@endsection
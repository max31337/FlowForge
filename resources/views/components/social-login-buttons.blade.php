<div class="flex items-center justify-center mt-4">
    <a href="{{ url('auth/google') }}" class="btn btn-outline d-inline-flex align-items-center justify-content-center me-2">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google Logo" class="h-5 me-2">
        {{ __('Log in with Google') }}
    </a>
    <a href="{{ url('auth/github') }}" class="btn btn-outline d-inline-flex align-items-center justify-content-center">
        <img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" alt="GitHub Logo" class="h-5 me-2">
        {{ __('Log in with GitHub') }}
    </a>
</div>

@extends('layouts.app')

@section('title', 'Login Debug')
@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">⚠️ Login Debug Information</h5>
                </div>
                <div class="card-body">
                    <h6>Session Information:</h6>
                    <ul>
                        <li>Session Driver: <code>{{ config('session.driver') }}</code></li>
                        <li>Session Lifetime: <code>{{ config('session.lifetime') }} minutes</code></li>
                        <li>Session Path: <code>{{ config('session.files') }}</code></li>
                        <li>HTTPS Only: <code>{{ config('session.secure') ? 'Yes' : 'No' }}</code></li>
                        <li>HTTP Only Cookies: <code>{{ config('session.http_only') ? 'Yes' : 'No' }}</code></li>
                        <li>Same Site: <code>{{ config('session.same_site') }}</code></li>
                    </ul>

                    <hr>

                    <h6>Disk Permissions:</h6>
                    <ul>
                        <li>Session folder exists: 
                            @if(is_dir(config('session.files')))
                                <span class="badge bg-success">✓ Yes</span>
                            @else
                                <span class="badge bg-danger">✗ No</span>
                            @endif
                        </li>
                        <li>Session folder writable:
                            @if(is_writable(config('session.files')))
                                <span class="badge bg-success">✓ Yes</span>
                            @else
                                <span class="badge bg-danger">✗ No</span>
                            @endif
                        </li>
                    </ul>

                    <hr>

                    <h6>Database Connection Test:</h6>
                    <div id="db-test">
                        <small class="text-muted">Testing database connection...</small>
                    </div>

                    <hr>

                    <a href="{{ route('login') }}" class="btn btn-primary">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Test database connection
    fetch('{{ route("login.debug") }}', { method: 'GET' })
        .then(response => response.json())
        .then(data => {
            const dbTest = document.getElementById('db-test');
            if (data.success) {
                dbTest.innerHTML = '<span class="badge bg-success">✓ Database connected</span>';
            } else {
                dbTest.innerHTML = '<span class="badge bg-danger">✗ Error: ' + data.error + '</span>';
            }
        })
        .catch(error => {
            document.getElementById('db-test').innerHTML = '<span class="badge bg-danger">✗ Error: ' + error.message + '</span>';
        });
</script>

@endsection

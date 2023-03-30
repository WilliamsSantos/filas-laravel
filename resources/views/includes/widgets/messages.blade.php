@if (session('upload_processed'))
    <div class="alert alert-success">{{ session('upload_processed') }}</div>
@endif
@if (session('success-info'))
    <div class="alert alert-info">{{ session('success-info') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'Error' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            padding: 20px;
            background-color: #f8f9fa;
        }
        .error-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container text-center">
        <div class="error-code">{{ $code ?? 'Error' }}</div>
        <h2>{{ $title ?? 'Something went wrong' }}</h2>
        <p class="lead">{{ $message ?? 'An unexpected error occurred.' }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-3">Return to Home</a>
    </div>
</body>
</html>

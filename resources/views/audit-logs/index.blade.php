@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0 page-title">Audit Trail</h1>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Event</th>
                            <th>Record</th>
                            <th>Changes</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $log->user?->full_name ?? 'System' }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($log->event) }}</span></td>
                                <td>
                                    {{ class_basename($log->auditable_type) }}
                                    @if($log->auditable_id)
                                        #{{ $log->auditable_id }}
                                    @endif
                                </td>
                                <td>
                                    @if($log->event === 'updated')
                                        <code>{{ implode(', ', array_keys($log->new_values ?? [])) }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No audit activity yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

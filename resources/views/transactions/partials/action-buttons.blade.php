<div class="btn-group btn-group-sm" role="group">
    <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-info" title="View">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-warning" title="Edit">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" title="Delete" onclick="return confirm('Delete this transaction?')">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
</div>

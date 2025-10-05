<div class="card mb-3">
    <div class="card-body">
        <form id="{{ $formId ?? 'task-form' }}" method="POST" action="{{ $action }}">
            @csrf
            @if(isset($task) && $task->id)
                @method('PATCH')
            @endif
            
            <div class="mb-2">
                <input required name="title" class="form-control" placeholder="Task title" 
                       value="{{ $task->title ?? old('title') }}">
                @error('title')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-2">
                <textarea name="description" class="form-control" placeholder="Task description (optional)">{{ $task->description ?? old('description') }}</textarea>
                @error('description')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">
                {{ $buttonText ?? (isset($task) && $task->id ? 'Update Task' : 'Create Task') }}
            </button>
            
            @if(isset($task) && $task->id)
                <button type="button" class="btn btn-secondary ms-2" onclick="cancelEdit()">Cancel</button>
            @endif
        </form>
    </div>
</div>

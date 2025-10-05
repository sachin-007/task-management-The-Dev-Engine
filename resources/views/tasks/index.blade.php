@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h3>Tasks</h3>
    </div>
    <div class="col-md-6 text-end">
        <form method="GET" action="{{ route('tasks.index') }}" class="d-inline">
            <select name="filter" onchange="this.form.submit()" class="form-select form-select-sm" style="width:auto; display:inline-block;">
                <option value="all" {{ ($filter ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                <option value="completed" {{ ($filter ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="incomplete" {{ ($filter ?? '') === 'incomplete' ? 'selected' : '' }}>Incomplete</option>
            </select>
        </form>
    </div>
</div>

<!-- Task Form (Create/Update) -->
<div id="task-form-container">
    @include('tasks.partials.task-form', [
        'action' => route('tasks.store'),
        'formId' => 'task-form',
        'buttonText' => 'Create Task'
    ])
            </div>

<!-- View Toggle Buttons -->
<div class="row mb-3">
    <div class="col-12 text-center">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" id="table-view-btn">
                <i class="fas fa-table"></i> Table View
            </button>
            <button type="button" class="btn btn-outline-primary" id="scroll-view-btn">
                <i class="fas fa-list"></i> Scroll View
            </button>
            </div>
    </div>
</div>

<!-- Task Table with DataTable -->
<div id="table-view" class="card">
    <div class="card-body">
        <div id="table-skeleton" class="skeleton-hidden">
            @include('tasks.partials.skeleton')
                    </div>
        <table id="tasks-table" class="table table-striped table-hover" style="width: 100% !important;">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="5%">Status</th>
                    <th width="40%">Title</th>
                    <th width="35%">Description</th>
                    <th width="15%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTable will populate this -->
            </tbody>
        </table>
                </div>
            </div>

<!-- Scroll View -->
@include('tasks.partials.scroll-view')
@endsection

@push('head')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<style>
/* Ensure DataTable is 100% width */
#tasks-table {
    width: 100% !important;
}

.dataTables_wrapper {
    width: 100% !important;
}

.dataTables_scrollHeadInner {
    width: 100% !important;
}

.dataTables_scrollBody {
    width: 100% !important;
}

/* Card body should also be full width */
.card-body {
    padding: 0;
}

#table-view .card-body {
    padding: 1rem;
}
</style>
@endpush

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let currentEditTaskId = null;
    let currentView = 'table'; // 'table' or 'scroll'
    let scrollPage = 1;
    let scrollLoading = false;
    let scrollHasMore = true;
    let scrollFilter = 'all';

    // Show skeleton loading
    function showSkeleton() {
        $('#table-skeleton').removeClass('skeleton-hidden');
        $('#tasks-table').hide();
    }

    // Hide skeleton loading
    function hideSkeleton() {
        $('#table-skeleton').addClass('skeleton-hidden');
        $('#tasks-table').show();
    }

    // Initialize DataTable with server-side processing
    const table = $('#tasks-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX: true,
        ajax: {
            url: '{{ route("api.tasks.index") }}',
            type: 'GET',
            data: function(d) {
                d.filter = '{{ $filter ?? "all" }}';
            },
            beforeSend: function() {
                showSkeleton();
            },
            complete: function() {
                hideSkeleton();
            }
        },
        columns: [
            { 
                data: 'order',
                name: 'order',
                orderable: true,
                searchable: false
            },
            { 
                data: 'completed',
                name: 'completed',
                orderable: true,
                searchable: false,
                render: function(data, type, row) {
                    return `<input type="checkbox" class="form-check-input task-complete-checkbox" 
                            data-update-url="/tasks/${row.id}" ${data ? 'checked' : ''}>`;
                }
            },
            { 
                data: 'title',
                name: 'title',
                render: function(data, type, row) {
                    const completed = row.completed ? 'text-decoration-line-through' : '';
                    return `<span class="${completed}">${data}</span>`;
                }
            },
            { 
                data: 'description',
                name: 'description',
                render: function(data, type, row) {
                    return data ? `<small class="text-muted">${data}</small>` : '';
                }
            },
            { 
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary btn-edit" 
                                    data-task-id="${data}" 
                                    data-task-title="${row.title}" 
                                    data-task-description="${row.description || ''}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete" 
                                    data-task-id="${data}" data-task-title="${row.title}">
                                Delete
                            </button>
                        </div>
                    `;
                }
            }
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        responsive: true,
        order: [[0, 'asc']],
        language: {
            processing: "Loading tasks...",
            emptyTable: "No tasks found",
            zeroRecords: "No matching tasks found"
        }
    });

    // Handle task completion toggle
    $(document).on('change', '.task-complete-checkbox', async function() {
        const url = $(this).data('update-url');
        const completed = $(this).is(':checked') ? 1 : 0;

        try {
            await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ completed: completed })
            });

            // Refresh the table
            table.ajax.reload();
        } catch (error) {
            console.error('Error updating task:', error);
            alert('Error updating task. Please try again.');
        }
    });

    // Handle edit button click
    $(document).on('click', '.btn-edit', function() {
        const taskId = $(this).data('task-id');
        const taskTitle = $(this).data('task-title');
        const taskDescription = $(this).data('task-description');
        
        currentEditTaskId = taskId;
        
        // Update form for editing
        const form = $('#task-form');
        form.attr('action', `/tasks/${taskId}`);
        form.find('input[name="title"]').val(taskTitle);
        form.find('textarea[name="description"]').val(taskDescription);
        form.find('button[type="submit"]').text('Update Task');
        
        // Add cancel button if not exists
        if (!form.find('.btn-cancel').length) {
            form.find('button[type="submit"]').after('<button type="button" class="btn btn-secondary ms-2 btn-cancel">Cancel</button>');
        }
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $('#task-form-container').offset().top - 100
        }, 500);
    });

    // Handle cancel edit
    $(document).on('click', '.btn-cancel', function() {
        cancelEdit();
    });

    function cancelEdit() {
        currentEditTaskId = null;
        const form = $('#task-form');
        form.attr('action', '{{ route("tasks.store") }}');
        form.find('input[name="title"]').val('');
        form.find('textarea[name="description"]').val('');
        form.find('button[type="submit"]').text('Create Task');
        form.find('.btn-cancel').remove();
    }

    // Handle delete button click
    $(document).on('click', '.btn-delete', function() {
        const taskId = $(this).data('task-id');
        const taskTitle = $(this).data('task-title');
        
        if (confirm('Are you sure you want to delete this task?')) {
            fetch(`/tasks/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                showAlert('Task '+ taskTitle +' deleted'+ ' successfully!', 'success');
                    table.ajax.reload();
                } else {
                    alert('Error deleting task. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error deleting task:', error);
                alert('Error deleting task. Please try again.');
            });
        }
    });

    // Handle form submission (create/update)
    $('#task-form').on('submit', async function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        const url = form.attr('action');
        const method = currentEditTaskId ? 'PATCH' : 'POST';
        const taskTitle = form.find('input[name="title"]').val();
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            if (response.ok) {
                // Clear form and reset to create mode
                cancelEdit();
                // Refresh table
                table.ajax.reload();
                // Show success message
                showAlert('Task '+ taskTitle +' '+ (currentEditTaskId ? 'updated' : 'created') + ' successfully!', 'success');
            } else {
                const errors = await response.json();
                showAlert('Error: ' + (errors.message || 'Please check your input'), 'danger');
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            showAlert('Error submitting form. Please try again.', 'danger');
        }
    });

    // Show alert function
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert
        $('.container').prepend(alertHtml);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 3000);
    }

    // View switching functionality
    $('#table-view-btn').on('click', function() {
        currentView = 'table';
        $('#table-view').show();
        $('#scroll-view-container').hide();
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        $('#scroll-view-btn').removeClass('btn-primary').addClass('btn-outline-primary');
    });

    $('#scroll-view-btn').on('click', function() {
        currentView = 'scroll';
        $('#table-view').hide();
        $('#scroll-view-container').show();
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        $('#table-view-btn').removeClass('btn-primary').addClass('btn-outline-primary');
        
        // Load initial scroll data if not loaded
        if ($('#scroll-tasks-list').children().length === 0) {
            loadScrollTasks();
        }
    });

    // Scroll view functionality
    function loadScrollTasks() {
        if (scrollLoading || !scrollHasMore) return;
        
        scrollLoading = true;
        $('#scroll-loading').show();
        
        fetch(`/api/tasks?page=${scrollPage}&per_page=10&filter=${scrollFilter}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.data && data.data.length > 0) {
                data.data.forEach(task => {
                    const taskHtml = createScrollTaskHtml(task);
                    $('#scroll-tasks-list').append(taskHtml);
                });
                scrollPage++;
            } else {
                scrollHasMore = false;
                $('#scroll-end-message').show();
            }
        })
        .catch(error => {
            console.error('Error loading scroll tasks:', error);
            showAlert('Error loading tasks', 'danger');
        })
        .finally(() => {
            scrollLoading = false;
            $('#scroll-loading').hide();
        });
    }

    function createScrollTaskHtml(task) {
        const completedClass = task.completed ? 'completed' : '';
        const completedIcon = task.completed ? 'fa-check-circle text-success' : 'fa-circle text-muted';
        
        return `
            <div class="scroll-task-item ${completedClass}" data-task-id="${task.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="task-title">${task.title}</div>
                        ${task.description ? `<div class="task-description">${task.description}</div>` : ''}
                        <div class="task-meta">
                            <span><i class="fas fa-sort-numeric-up"></i> Order: ${task.order}</span>
                            <span><i class="fas ${completedIcon}"></i> ${task.completed ? 'Completed' : 'Pending'}</span>
                        </div>
                    </div>
                    <div class="task-actions">
                        <button class="btn btn-sm btn-outline-primary btn-scroll-edit" data-task-id="${task.id}" data-task-title="${task.title}" data-task-description="${task.description || ''}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-success btn-scroll-toggle" data-task-id="${task.id}">
                            <i class="fas ${task.completed ? 'fa-undo' : 'fa-check'}"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-scroll-delete" data-task-id="${task.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Scroll view event handlers
    $(document).on('click', '.btn-scroll-edit', function() {
        const taskId = $(this).data('task-id');
        const taskTitle = $(this).data('task-title');
        const taskDescription = $(this).data('task-description');
        
        currentEditTaskId = taskId;
        
        // Update form for editing
        const form = $('#task-form');
        form.attr('action', `/tasks/${taskId}`);
        form.find('input[name="title"]').val(taskTitle);
        form.find('textarea[name="description"]').val(taskDescription);
        form.find('button[type="submit"]').text('Update Task');
        
        // Add cancel button if not exists
        if (!form.find('.btn-cancel').length) {
            form.find('button[type="submit"]').after('<button type="button" class="btn btn-secondary ms-2 btn-cancel">Cancel</button>');
        }
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $('#task-form-container').offset().top - 100
        }, 500);
    });

    $(document).on('click', '.btn-scroll-toggle', function() {
        const taskId = $(this).data('task-id');
        const taskItem = $(this).closest('.scroll-task-item');
        const isCompleted = taskItem.hasClass('completed');
        
        fetch(`/tasks/${taskId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ completed: !isCompleted })
        })
        .then(response => {
            if (response.ok) {
                // Update UI
                if (isCompleted) {
                    taskItem.removeClass('completed');
                    taskItem.find('.task-title').removeClass('text-decoration-line-through text-muted');
                    taskItem.find('.fa-check-circle').removeClass('fa-check-circle text-success').addClass('fa-circle text-muted');
                    taskItem.find('.btn-scroll-toggle i').removeClass('fa-undo').addClass('fa-check');
                    taskItem.find('.task-meta span:last-child').html('<i class="fas fa-circle text-muted"></i> Pending');
                } else {
                    taskItem.addClass('completed');
                    taskItem.find('.task-title').addClass('text-decoration-line-through text-muted');
                    taskItem.find('.fa-circle').removeClass('fa-circle text-muted').addClass('fa-check-circle text-success');
                    taskItem.find('.btn-scroll-toggle i').removeClass('fa-check').addClass('fa-undo');
                    taskItem.find('.task-meta span:last-child').html('<i class="fas fa-check-circle text-success"></i> Completed');
                }
            }
        })
        .catch(error => {
            console.error('Error toggling task:', error);
            showAlert('Error updating task', 'danger');
        });
    });

    $(document).on('click', '.btn-scroll-delete', function() {
        const taskId = $(this).data('task-id');
        
        if (confirm('Are you sure you want to delete this task?')) {
            fetch(`/tasks/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    $(this).closest('.scroll-task-item').fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    showAlert('Error deleting task', 'danger');
                }
            })
            .catch(error => {
                console.error('Error deleting task:', error);
                showAlert('Error deleting task', 'danger');
            });
        }
    });

    // Scroll view sorting
    $('#sort-all').on('click', function() {
        scrollFilter = 'all';
        resetScrollView();
    });

    $('#sort-completed').on('click', function() {
        scrollFilter = 'completed';
        resetScrollView();
    });

    $('#sort-incomplete').on('click', function() {
        scrollFilter = 'incomplete';
        resetScrollView();
    });

    function resetScrollView() {
        scrollPage = 1;
        scrollHasMore = true;
        scrollLoading = false;
        $('#scroll-tasks-list').empty();
        $('#scroll-end-message').hide();
        loadScrollTasks();
    }

    // Infinite scroll for scroll view
    $('#scroll-view-container').on('scroll', function() {
        const container = $(this);
        const scrollTop = container.scrollTop();
        const scrollHeight = container[0].scrollHeight;
        const clientHeight = container[0].clientHeight;
        
        // Load more when scrolled to bottom
        if (scrollTop + clientHeight >= scrollHeight - 100 && !scrollLoading && scrollHasMore) {
            loadScrollTasks();
        }
    });
</script>
@endpush

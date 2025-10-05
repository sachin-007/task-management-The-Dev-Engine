<div id="scroll-view-container" class="scroll-view-container" style="display: none;">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4>Tasks (Scroll View)</h4>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm" id="sort-all">All</button>
                <button type="button" class="btn btn-outline-success btn-sm" id="sort-completed">Completed</button>
                <button type="button" class="btn btn-outline-warning btn-sm" id="sort-incomplete">Incomplete</button>
            </div>
        </div>
    </div>
    
    <div id="scroll-tasks-list" class="scroll-tasks-list">
        <!-- Tasks will be loaded here with lazy loading -->
    </div>
    
    <div id="scroll-loading" class="text-center mt-3" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading more tasks...</p>
    </div>
    
    <div id="scroll-end-message" class="text-center mt-3 text-muted" style="display: none;">
        <p>No more tasks to load</p>
    </div>
</div>

<style>
.scroll-view-container {
    max-height: 600px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
}

.scroll-tasks-list {
    min-height: 200px;
}

.scroll-task-item {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.scroll-task-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.scroll-task-item.completed {
    background: #f8f9fa;
    opacity: 0.8;
}

.scroll-task-item.completed .task-title {
    text-decoration: line-through;
    color: #6c757d;
}

.task-title {
    font-weight: 600;
    margin-bottom: 5px;
    color: #212529;
}

.task-description {
    color: #6c757d;
    font-size: 0.9em;
    margin-bottom: 10px;
}

.task-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8em;
    color: #6c757d;
}

.task-actions {
    display: flex;
    gap: 5px;
}

.task-actions .btn {
    padding: 2px 8px;
    font-size: 0.75em;
}

/* Custom scrollbar */
.scroll-view-container::-webkit-scrollbar {
    width: 8px;
}

.scroll-view-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.scroll-view-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.scroll-view-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

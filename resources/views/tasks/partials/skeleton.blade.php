<div class="skeleton-container">
    <div class="skeleton-card mb-3">
        <div class="skeleton-line skeleton-title"></div>
        <div class="skeleton-line skeleton-description"></div>
        <div class="skeleton-line skeleton-description-short"></div>
    </div>
    
    <div class="skeleton-card mb-3">
        <div class="skeleton-line skeleton-title"></div>
        <div class="skeleton-line skeleton-description"></div>
        <div class="skeleton-line skeleton-description-short"></div>
    </div>
    
    <div class="skeleton-card mb-3">
        <div class="skeleton-line skeleton-title"></div>
        <div class="skeleton-line skeleton-description"></div>
        <div class="skeleton-line skeleton-description-short"></div>
    </div>
    
    <div class="skeleton-card mb-3">
        <div class="skeleton-line skeleton-title"></div>
        <div class="skeleton-line skeleton-description"></div>
        <div class="skeleton-line skeleton-description-short"></div>
    </div>
    
    <div class="skeleton-card mb-3">
        <div class="skeleton-line skeleton-title"></div>
        <div class="skeleton-line skeleton-description"></div>
        <div class="skeleton-line skeleton-description-short"></div>
    </div>
</div>

<style>
.skeleton-container {
    animation: fadeIn 0.3s ease-in;
}

.skeleton-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
}

.skeleton-line {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 4px;
    margin-bottom: 10px;
    height: 20px;
}

.skeleton-title {
    width: 60%;
    height: 24px;
}

.skeleton-description {
    width: 100%;
    height: 18px;
}

.skeleton-description-short {
    width: 40%;
    height: 18px;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.skeleton-hidden {
    display: none;
}
</style>

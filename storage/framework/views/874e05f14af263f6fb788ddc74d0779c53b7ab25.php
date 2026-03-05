<?php $__env->startSection('content'); ?>

<!-- Page Header -->
<div class="page-header">
    <div class="header-content">
        <h1 class="page-title">
            <i class="fas fa-calendar-alt"></i>
            Room Timetables
        </h1>
        <p class="page-subtitle">Select a room to view and manage its schedule</p>
    </div>
    <div class="header-actions">
        <button onclick="showAllQRCodes()" class="btn btn-info mr-2">
            <i class="fas fa-qrcode mr-1"></i>
            All QR Codes
        </button>
        <button onclick="printAllRoomTimetables()" class="btn btn-success btn-print">
            <i class="fas fa-print mr-1"></i>
            Print All Timetables
        </button>
    </div>
</div>

<!-- Rooms Grid with Integrated Filters -->
<div class="rooms-container">
    <!-- Integrated Filter Section -->
    <div class="integrated-filter-section">
        <div class="filter-row">
            <div class="filter-column">
                <div class="filter-controls">
                    <label class="filter-label">
                        <i class="fas fa-filter mr-1"></i>
                        Filter by Room Type:
                    </label>
                    <div class="filter-buttons">
                        <button type="button" class="filter-btn active" data-filter="all">
                            <i class="fas fa-th mr-1"></i>
                            All Rooms
                            <span class="badge badge-light"><?php echo e($rooms->count()); ?></span>
                        </button>
                        <button type="button" class="filter-btn" data-filter="classroom">
                            <i class="fas fa-door-open mr-1"></i>
                            Classrooms
                            <span class="badge badge-primary"><?php echo e($rooms->where('is_lab', false)->count()); ?></span>
                        </button>
                        <button type="button" class="filter-btn" data-filter="laboratory">
                            <i class="fas fa-flask mr-1"></i>
                            Laboratories
                            <span class="badge badge-info"><?php echo e($rooms->where('is_lab', true)->count()); ?></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="search-column">
                <div class="search-controls">
                    <div class="input-group">
                        <input type="text" class="form-control" id="roomSearch" placeholder="Search rooms...">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="rooms-grid" id="roomsContainer">
        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="room-card room-item" 
                 data-room-type="<?php echo e($room->is_lab ? 'laboratory' : 'classroom'); ?>" 
                 data-room-name="<?php echo e(strtolower($room->name)); ?>" 
                 data-room-description="<?php echo e(strtolower($room->description ?? '')); ?>">
                <div class="room-header">
                    <div class="room-type-badge">
                        <?php if($room->is_lab): ?>
                            <span class="badge badge-info">
                                <i class="fas fa-flask mr-1"></i>
                                Laboratory
                            </span>
                        <?php else: ?>
                            <span class="badge badge-primary">
                                <i class="fas fa-door-open mr-1"></i>
                                Classroom
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="room-header-actions">
                        <button onclick="showQRCode(<?php echo e($room->id); ?>, '<?php echo e($room->name); ?>')" class="btn btn-outline-success btn-sm" title="View QR Code">
                            <i class="fas fa-qrcode"></i>
                        </button>
                    </div>
                </div>
                
                <div class="room-content">
                    <h3 class="room-title"><?php echo e($room->name); ?></h3>
                    
                    <?php if($room->description): ?>
                        <p class="room-description"><?php echo e($room->description); ?></p>
                    <?php endif; ?>
                    
                    <div class="room-details">
                        <?php if($room->capacity): ?>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span><?php echo e($room->capacity); ?> capacity</span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($room->has_equipment): ?>
                            <div class="detail-item">
                                <i class="fas fa-tools"></i>
                                <span>Equipment Available</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="room-footer">
                    <a href="<?php echo e(route('admin.room-management.room-timetables.show', $room->id)); ?>" class="btn btn-primary btn-view-timetable">
                        <i class="fas fa-calendar-check mr-1"></i>
                        View Timetable
                    </a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partials.qr-code-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('scripts'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('scripts'); ?>

<style>
/* Room Header with QR Button */
.room-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.room-header-actions {
    margin-left: auto;
}

.room-header-actions .btn {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 4px;
}

.room-header-actions .btn:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .room-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .room-header-actions {
        margin-left: 0;
        margin-top: 8px;
    }
}
</style>
<script>
$(document).ready(function() {
    // Room filtering functionality
    $('.filter-btn').click(function() {
        // Update active state
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Get filter value
        var filter = $(this).data('filter');
        
        // Filter rooms
        filterRooms(filter);
    });
    
    // Search functionality
    $('#roomSearch').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        searchRooms(searchTerm);
    });
    
    function filterRooms(filter) {
        $('.room-item').each(function() {
            var roomType = $(this).data('room-type');
            
            if (filter === 'all' || roomType === filter) {
                $(this).fadeIn(300);
            } else {
                $(this).fadeOut(300);
            }
        });
        
        // Update results count
        updateResultsCount();
    }
    
    function searchRooms(searchTerm) {
        if (searchTerm === '') {
            // If search is empty, show all rooms based on current filter
            var activeFilter = $('.filter-btn.active').data('filter');
            filterRooms(activeFilter);
            return;
        }
        
        $('.room-item').each(function() {
            var roomName = $(this).data('room-name');
            var roomDescription = $(this).data('room-description');
            var searchableText = roomName + ' ' + roomDescription;
            
            if (searchableText.includes(searchTerm)) {
                $(this).fadeIn(300);
            } else {
                $(this).fadeOut(300);
            }
        });
        
        // Update results count
        updateResultsCount();
    }
    
    function updateResultsCount() {
        var visibleCount = $('.room-item:visible').length;
        var totalCount = $('.room-item').length;
        
        // Show/hide no results message
        if (visibleCount === 0) {
            if ($('#noResultsMessage').length === 0) {
                $('#roomsContainer').append(`
                    <div class="col-12" id="noResultsMessage">
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Rooms Found</h5>
                            <p class="text-muted">Try adjusting your search or filter criteria.</p>
                            <button type="button" class="btn btn-outline-primary" onclick="clearFilters()">
                                <i class="fas fa-times mr-1"></i>
                                Clear Filters
                            </button>
                        </div>
                    </div>
                `);
            }
            $('#noResultsMessage').show();
        } else {
            $('#noResultsMessage').hide();
        }
    }
    
    // Clear filters function
    window.clearFilters = function() {
        $('#roomSearch').val('');
        $('.filter-btn').removeClass('active');
        $('.filter-btn[data-filter="all"]').addClass('active');
        $('.room-item').fadeIn(300);
        updateResultsCount();
    };
    
    // Initialize results count
    updateResultsCount();
});

function printAllRoomTimetables() {
    // Get all visible room URLs (respecting current filter)
    const visibleRoomItems = $('.room-item:visible');
    const roomUrls = [];
    
        visibleRoomItems.each(function() {
            const roomId = $(this).find('.btn-view-timetable').attr('href').match(/\/(\d+)$/)[1];
            roomUrls.push('<?php echo e(url('/admin/room-management/room-timetables')); ?>/' + roomId);
        });
    
    if (roomUrls.length === 0) {
        alert('No rooms to print. Please adjust your filters.');
        return;
    }
    
    // Open each room timetable in a new window and print
    roomUrls.forEach((url, index) => {
        setTimeout(() => {
            const printWindow = window.open(url, '_blank');
            printWindow.onload = function() {
                printWindow.print();
                // Close the window after a short delay
                setTimeout(() => {
                    printWindow.close();
                }, 1000);
            };
        }, index * 2000); // 2 second delay between each print
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/admin/room-timetable/index.blade.php ENDPATH**/ ?>
<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="qrCodeModalLabel">
                    <i class="fas fa-qrcode mr-2"></i>
                    <span id="qrModalRoomName">Room QR Code</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Loading State -->
                <div id="qrLoadingState" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                    <p class="mt-2 text-muted">Generating QR Code...</p>
                </div>

                <!-- QR Code Content -->
                <div id="qrCodeContent" style="display: none;">
                    <!-- Room Information -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-door-open text-primary mr-2"></i>
                                        Room Information
                                    </h6>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p class="mb-1"><strong>Room:</strong> <span id="qrRoomName">-</span></p>
                                            <p class="mb-1"><strong>Type:</strong> <span id="qrRoomType">-</span></p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="mb-1"><strong>Capacity:</strong> <span id="qrRoomCapacity">-</span></p>
                                            <p class="mb-1"><strong>Description:</strong> <span id="qrRoomDescription">-</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <!-- QR Code Image -->
                            <div class="qr-code-container">
                                <img id="qrCodeImage" src="" alt="Room QR Code" class="img-fluid border rounded" style="max-width: 200px;">
                                <div id="qrServiceStatus" class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        <span id="qrServiceName">Generating...</span>
                                    </small>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">Scan to view timetable</small>
                        </div>
                    </div>

                    <!-- QR Code Details -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle text-info mr-2"></i>
                                        QR Code Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Public URL:</strong></p>
                                            <div class="input-group">
                                                <input type="text" id="qrPublicUrl" class="form-control form-control-sm" readonly>
                                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyToClipboard('qrPublicUrl')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>QR Code Image URL:</strong></p>
                                            <div class="input-group">
                                                <input type="text" id="qrImageUrl" class="form-control form-control-sm" readonly>
                                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyToClipboard('qrImageUrl')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Instructions -->
                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-lightbulb mr-2"></i>
                            How to Use This QR Code
                        </h6>
                        <ul class="mb-0">
                            <li>Print this QR code and display it in the room</li>
                            <li>Anyone can scan it with their smartphone to view the room timetable</li>
                            <li>No login or authentication required</li>
                            <li>The timetable updates automatically when schedules change</li>
                        </ul>
                    </div>
                </div>

                <!-- Error State -->
                <div id="qrErrorState" class="alert alert-danger" style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span id="qrErrorMessage">Failed to generate QR code. Please try again.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="printQRCode" onclick="printQRCode()">
                    <i class="fas fa-print mr-1"></i>
                    Print QR Code
                </button>
                <button type="button" class="btn btn-success" id="downloadQRCode" onclick="downloadQRCode()">
                    <i class="fas fa-download mr-1"></i>
                    Download QR Code
                </button>
            </div>
        </div>
    </div>
</div>

<!-- All Rooms QR Codes Modal -->
<div class="modal fade" id="allQRCodesModal" tabindex="-1" role="dialog" aria-labelledby="allQRCodesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="allQRCodesModalLabel">
                    <i class="fas fa-qrcode mr-2"></i>
                    All Room QR Codes
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Loading State -->
                <div id="allQRLoadingState" class="text-center py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                    <p class="mt-2 text-muted">Generating QR Codes for All Rooms...</p>
                </div>

                <!-- All QR Codes Content -->
                <div id="allQRCodesContent" style="display: none;">
                    <div class="row" id="allQRCodesGrid">
                        <!-- QR codes will be populated here -->
                    </div>
                </div>

                <!-- Error State -->
                <div id="allQRErrorState" class="alert alert-danger" style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span id="allQRErrorMessage">Failed to generate QR codes. Please try again.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>
                    Close
                </button>
                <button type="button" class="btn btn-success" id="printAllQRCodes" onclick="printAllQRCodes()">
                    <i class="fas fa-print mr-1"></i>
                    Print All QR Codes
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.qr-code-container {
    position: relative;
    display: inline-block;
}

/* Removed blocking icon overlay */

/* Print styles */
@media print {
    .modal-dialog {
        max-width: none !important;
        margin: 0 !important;
    }
    
    .modal-content {
        border: none !important;
        box-shadow: none !important;
    }
    
    .modal-header,
    .modal-footer {
        display: none !important;
    }
    
    .modal-body {
        padding: 0 !important;
    }
    
    /* QR code overlay removed - no longer needed */
}

/* QR Code Grid Styles */
.qr-code-item {
    text-align: center;
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #f8f9fa;
}

.qr-code-item img {
    max-width: 150px;
    height: auto;
}

.qr-code-item h6 {
    margin-top: 10px;
    margin-bottom: 5px;
    color: #495057;
}

.qr-code-item small {
    color: #6c757d;
}
</style>

<script>
// QR Code Modal Functions with Enhanced Error Handling
function showQRCode(roomId, roomName) {
    $('#qrModalRoomName').text(roomName + ' - QR Code');
    $('#qrCodeModal').modal('show');
    
    // Show loading state
    $('#qrLoadingState').show();
    $('#qrCodeContent').hide();
    $('#qrErrorState').hide();
    
    // Determine which route to use based on current page
    let qrCodeUrl;
    if (window.location.pathname.includes('/room-timetables')) {
        qrCodeUrl = `/admin/room-management/room-timetables/${roomId}/qr-code`;
    } else {
        qrCodeUrl = `/admin/room-management/rooms/${roomId}/qr-code`;
    }
    
    // Fetch QR code data with retry mechanism
    fetchQRCodeData(qrCodeUrl, 0);
}

function fetchQRCodeData(url, retryCount) {
    const maxRetries = 3;
    
    $.get(url)
        .done(function(data) {
            populateQRModal(data);
        })
        .fail(function(xhr) {
            if (retryCount < maxRetries) {
                // Retry after a short delay
                setTimeout(() => {
                    fetchQRCodeData(url, retryCount + 1);
                }, 1000 * (retryCount + 1)); // Progressive delay: 1s, 2s, 3s
                
                // Show retry message
                showQRError(`Attempting to load QR code... (${retryCount + 1}/${maxRetries})`);
            } else {
                // Final error after all retries
                let errorMessage = 'Failed to load QR code after multiple attempts.';
                if (xhr.responseJSON?.message) {
                    errorMessage += ' ' + xhr.responseJSON.message;
                } else if (xhr.status === 0) {
                    errorMessage += ' Network connection error.';
                } else if (xhr.status >= 500) {
                    errorMessage += ' Server error. Please try again later.';
                }
                showQRError(errorMessage);
            }
        });
}

function populateQRModal(data) {
    $('#qrRoomName').text(data.room.name);
    
    // Determine room type based on is_lab field
    const roomType = data.room.is_lab ? 'Laboratory' : 'Classroom';
    $('#qrRoomType').text(roomType);
    
    $('#qrRoomCapacity').text(data.room.capacity || 'Not specified');
    
    $('#qrRoomDescription').text(data.room.description || 'No description');
    
    // Set QR code image with error handling
    const qrImage = $('#qrCodeImage');
    qrImage.attr('src', data.qr_image_url);
    
    // Determine and display which service is being used
    const qrImageUrl = data.qr_image_url;
    let serviceName = 'Unknown Service';
    let serviceIcon = 'fas fa-question-circle';
    
    if (qrImageUrl.includes('quickchart.io')) {
        serviceName = 'QuickChart API (Primary)';
        serviceIcon = 'fas fa-check-circle text-success';
    } else if (qrImageUrl.includes('chart.googleapis.com')) {
        serviceName = 'Google Charts API (Fallback)';
        serviceIcon = 'fas fa-exclamation-triangle text-warning';
    } else if (qrImageUrl.includes('api.qrserver.com')) {
        serviceName = 'QR Server API (Fallback)';
        serviceIcon = 'fas fa-exclamation-triangle text-warning';
    }
    
    $('#qrServiceName').html(`<i class="${serviceIcon}"></i> ${serviceName}`);
    
    // Handle QR image load errors
    qrImage.on('error', function() {
        console.error('QR Code image failed to load:', data.qr_image_url);
        $('#qrServiceName').html('<i class="fas fa-times-circle text-danger"></i> Failed to load');
        showQRError('QR code image failed to load. The URL may be invalid or the service may be temporarily unavailable.');
    });
    
    qrImage.on('load', function() {
        console.log('QR Code image loaded successfully');
    });
    
    $('#qrPublicUrl').val(data.public_url);
    $('#qrImageUrl').val(data.qr_image_url);
    
    // Hide loading, show content
    $('#qrLoadingState').hide();
    $('#qrCodeContent').show();
}

function showQRError(message) {
    $('#qrErrorMessage').text(message);
    $('#qrLoadingState').hide();
    $('#qrCodeContent').hide();
    $('#qrErrorState').show();
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    
    // Modern clipboard API
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(element.value).then(() => {
            showCopySuccess(elementId);
        }).catch(() => {
            // Fallback to old method
            fallbackCopyToClipboard(element);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyToClipboard(element);
    }
}

function fallbackCopyToClipboard(element) {
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(element.id);
        } else {
            showCopyError(element.id);
        }
    } catch (err) {
        showCopyError(element.id);
    }
}

function showCopySuccess(elementId) {
    const button = document.getElementById(elementId).nextElementSibling;
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check text-success"></i> Copied!';
    button.classList.add('btn-success');
    button.classList.remove('btn-outline-secondary');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}

function showCopyError(elementId) {
    const button = document.getElementById(elementId).nextElementSibling;
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-times text-danger"></i> Failed';
    button.classList.add('btn-danger');
    button.classList.remove('btn-outline-secondary');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-danger');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}

function printQRCode() {
    // Add print-specific styling
    const printWindow = window.open('', '_blank');
    const qrImageUrl = $('#qrImageUrl').val();
    const roomName = $('#qrRoomName').text();
    const publicUrl = $('#qrPublicUrl').val();
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${roomName} - QR Code</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
                .qr-container { margin: 20px 0; }
                .qr-image { max-width: 300px; height: auto; border: 2px solid #000; }
                .room-info { margin: 20px 0; }
                .url-info { margin: 20px 0; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <h1>${roomName}</h1>
            <div class="room-info">
                <p>Room QR Code for Public Timetable Access</p>
            </div>
            <div class="qr-container">
                <img src="${qrImageUrl}" alt="${roomName} QR Code" class="qr-image">
            </div>
            <div class="url-info">
                <p>Scan this QR code to view the room timetable</p>
                <p>URL: ${publicUrl}</p>
                <p>Generated on: ${new Date().toLocaleString()}</p>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

function downloadQRCode() {
    const qrImageUrl = $('#qrImageUrl').val();
    const roomName = $('#qrRoomName').text().replace(/[^a-zA-Z0-9]/g, '_');
    
    if (qrImageUrl) {
        // Create a temporary link element
        const link = document.createElement('a');
        link.href = qrImageUrl;
        link.download = `${roomName}_QR_Code.png`;
        link.target = '_blank';
        
        // Add to DOM, click, and remove
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success message
        showDownloadSuccess();
    } else {
        showDownloadError();
    }
}

function showDownloadSuccess() {
    const button = document.getElementById('downloadQRCode');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Downloaded!';
    button.classList.add('btn-success');
    button.classList.remove('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-success');
    }, 2000);
}

function showDownloadError() {
    const button = document.getElementById('downloadQRCode');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-times"></i> Failed';
    button.classList.add('btn-danger');
    button.classList.remove('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-danger');
        button.classList.add('btn-success');
    }, 2000);
}

// All QR Codes Modal Functions with Enhanced Error Handling
function showAllQRCodes() {
    $('#allQRCodesModal').modal('show');
    
    // Show loading state
    $('#allQRLoadingState').show();
    $('#allQRCodesContent').hide();
    $('#allQRErrorState').hide();
    
    // Determine which route to use based on current page
    let allQRCodesUrl;
    if (window.location.pathname.includes('/room-timetables')) {
        allQRCodesUrl = '/admin/room-management/room-timetables/qr-codes/all';
    } else {
        allQRCodesUrl = '/admin/room-management/rooms/qr-codes/all';
    }
    
    // Fetch all QR codes with retry mechanism
    fetchAllQRCodesData(allQRCodesUrl, 0);
}

function fetchAllQRCodesData(url, retryCount) {
    const maxRetries = 3;
    
    $.get(url)
        .done(function(data) {
            populateAllQRCodes(data);
        })
        .fail(function(xhr) {
            if (retryCount < maxRetries) {
                // Retry after a short delay
                setTimeout(() => {
                    fetchAllQRCodesData(url, retryCount + 1);
                }, 1000 * (retryCount + 1));
                
                // Show retry message
                showAllQRError(`Loading QR codes... (${retryCount + 1}/${maxRetries})`);
            } else {
                // Final error after all retries
                let errorMessage = 'Failed to load QR codes after multiple attempts.';
                if (xhr.responseJSON?.message) {
                    errorMessage += ' ' + xhr.responseJSON.message;
                }
                showAllQRError(errorMessage);
            }
        });
}

function populateAllQRCodes(qrCodesData) {
    const grid = $('#allQRCodesGrid');
    grid.empty();
    
    if (qrCodesData.length === 0) {
        grid.html('<div class="col-12 text-center text-muted"><p>No rooms found.</p></div>');
    } else {
    qrCodesData.forEach(function(qrData) {
        // Determine room type based on is_lab field
        const roomType = qrData.room.is_lab ? 'Laboratory' : 'Classroom';
        
        const qrItem = `
                <div class="col-md-4 col-lg-3 mb-3">
                <div class="qr-code-item">
                        <img src="${qrData.qr_image_url}" 
                             alt="${qrData.room.name} QR Code" 
                             class="img-fluid"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkVycm9yPC90ZXh0Pjwvc3ZnPg=='">
                    <h6>${qrData.room.name}</h6>
                        <small>${roomType} • ${qrData.room.capacity || 'N/A'} capacity</small>
                    </div>
            </div>
        `;
        grid.append(qrItem);
    });
    }
    
    // Hide loading, show content
    $('#allQRLoadingState').hide();
    $('#allQRCodesContent').show();
}

function showAllQRError(message) {
    $('#allQRErrorMessage').text(message);
    $('#allQRLoadingState').hide();
    $('#allQRCodesContent').hide();
    $('#allQRErrorState').show();
}

function printAllQRCodes() {
    const printWindow = window.open('', '_blank');
    const qrItems = $('#allQRCodesGrid').html();
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>All Room QR Codes</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .qr-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
                .qr-item { text-align: center; border: 1px solid #ddd; padding: 10px; }
                .qr-image { max-width: 150px; height: auto; }
                h1 { text-align: center; margin-bottom: 30px; }
            </style>
        </head>
        <body>
            <h1>All Room QR Codes</h1>
            <div class="qr-grid">
                ${qrItems.replace(/col-md-4 col-lg-3 mb-3/g, 'qr-item')}
            </div>
            <p style="text-align: center; margin-top: 30px; color: #666;">
                Generated on: ${new Date().toLocaleString()}
            </p>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+P for print
    if (e.ctrlKey && e.key === 'p' && $('#qrCodeModal').hasClass('show')) {
        e.preventDefault();
        printQRCode();
    }
    
    // Escape to close modal
    if (e.key === 'Escape') {
        if ($('#qrCodeModal').hasClass('show')) {
            $('#qrCodeModal').modal('hide');
        }
        if ($('#allQRCodesModal').hasClass('show')) {
            $('#allQRCodesModal').modal('hide');
        }
    }
});

// Add modal event listeners for better UX
$(document).ready(function() {
    $('#qrCodeModal').on('shown.bs.modal', function() {
        // Focus on the QR code image for accessibility
        $('#qrCodeImage').focus();
    });
    
    $('#qrCodeModal').on('hidden.bs.modal', function() {
        // Reset modal state
        $('#qrLoadingState').show();
        $('#qrCodeContent').hide();
        $('#qrErrorState').hide();
    });
    
    // Ensure modal works with Bootstrap 4
    $('#qrCodeModal').modal({
        backdrop: true,
        keyboard: true,
        focus: true,
        show: false
    });
    
    $('#allQRCodesModal').modal({
        backdrop: true,
        keyboard: true,
        focus: true,
        show: false
    });
});
</script>

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


# Fix localhost IPv6/IPv4 issue for Testsprite
# Run this script as Administrator

$hostsPath = "$env:SystemRoot\System32\drivers\etc\hosts"
$backupPath = "$hostsPath.backup.$(Get-Date -Format 'yyyyMMdd-HHmmss')"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Testsprite Localhost Fix Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if running as administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    exit 1
}

# Backup hosts file
try {
    Copy-Item $hostsPath $backupPath -Force
    Write-Host "✓ Backup created: $backupPath" -ForegroundColor Green
} catch {
    Write-Host "✗ Failed to create backup: $_" -ForegroundColor Red
    exit 1
}

# Read current content
try {
    $content = Get-Content $hostsPath -ErrorAction Stop
} catch {
    Write-Host "✗ Failed to read hosts file: $_" -ForegroundColor Red
    exit 1
}

# Check if IPv4 entry exists
$hasIPv4 = $content | Select-String -Pattern "^\s*127\.0\.0\.1\s+localhost" -Quiet

# Check if IPv6 entry exists and is not commented
$hasIPv6Active = $content | Select-String -Pattern "^\s*::1\s+localhost" -Quiet

$newContent = @()
$changesMade = $false

# Add IPv4 entry if missing
if (-not $hasIPv4) {
    $newContent += "127.0.0.1    localhost"
    Write-Host "✓ Adding IPv4 localhost entry..." -ForegroundColor Yellow
    $changesMade = $true
} else {
    Write-Host "✓ IPv4 localhost entry already exists" -ForegroundColor Green
}

# Process existing content
foreach ($line in $content) {
    # Comment out active IPv6 localhost entries
    if ($line -match "^\s*::1\s+localhost" -and -not $line.TrimStart().StartsWith("#")) {
        $newContent += "#$line  # Commented for Testsprite compatibility"
        Write-Host "✓ Commenting out IPv6 entry: $($line.Trim())" -ForegroundColor Yellow
        $changesMade = $true
    } elseif ($line -match "^\s*127\.0\.0\.1\s+localhost") {
        # Keep IPv4 entry as-is
        $newContent += $line
    } else {
        $newContent += $line
    }
}

if ($changesMade) {
    # Write updated content
    try {
        $newContent | Set-Content $hostsPath -Force
        Write-Host "✓ Hosts file updated successfully!" -ForegroundColor Green
    } catch {
        Write-Host "✗ Failed to write hosts file: $_" -ForegroundColor Red
        Write-Host "Restoring from backup..." -ForegroundColor Yellow
        Copy-Item $backupPath $hostsPath -Force
        exit 1
    }
} else {
    Write-Host "✓ No changes needed - hosts file is already configured correctly" -ForegroundColor Green
}

# Flush DNS
Write-Host ""
Write-Host "Flushing DNS cache..." -ForegroundColor Cyan
ipconfig /flushdns | Out-Null
Write-Host "✓ DNS cache flushed!" -ForegroundColor Green

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Fix Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Close and restart Cursor IDE" -ForegroundColor White
Write-Host "2. Ensure Laravel server is running: php artisan serve --host=0.0.0.0 --port=8000" -ForegroundColor White
Write-Host "3. Run Testsprite tests again" -ForegroundColor White
Write-Host ""

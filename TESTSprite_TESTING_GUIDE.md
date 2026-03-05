# Testsprite Testing Guide - Step by Step

## Problem Summary
Testsprite is trying to connect to `localhost` which resolves to IPv6 (`::1`) on Windows, but the PHP server only listens on IPv4 (`127.0.0.1`). This prevents Testsprite from establishing a connection.

## Solution Approaches (Choose One)

### ✅ **Approach 1: Force Node.js to Use IPv4 (Recommended - Easiest)**

This approach forces Node.js to prefer IPv4 when resolving hostnames.

#### Step 1: Set Environment Variable
Open PowerShell as Administrator and run:
```powershell
[System.Environment]::SetEnvironmentVariable("NODE_OPTIONS", "--dns-result-order=ipv4first", "User")
```

#### Step 2: Restart Your Terminal/Cursor
Close and reopen Cursor IDE to apply the environment variable.

#### Step 3: Verify Server is Running
```powershell
cd C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar
php artisan serve --host=0.0.0.0 --port=8000
```

#### Step 4: Run Testsprite Tests
The tests should now work. If not, proceed to Approach 2.

---

### ✅ **Approach 2: Modify Windows Hosts File**

This forces `localhost` to resolve to `127.0.0.1` (IPv4) instead of `::1` (IPv6).

#### Step 1: Open Hosts File
1. Press `Win + R`
2. Type: `notepad C:\Windows\System32\drivers\etc\hosts`
3. Click "Yes" when prompted for administrator privileges

#### Step 2: Add IPv4 Entry
Add this line at the top of the file (if not already present):
```
127.0.0.1    localhost
```

#### Step 3: Comment Out IPv6 Entry (if present)
If you see a line like:
```
::1         localhost
```
Comment it out by adding `#`:
```
#::1         localhost
```

#### Step 4: Save and Close
Save the file and close Notepad.

#### Step 5: Flush DNS Cache
Open PowerShell as Administrator:
```powershell
ipconfig /flushdns
```

#### Step 6: Restart Server and Run Tests
```powershell
cd C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar
php artisan serve --host=0.0.0.0 --port=8000
```

---

### ✅ **Approach 3: Disable IPv6 for Localhost (System-Wide)**

⚠️ **Warning**: This affects all applications on your system.

#### Step 1: Open Registry Editor
1. Press `Win + R`
2. Type: `regedit`
3. Click "Yes" for administrator privileges

#### Step 2: Navigate to Registry Key
Go to:
```
HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\Tcpip6\Parameters
```

#### Step 3: Create/Modify DisabledComponents
1. Right-click in the right pane
2. Select "New" → "DWORD (32-bit) Value"
3. Name it: `DisabledComponents`
4. Double-click it and set value to: `0xFFFFFFFF` (or `4294967295` in decimal)
5. Click OK

#### Step 4: Restart Computer
You must restart your computer for this to take effect.

#### Step 5: After Restart, Run Tests
```powershell
cd C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar
php artisan serve --host=0.0.0.0 --port=8000
```

---

## Quick Test Execution Steps

Once you've applied one of the approaches above:

### Step 1: Verify Server is Running
```powershell
# Check if server is listening
netstat -ano | findstr :8000 | findstr LISTENING

# Test connectivity
curl http://127.0.0.1:8000 -UseBasicParsing | Select-Object StatusCode
```

### Step 2: Verify Config File
Ensure `testsprite_tests/tmp/config.json` has:
- `localEndpoint`: `http://127.0.0.1:8000` (or `http://localhost:8000` if using hosts file approach)
- `executionArgs` with proper structure
- `envs` with `API_KEY` set

### Step 3: Run Testsprite Tests
```powershell
cd C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar
node C:\Users\jimbo\AppData\Local\npm-cache\_npx\8ddf6bea01b2519d\node_modules\@testsprite\testsprite-mcp\dist\index.js generateCodeAndExecute
```

Or use the MCP tool directly (which should handle this automatically).

### Step 4: Check Test Results
After execution, check:
- `testsprite_tests/tmp/raw_report.md` - Raw test results
- `testsprite_tests/testsprite-mcp-test-report.md` - Formatted report (generated after analysis)

---

## Troubleshooting

### Issue: "Connection refused" or "ECONNREFUSED"
**Solution**: 
1. Ensure server is running: `php artisan serve --host=0.0.0.0 --port=8000`
2. Check firewall isn't blocking port 8000
3. Try accessing `http://127.0.0.1:8000` in browser

### Issue: "Execution arguments not found"
**Solution**: 
1. Check `testsprite_tests/tmp/config.json` has `executionArgs` section
2. Ensure all required fields are present

### Issue: "API_KEY error"
**Solution**: 
1. Verify API key is in `envs.API_KEY` in config.json
2. Check API key is valid at https://www.testsprite.com/dashboard/settings/apikey

### Issue: Tests still fail with IPv6 error
**Solution**: 
1. Try Approach 1 first (NODE_OPTIONS)
2. If that doesn't work, try Approach 2 (hosts file)
3. As last resort, try Approach 3 (disable IPv6)

---

## ⚡ QUICK FIX SCRIPT (Run as Administrator)

Save this as `fix-localhost.ps1` and run as Administrator:

```powershell
# Fix localhost IPv6/IPv4 issue for Testsprite
$hostsPath = "$env:SystemRoot\System32\drivers\etc\hosts"
$backupPath = "$hostsPath.backup.$(Get-Date -Format 'yyyyMMdd-HHmmss')"

# Backup hosts file
Copy-Item $hostsPath $backupPath -Force
Write-Host "Backup created: $backupPath" -ForegroundColor Green

# Read current content
$content = Get-Content $hostsPath -ErrorAction SilentlyContinue

# Check if IPv4 entry exists
$hasIPv4 = $content | Select-String -Pattern "^\s*127\.0\.0\.1\s+localhost" -Quiet

# Check if IPv6 entry exists and is not commented
$hasIPv6Active = $content | Select-String -Pattern "^\s*::1\s+localhost" -Quiet

$newContent = @()

# Add IPv4 entry if missing
if (-not $hasIPv4) {
    $newContent += "127.0.0.1    localhost"
    Write-Host "Adding IPv4 localhost entry..." -ForegroundColor Yellow
}

# Process existing content
foreach ($line in $content) {
    # Comment out active IPv6 localhost entries
    if ($line -match "^\s*::1\s+localhost" -and -not $line.StartsWith("#")) {
        $newContent += "#$line  # Commented for Testsprite compatibility"
        Write-Host "Commenting out IPv6 entry: $line" -ForegroundColor Yellow
    } else {
        $newContent += $line
    }
}

# Write updated content
$newContent | Set-Content $hostsPath -Force
Write-Host "Hosts file updated successfully!" -ForegroundColor Green

# Flush DNS
ipconfig /flushdns | Out-Null
Write-Host "DNS cache flushed!" -ForegroundColor Green

Write-Host "`nPlease restart Cursor IDE and try running Testsprite again." -ForegroundColor Cyan
```

## Recommended Order of Attempts

1. **First**: Run the Quick Fix Script above (Requires Admin) - Fastest solution
2. **Second**: Try Approach 2 manually (hosts file) - If script doesn't work
3. **Third**: Try Approach 1 (NODE_OPTIONS) - May work after hosts fix
4. **Last Resort**: Approach 3 (disable IPv6) - System-wide change, requires restart

---

## Current Status

✅ Code summary generated: `testsprite_tests/tmp/code_summary.json`
✅ Test plan generated: `testsprite_tests/testsprite_frontend_test_plan.json`
✅ Server running on port 8000
✅ Config file has API key
⏳ Waiting for IPv6/IPv4 connectivity fix

---

## Next Steps After Fix

Once connectivity is resolved:
1. Testsprite will generate test code
2. Tests will execute automatically
3. Results will be saved to `testsprite_tests/tmp/raw_report.md`
4. AI will analyze and create formatted report in `testsprite_tests/testsprite-mcp-test-report.md`

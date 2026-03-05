# 🚀 Quick Start: Fix Testsprite IPv6 Issue

## The Problem
Testsprite can't connect because Windows resolves `localhost` to IPv6 (`::1`), but your PHP server only listens on IPv4 (`127.0.0.1`).

## ✅ EASIEST SOLUTION (2 minutes)

### Step 1: Run the Fix Script
1. **Right-click** on `fix-localhost.ps1` in your project folder
2. Select **"Run with PowerShell"** (or "Run as Administrator")
3. If prompted, click **"Yes"** to allow the script to run

The script will:
- ✅ Backup your hosts file
- ✅ Add IPv4 localhost entry
- ✅ Comment out IPv6 entries
- ✅ Flush DNS cache

### Step 2: Restart Cursor IDE
Close and reopen Cursor completely to apply changes.

### Step 3: Verify Server is Running
```powershell
cd C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 4: Run Testsprite Tests
The tests should now work! You can either:
- Use the MCP tool: `testsprite_generate_code_and_execute`
- Or run manually (see full guide)

---

## 📋 What's Already Done

✅ Code summary generated  
✅ Test plan created (783 test cases!)  
✅ Server configured  
✅ API key added to config  
⏳ Just need to fix IPv6/IPv4 connectivity

---

## 🔍 Verify the Fix Worked

After running the script, test connectivity:
```powershell
# This should work now
Test-NetConnection -ComputerName localhost -Port 8000
```

You should see `TcpTestSucceeded : True` without IPv6 errors.

---

## 📚 Full Documentation

See `TESTSprite_TESTING_GUIDE.md` for:
- Detailed troubleshooting
- Alternative approaches
- Complete step-by-step instructions

---

## 🆘 If Script Doesn't Work

1. **Manual hosts file edit** (see Approach 2 in full guide)
2. **Set NODE_OPTIONS** environment variable (see Approach 1)
3. **Contact Testsprite support** about Windows IPv6 handling

---

## ✨ After Fix - What Happens Next

1. Testsprite generates test code automatically
2. Tests execute against your Laravel app
3. Results saved to `testsprite_tests/tmp/raw_report.md`
4. AI analyzes and creates formatted report
5. Review results in `testsprite_tests/testsprite-mcp-test-report.md`

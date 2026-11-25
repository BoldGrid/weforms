# 🔒 weForms Security Vulnerability Testing Suite

Complete testing suite for verifying the PHP Object Injection vulnerability patch in the weForms WordPress plugin.

## 📋 Table of Contents

- [Quick Start](#quick-start)
- [Files Included](#files-included)
- [Automated Testing](#automated-testing)
- [Manual Testing](#manual-testing)
- [Understanding the Results](#understanding-the-results)
- [Troubleshooting](#troubleshooting)

---

## 🚀 Quick Start

### Option 1: Run All Tests (Recommended)

```bash
cd /tmp/weforms-security-tests
./run-all-tests.sh /path/to/weforms/plugin
```

### Option 2: Run Individual Tests

```bash
# Test 1: Basic Proof of Concept
php exploit-poc.php

# Test 2: Automated Plugin Scan
php automated-test.php /path/to/weforms/plugin
```

### Option 3: Follow Manual Testing Guide

See [MANUAL_TESTING_GUIDE.md](MANUAL_TESTING_GUIDE.md) for detailed step-by-step instructions.

---

## 📁 Files Included

### `run-all-tests.sh`
Main test runner script that executes all automated tests and provides a summary.

**Usage:**
```bash
./run-all-tests.sh [plugin_path]
```

**Example:**
```bash
./run-all-tests.sh ~/wordpress/wp-content/plugins/weforms
```

### `exploit-poc.php`
Proof-of-concept demonstrating the PHP Object Injection vulnerability using a safe payload.

**Features:**
- Demonstrates vulnerable `unserialize()` behavior
- Shows secure deserialization with `allowed_classes => false`
- Safe payload (only writes to log file)
- Compares before/after patch behavior

**Usage:**
```bash
php exploit-poc.php
```

**Example Output:**
```
======================================================================
  WEFORMS PHP OBJECT INJECTION VULNERABILITY TEST
======================================================================

Generated malicious payload:
O:11:"EvilPayload":2:{s:8:"log_file";s:26:"/tmp/exploit-test.log";s:7:"message";s:36:"PHP OBJECT INJECTION - EXPLOIT SUCCESSFUL!";}

TEST 1: VULNERABLE CODE (BEFORE PATCH)
----------------------------------------------------------------------
❌ TESTING UNSAFE DESERIALIZATION (VULNERABLE)
⚠️ CRITICAL: Object was instantiated!
⚠️ VULNERABILITY EXPLOITED: Magic method executed!

TEST 2: PATCHED CODE (AFTER PATCH)
----------------------------------------------------------------------
✅ TESTING SAFE DESERIALIZATION (PATCHED)
✅ SUCCESS: Object instantiation blocked!

EXPLOIT DETECTION RESULTS
----------------------------------------------------------------------
✅ SECURE: No exploit log file created!
```

### `automated-test.php`
Comprehensive automated security scanner that checks the plugin code for vulnerabilities.

**Features:**
- Scans for unsafe `unserialize()` calls
- Verifies safe deserialization patterns
- Checks for `maybe_unserialize()` usage
- Validates `allowed_classes` parameter usage
- Color-coded output
- Exit codes for CI/CD integration

**Usage:**
```bash
php automated-test.php /path/to/weforms/plugin
```

**Tests Performed:**
1. ✅ Scan for unsafe `unserialize()` calls
2. ✅ Verify safe deserialization patterns
3. ✅ Check for remaining `maybe_unserialize()`
4. ✅ Verify `allowed_classes => false` parameter

**Exit Codes:**
- `0` - All tests passed (secure)
- `1` - Tests failed (vulnerable)

### `MANUAL_TESTING_GUIDE.md`
Comprehensive step-by-step manual testing guide.

**Includes:**
- Environment setup instructions
- Vulnerability explanation
- Testing vulnerable version
- Applying the patch
- Testing patched version
- Verification checklists
- Troubleshooting tips

---

## 🤖 Automated Testing

### Running the Full Test Suite

```bash
cd /tmp/weforms-security-tests
./run-all-tests.sh /path/to/weforms
```

### Expected Output (Patched Version)

```
========================================================================
  WEFORMS SECURITY VULNERABILITY - AUTOMATED TESTING
========================================================================

Checking Prerequisites
------------------------------------------------------------------------
✓ PHP found: PHP 8.1.2 (cli)
✓ Plugin path: /home/user/wp-content/plugins/weforms

TEST 1: Running Basic Proof-of-Concept
------------------------------------------------------------------------
✅ SUCCESS: Object instantiation blocked!
✅ SECURE: No exploit log file created!
✓ PoC test completed

TEST 2: Running Automated Plugin Security Scan
------------------------------------------------------------------------
✅ PASS: No unsafe unserialize() calls found
✅ PASS: All expected safe patterns verified
✅ PASS: No maybe_unserialize() calls found
✅ PASS: All unserialize() calls have allowed_classes => false

TEST SUMMARY
------------------------------------------------------------------------
✅ ALL TESTS PASSED!

The weForms plugin is secure against PHP Object Injection.
The patch has been successfully applied and verified.
```

### Expected Output (Vulnerable Version)

```
========================================================================
  WEFORMS SECURITY VULNERABILITY - AUTOMATED TESTING
========================================================================

TEST 1: Running Basic Proof-of-Concept
------------------------------------------------------------------------
⚠️ VULNERABILITY EXPLOITED: Magic method executed!
❌ VULNERABLE: Exploit log file created!

TEST 2: Running Automated Plugin Security Scan
------------------------------------------------------------------------
❌ FAIL: Found unsafe unserialize() calls
❌ FAIL: Some safe patterns missing

TEST SUMMARY
------------------------------------------------------------------------
❌ TESTS FAILED!

The weForms plugin may still be vulnerable.
```

---

## 📖 Manual Testing

For detailed manual testing instructions, see [MANUAL_TESTING_GUIDE.md](MANUAL_TESTING_GUIDE.md).

### Quick Manual Test

```bash
# 1. Check current branch
cd /path/to/weforms
git branch

# 2. Scan for vulnerable code
grep -rn "unserialize(" includes/ | grep -v "allowed_classes"

# 3. Expected on VULNERABLE version:
#    - Multiple unsafe unserialize() calls found

# 4. Expected on PATCHED version:
#    - No unsafe unserialize() calls found
#    - All calls have allowed_classes => false
```

---

## 📊 Understanding the Results

### Test Status Indicators

| Indicator | Meaning |
|-----------|---------|
| ✅ PASS | Test passed - secure |
| ❌ FAIL | Test failed - vulnerable |
| ⚠️ WARNING | Potential issue - review needed |
| ℹ INFO | Informational message |

### What Makes Code Vulnerable?

```php
// ❌ VULNERABLE - No validation or restrictions
$data = unserialize($user_input);

// ❌ VULNERABLE - No object restrictions
$data = maybe_unserialize($user_input);

// ⚠️ PARTIALLY VULNERABLE - Missing type check
if (is_serialized($data)) {
    $data = unserialize($data, ['allowed_classes' => false]);
}
```

### What Makes Code Secure?

```php
// ✅ SECURE - Multiple layers of validation
$data = is_string($user_input) && is_serialized($user_input)
    ? @unserialize($user_input, ['allowed_classes' => false])
    : $user_input;
```

**Security Layers:**
1. ✅ `is_string()` - Type validation
2. ✅ `is_serialized()` - Format validation
3. ✅ `['allowed_classes' => false]` - Object restriction
4. ✅ `@` - Error suppression
5. ✅ Fallback - Return original if validation fails

---

## 🔧 Troubleshooting

### "PHP not found"

**Solution:**
```bash
# Check if PHP is installed
php -v

# If not installed, install it
# Ubuntu/Debian:
sudo apt-get install php-cli

# macOS:
brew install php
```

### "Not a weForms plugin directory"

**Solution:**
```bash
# Verify you're pointing to the correct directory
ls /path/to/weforms/weforms.php

# If file doesn't exist, locate the correct path
find /var/www -name "weforms.php" 2>/dev/null
```

### Tests Pass But Still Unsure

**Solution: Run Comparison Test**

```bash
# Test vulnerable version
cd /path/to/weforms
git checkout master
php /tmp/weforms-security-tests/exploit-poc.php > /tmp/before.txt

# Test patched version
git checkout fix-access-vulnerable
php /tmp/weforms-security-tests/exploit-poc.php > /tmp/after.txt

# Compare results
diff /tmp/before.txt /tmp/after.txt
```

**Expected Diff:**
- Before: Object instantiated, magic methods executed
- After: Object blocked, magic methods NOT executed

### False Positives

Some matches might be false positives:

```bash
# Comments are okay
// unserialize() is dangerous

# Safe patterns are okay
unserialize($data, ['allowed_classes' => false])

# Third-party code in libraries might be okay
/library/external/file.php
```

---

## 📝 Test Checklist

Before marking the vulnerability as fixed, ensure:

- [ ] All automated tests pass
- [ ] Manual PoC exploit is blocked
- [ ] Plugin functionality still works
- [ ] No performance degradation
- [ ] All 10 unserialize() locations patched
- [ ] Git diff shows expected changes
- [ ] No new unsafe patterns introduced
- [ ] Documentation updated
- [ ] Security advisory prepared

---

## 🎯 Quick Reference

### Test Commands

```bash
# Run everything
./run-all-tests.sh /path/to/weforms

# Basic PoC only
php exploit-poc.php

# Plugin scan only
php automated-test.php /path/to/weforms

# Manual check
grep -rn "allowed_classes" /path/to/weforms/includes/ | wc -l
# Should return: 10
```

### Verify Patch Applied

```bash
cd /path/to/weforms
git branch  # Should show: * fix-access-vulnerable
git diff master --stat  # Should show: 6 files changed
```

### Quick Security Scan

```bash
# Should return 0 (zero) results
grep -rn "unserialize(" /path/to/weforms/includes/ | \
  grep -v "allowed_classes" | \
  grep -v "//" | \
  wc -l
```

---

## 📚 Additional Resources

- [OWASP: PHP Object Injection](https://owasp.org/www-community/vulnerabilities/PHP_Object_Injection)
- [CWE-502: Deserialization of Untrusted Data](https://cwe.mitre.org/data/definitions/502.html)
- [PHP unserialize() Security](https://www.php.net/manual/en/function.unserialize.php)
- [WordPress Plugin Security](https://developer.wordpress.org/plugins/security/)

---

## 🆘 Support

If you encounter issues:

1. Check the [MANUAL_TESTING_GUIDE.md](MANUAL_TESTING_GUIDE.md)
2. Review the [Troubleshooting](#troubleshooting) section
3. Verify prerequisites are met
4. Check file permissions
5. Review PHP error logs

---

## 📄 License

This testing suite is provided for security research and vulnerability verification purposes only.

**⚠️ IMPORTANT:** These tests should ONLY be run in development/test environments, NEVER in production!

---

**Version:** 1.0.0
**Last Updated:** 2025-01-15
**Vulnerability:** CWE-502 (PHP Object Injection)
**Severity:** Critical (CVSS 9.8)

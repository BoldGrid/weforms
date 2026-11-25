# 🧪 weForms Security Vulnerability - Manual Testing Guide

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Test Environment Setup](#test-environment-setup)
3. [Understanding the Vulnerability](#understanding-the-vulnerability)
4. [Testing the Vulnerable Version](#testing-the-vulnerable-version)
5. [Applying the Patch](#applying-the-patch)
6. [Testing the Patched Version](#testing-the-patched-version)
7. [Verification Checklist](#verification-checklist)

---

## Prerequisites

- PHP 7.4 or higher with CLI access
- WordPress test installation
- weForms plugin installed
- Git installed
- Terminal/command line access
- Text editor

**⚠️ WARNING**: Perform these tests ONLY on a test/development environment, NEVER on production!

---

## Test Environment Setup

### Step 1: Create Test Environment

```bash
# Create a backup of your current environment
cd /path/to/wordpress/wp-content/plugins/weforms
git stash  # Save any uncommitted changes

# Checkout the vulnerable version (master branch)
git checkout master

# Verify you're on the correct branch
git branch
# Should show: * master
```

### Step 2: Prepare Test Database

```sql
-- Create a test form entry with serialized data
INSERT INTO wp_weforms_entrymeta
(weforms_entry_id, meta_key, meta_value)
VALUES
(1, 'test_field', 'a:1:{s:4:"test";s:5:"value";}');
```

---

## Understanding the Vulnerability

### What is PHP Object Injection?

PHP Object Injection occurs when user-controlled serialized data is passed to `unserialize()` without validation. This allows attackers to:

1. **Instantiate arbitrary objects**
2. **Trigger magic methods** (`__wakeup`, `__destruct`, `__toString`, etc.)
3. **Execute arbitrary code** through property-oriented programming (POP) chains
4. **Compromise the server** completely

### Attack Flow

```
User Input (Serialized Object)
    ↓
unserialize() without restrictions
    ↓
Object instantiated
    ↓
Magic methods execute
    ↓
Arbitrary code execution
    ↓
Server compromised
```

### Vulnerable Code Pattern

```php
// ❌ VULNERABLE
$data = unserialize($user_input);
```

### Secure Code Pattern

```php
// ✅ SECURE
$data = is_string($user_input) && is_serialized($user_input)
    ? @unserialize($user_input, ['allowed_classes' => false])
    : $user_input;
```

---

## Testing the Vulnerable Version

### Test 1: Basic Proof of Concept

#### 1.1 Create Evil Payload Class

Create file: `/tmp/test-exploit.php`

```php
<?php
/**
 * Safe PoC - Only creates a log file, not actual RCE
 */
class EvilPayload {
    public $log_file = '/tmp/exploit-success.log';
    public $message = 'VULNERABILITY CONFIRMED!';

    public function __destruct() {
        file_put_contents(
            $this->log_file,
            "[" . date('Y-m-d H:i:s') . "] " . $this->message . "\n",
            FILE_APPEND
        );
        echo "⚠️ EXPLOIT EXECUTED: Magic method __destruct() called!\n";
    }
}

// Generate payload
$evil = new EvilPayload();
$payload = serialize($evil);

echo "Malicious Payload:\n";
echo $payload . "\n\n";

// Test vulnerable code
echo "Testing VULNERABLE code:\n";
$result = unserialize($payload);  // VULNERABLE!
echo "Object class: " . get_class($result) . "\n";

// Check if exploit worked
if (file_exists('/tmp/exploit-success.log')) {
    echo "\n❌ EXPLOIT SUCCESSFUL!\n";
    echo "Log contents:\n";
    echo file_get_contents('/tmp/exploit-success.log');
} else {
    echo "\n✅ Exploit blocked\n";
}
```

#### 1.2 Run the Test

```bash
# Clean up any previous test
rm -f /tmp/exploit-success.log

# Run the vulnerable test
php /tmp/test-exploit.php
```

**Expected Output (VULNERABLE):**
```
Malicious Payload:
O:11:"EvilPayload":2:{s:8:"log_file";s:26:"/tmp/exploit-success.log";s:7:"message";s:23:"VULNERABILITY CONFIRMED!";}

Testing VULNERABLE code:
⚠️ EXPLOIT EXECUTED: Magic method __destruct() called!
Object class: EvilPayload

❌ EXPLOIT SUCCESSFUL!
Log contents:
[2025-01-15 10:30:45] VULNERABILITY CONFIRMED!
```

### Test 2: Testing Actual weForms Code

#### 2.1 Locate Vulnerable Functions

```bash
# Find all unserialize calls in the plugin
cd /path/to/wordpress/wp-content/plugins/weforms
grep -rn "unserialize(" includes/ | grep -v "allowed_classes"
```

**Expected Output (VULNERABLE VERSION):**
```
includes/admin/class-privacy.php:221:        $field_value = unserialize( $payment_data->payment_data );
includes/api/class-weforms-forms-controller.php:1551:            $payment->payment_data = unserialize( $payment->payment_data );
includes/class-ajax.php:523:            $payment->payment_data = unserialize( $payment->payment_data );
includes/class-form-entry.php:173:                        $value      = maybe_unserialize( $value );
includes/class-form-entry.php:199:                        $field_value = unserialize( $value );
includes/class-form-entry.php:224:                        $entry_value = unserialize( $value );
includes/class-form-entry.php:290:                        $entry_value = unserialize( $value );
includes/class-form-entry.php:356:                        $field_value = unserialize( $value );
includes/class-form.php:129:            $field = maybe_unserialize( $content->post_content );
includes/functions.php:1252:        $value = unserialize( $value );
```

#### 2.2 Test via WordPress Admin

1. **Install and activate weForms**
2. **Create a test form**
3. **Submit form with crafted payload** (via REST API or direct database injection)
4. **View entries in admin** - this triggers the vulnerable unserialize()

**Via REST API:**

```bash
# Generate malicious payload
php -r "class Evil { public function __destruct() { file_put_contents('/tmp/pwned.txt', 'RCE!'); }} echo serialize(new Evil());"

# Output something like:
# O:4:"Evil":0:{}

# Submit via REST API
curl -X POST "https://your-test-site.local/wp-json/weforms/v1/forms/1/entries" \
  -H "Content-Type: application/json" \
  -d '{
    "form_id": 1,
    "payment_data": "O:4:\"Evil\":0:{}"
  }'
```

---

## Applying the Patch

### Step 1: Switch to Patched Branch

```bash
cd /path/to/wordpress/wp-content/plugins/weforms

# Stash any changes
git stash

# Checkout the patch branch
git checkout fix-access-vulnerable

# Verify you're on the correct branch
git branch
# Should show: * fix-access-vulnerable
```

### Step 2: Verify Patch Applied

```bash
# Check that safe patterns are in place
grep -rn "allowed_classes" includes/ | wc -l
# Should show: 10 (or similar number indicating all fixes)

# View a specific patched file
git diff master includes/admin/class-privacy.php
```

---

## Testing the Patched Version

### Test 1: Verify Safe Deserialization

#### 1.1 Create Test Script

Create file: `/tmp/test-patched.php`

```php
<?php
/**
 * Test patched safe deserialization
 */

// Evil payload class
class EvilPayload {
    public $log_file = '/tmp/exploit-blocked.log';

    public function __destruct() {
        file_put_contents($this->log_file, "SHOULD NOT HAPPEN!\n");
        echo "❌ ERROR: Magic method executed (patch failed)\n";
    }
}

// Generate malicious payload
$evil = new EvilPayload();
$payload = serialize($evil);

echo "Testing PATCHED code:\n";
echo "Payload: {$payload}\n\n";

// Helper function
function is_serialized($data) {
    if (!is_string($data)) return false;
    $data = trim($data);
    if ('N;' === $data) return true;
    if (strlen($data) < 4) return false;
    if (':' !== $data[1]) return false;
    $lastc = substr($data, -1);
    if (';' !== $lastc && '}' !== $lastc) return false;
    $token = $data[0];
    switch ($token) {
        case 's': return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
        case 'a':
        case 'O': return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
        case 'b':
        case 'i':
        case 'd': return (bool) preg_match("/^{$token}:[0-9.E+-]+;$/", $data);
    }
    return false;
}

// PATCHED CODE - SAFE
$result = is_string($payload) && is_serialized($payload)
    ? @unserialize($payload, ['allowed_classes' => false])
    : $payload;

echo "Result type: " . gettype($result) . "\n";

if (is_object($result)) {
    echo "❌ FAIL: Object instantiated (patch incomplete)\n";
    echo "Class: " . get_class($result) . "\n";
} else {
    echo "✅ SUCCESS: Object instantiation blocked!\n";
    echo "Result: ";
    print_r($result);
}

// Verify exploit was blocked
sleep(1);  // Give time for destruct to potentially trigger

if (file_exists('/tmp/exploit-blocked.log')) {
    echo "\n❌ CRITICAL: Exploit file created! Patch FAILED!\n";
} else {
    echo "\n✅ VERIFIED: No exploit file created. Patch successful!\n";
}
```

#### 1.2 Run the Test

```bash
# Clean up
rm -f /tmp/exploit-blocked.log

# Run test
php /tmp/test-patched.php
```

**Expected Output (PATCHED):**
```
Testing PATCHED code:
Payload: O:11:"EvilPayload":1:{s:8:"log_file";s:26:"/tmp/exploit-blocked.log";}

Result type: array
✅ SUCCESS: Object instantiation blocked!
Result: Array
(
    [log_file] => /tmp/exploit-blocked.log
)

✅ VERIFIED: No exploit file created. Patch successful!
```

### Test 2: Run Automated Test Suite

```bash
# Run the comprehensive test suite
php /tmp/weforms-security-tests/automated-test.php /path/to/weforms
```

**Expected Output:**
```
======================================================================
  WEFORMS SECURITY VULNERABILITY TEST SUITE
======================================================================

Plugin Path: /path/to/weforms

----------------------------------------------------------------------
TEST 1: Scanning for Unsafe unserialize() Calls
----------------------------------------------------------------------
  ✅ PASS: No unsafe unserialize() calls found

----------------------------------------------------------------------
TEST 2: Verifying Safe Deserialization Patterns
----------------------------------------------------------------------
  ✅ includes/admin/class-privacy.php: 1/1 safe patterns
  ✅ includes/api/class-weforms-forms-controller.php: 1/1 safe patterns
  ✅ includes/class-ajax.php: 1/1 safe patterns
  ✅ includes/class-form-entry.php: 5/5 safe patterns
  ✅ includes/class-form.php: 1/1 safe patterns

  ✅ PASS: All expected safe patterns verified

----------------------------------------------------------------------
TEST 3: Checking for Unsafe maybe_unserialize() Calls
----------------------------------------------------------------------
  ✅ PASS: No maybe_unserialize() calls found

----------------------------------------------------------------------
TEST 4: Verifying allowed_classes => false Parameter
----------------------------------------------------------------------
  ✅ PASS: All 10 unserialize() calls have allowed_classes => false

======================================================================
  TEST SUMMARY
======================================================================

  ✅ Unsafe Unserialize            : PASS
  ✅ Safe Patterns                 : PASS
  ✅ Maybe Unserialize             : PASS
  ✅ Allowed Classes               : PASS

======================================================================
Total Tests: 4
Passed: 4
Failed: 0
Warnings: 0
======================================================================

✅ VERDICT: SECURE - All tests passed!
```

### Test 3: Functional Testing

Verify the plugin still works correctly after patching:

1. **Create a new form**
2. **Submit the form with various field types:**
   - Text fields
   - File uploads
   - Address fields
   - Product fields
   - Grid fields
3. **View entries in admin**
4. **Export entries**
5. **View payment data** (if applicable)

**All functions should work normally!**

---

## Verification Checklist

### Pre-Patch (Vulnerable) Verification

- [ ] Can find unsafe `unserialize()` calls in code
- [ ] PoC exploit executes successfully
- [ ] Magic methods are triggered
- [ ] Exploit log file is created
- [ ] Objects can be instantiated from serialized strings

### Post-Patch (Secure) Verification

- [ ] No unsafe `unserialize()` calls remain
- [ ] All `unserialize()` calls use `allowed_classes => false`
- [ ] PoC exploit is blocked
- [ ] Magic methods are NOT triggered
- [ ] No exploit log file is created
- [ ] Objects are converted to arrays
- [ ] All automated tests pass
- [ ] Plugin functionality remains intact

### Code Review Checklist

- [ ] `includes/admin/class-privacy.php:221` - Safe deserialization
- [ ] `includes/api/class-weforms-forms-controller.php:1552` - Safe deserialization
- [ ] `includes/class-ajax.php:524` - Safe deserialization
- [ ] `includes/class-form-entry.php:173` - Safe deserialization (file uploads)
- [ ] `includes/class-form-entry.php:204` - Safe deserialization (products)
- [ ] `includes/class-form-entry.php:229` - Safe deserialization (checkbox grid)
- [ ] `includes/class-form-entry.php:295` - Safe deserialization (choice grid)
- [ ] `includes/class-form-entry.php:361` - Safe deserialization (address fields)
- [ ] `includes/class-form.php:131` - Safe deserialization (form fields)
- [ ] `includes/functions.php:1251` - Unsafe unserialize removed

---

## Quick Testing Commands

```bash
# Quick scan for vulnerable patterns
cd /path/to/weforms
grep -rn "unserialize\s*(" includes/ | grep -v "allowed_classes" | grep -v "//"

# Count safe patterns
grep -rn "allowed_classes.*false" includes/ | wc -l

# Run basic PoC
php /tmp/weforms-security-tests/exploit-poc.php

# Run full test suite
php /tmp/weforms-security-tests/automated-test.php $(pwd)

# Compare branches
git diff master..fix-access-vulnerable
```

---

## Troubleshooting

### Test fails with "Class not found"

Make sure you're running the test scripts with PHP CLI:
```bash
php -v  # Check PHP version
which php  # Check PHP path
```

### Exploit still works after patching

1. Verify you're on the correct branch:
   ```bash
   git branch
   ```

2. Check the specific file:
   ```bash
   grep -A 5 "unserialize" includes/admin/class-privacy.php
   ```

3. Clear PHP opcache:
   ```bash
   php -r "opcache_reset();"
   ```

### Tests pass but unsure if secure

Run the comparison test:
```bash
# Create a test that compares both versions
git stash
git checkout master
php /tmp/test-exploit.php > /tmp/before.txt

git checkout fix-access-vulnerable
php /tmp/test-exploit.php > /tmp/after.txt

diff /tmp/before.txt /tmp/after.txt
```

---

## Next Steps

After successful testing:

1. ✅ **Commit the changes**
2. ✅ **Create a security release**
3. ✅ **Notify users to update**
4. ✅ **Submit to WordPress security team**
5. ✅ **Request CVE if applicable**

---

## References

- [PHP Object Injection (OWASP)](https://owasp.org/www-community/vulnerabilities/PHP_Object_Injection)
- [CWE-502: Deserialization of Untrusted Data](https://cwe.mitre.org/data/definitions/502.html)
- [PHP unserialize() Documentation](https://www.php.net/manual/en/function.unserialize.php)
- [WordPress Security Best Practices](https://developer.wordpress.org/plugins/security/)

---

**Last Updated:** 2025-01-15
**Test Suite Version:** 1.0.0
**Vulnerability:** CWE-502 (PHP Object Injection)
**Severity:** Critical (CVSS 9.8)

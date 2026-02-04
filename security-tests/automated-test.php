<?php
/**
 * Automated Security Test for weForms Plugin
 *
 * This script tests the actual plugin code for unsafe deserialization
 * vulnerabilities.
 *
 * Usage: php automated-test.php /path/to/weforms/plugin
 *
 * @package WeForms Security Tests
 */

// Color output for terminal
class TestColors {
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const RESET = "\033[0m";
    const BOLD = "\033[1m";
}

class WeFormsSecurityTester {
    private $plugin_path;
    private $test_results = [];
    private $vulnerable_files = [];
    private $patched_files = [];

    public function __construct($plugin_path) {
        $this->plugin_path = rtrim($plugin_path, '/');

        if (!is_dir($this->plugin_path)) {
            die("Error: Plugin path does not exist: {$this->plugin_path}\n");
        }
    }

    /**
     * Run all security tests
     */
    public function run_tests() {
        $this->print_header("WEFORMS SECURITY VULNERABILITY TEST SUITE");

        echo "Plugin Path: {$this->plugin_path}\n\n";

        // Test 1: Scan for unsafe unserialize calls
        $this->test_scan_unsafe_unserialize();

        // Test 2: Verify safe deserialization patterns
        $this->test_verify_safe_patterns();

        // Test 3: Check for remaining maybe_unserialize
        $this->test_check_maybe_unserialize();

        // Test 4: Verify allowed_classes parameter
        $this->test_verify_allowed_classes();

        // Print summary
        $this->print_summary();
    }

    /**
     * Test 1: Scan for unsafe unserialize calls
     */
    private function test_scan_unsafe_unserialize() {
        $this->print_test_header("TEST 1: Scanning for Unsafe unserialize() Calls");

        $files_to_check = [
            'includes/admin/class-privacy.php',
            'includes/api/class-weforms-forms-controller.php',
            'includes/class-ajax.php',
            'includes/class-form-entry.php',
            'includes/class-form.php',
            'includes/functions.php',
        ];

        $found_unsafe = false;

        foreach ($files_to_check as $file) {
            $full_path = $this->plugin_path . '/' . $file;

            if (!file_exists($full_path)) {
                echo "  ⚠️  File not found: {$file}\n";
                continue;
            }

            $content = file_get_contents($full_path);

            // Look for unsafe unserialize patterns
            // Pattern: unserialize( without ['allowed_classes' => false]
            if (preg_match('/unserialize\s*\(\s*\$[^)]+\)\s*;/', $content, $matches)) {
                // Check if it has allowed_classes parameter
                if (!preg_match('/unserialize\s*\([^)]+\[\s*[\'"]allowed_classes[\'"]\s*=>\s*false\s*\]/', $content)) {
                    echo TestColors::RED . "  ❌ VULNERABLE: {$file}" . TestColors::RESET . "\n";
                    echo "     Found: " . trim($matches[0]) . "\n";
                    $this->vulnerable_files[] = $file;
                    $found_unsafe = true;
                }
            }
        }

        if (!$found_unsafe) {
            echo TestColors::GREEN . "  ✅ PASS: No unsafe unserialize() calls found\n" . TestColors::RESET;
            $this->test_results['unsafe_unserialize'] = 'PASS';
        } else {
            echo TestColors::RED . "  ❌ FAIL: Found unsafe unserialize() calls\n" . TestColors::RESET;
            $this->test_results['unsafe_unserialize'] = 'FAIL';
        }

        echo "\n";
    }

    /**
     * Test 2: Verify safe deserialization patterns
     */
    private function test_verify_safe_patterns() {
        $this->print_test_header("TEST 2: Verifying Safe Deserialization Patterns");

        $files_to_check = [
            'includes/admin/class-privacy.php' => 1,
            'includes/api/class-weforms-forms-controller.php' => 1,
            'includes/class-ajax.php' => 1,
            'includes/class-form-entry.php' => 5,
            'includes/class-form.php' => 1,
        ];

        $all_verified = true;

        foreach ($files_to_check as $file => $expected_count) {
            $full_path = $this->plugin_path . '/' . $file;

            if (!file_exists($full_path)) {
                continue;
            }

            $content = file_get_contents($full_path);

            // Count safe patterns
            $safe_count = preg_match_all(
                '/unserialize\s*\([^)]+\[\s*[\'"]allowed_classes[\'"]\s*=>\s*false\s*\]/',
                $content
            );

            if ($safe_count >= $expected_count) {
                echo TestColors::GREEN . "  ✅ {$file}: {$safe_count}/{$expected_count} safe patterns" . TestColors::RESET . "\n";
                $this->patched_files[] = $file;
            } else {
                echo TestColors::RED . "  ❌ {$file}: {$safe_count}/{$expected_count} safe patterns (MISSING: " . ($expected_count - $safe_count) . ")" . TestColors::RESET . "\n";
                $all_verified = false;
            }
        }

        if ($all_verified) {
            echo TestColors::GREEN . "\n  ✅ PASS: All expected safe patterns verified\n" . TestColors::RESET;
            $this->test_results['safe_patterns'] = 'PASS';
        } else {
            echo TestColors::RED . "\n  ❌ FAIL: Some safe patterns missing\n" . TestColors::RESET;
            $this->test_results['safe_patterns'] = 'FAIL';
        }

        echo "\n";
    }

    /**
     * Test 3: Check for remaining maybe_unserialize
     */
    private function test_check_maybe_unserialize() {
        $this->print_test_header("TEST 3: Checking for Unsafe maybe_unserialize() Calls");

        $files_to_check = [
            'includes/class-form-entry.php',
            'includes/class-form.php',
        ];

        $found_unsafe = false;

        foreach ($files_to_check as $file) {
            $full_path = $this->plugin_path . '/' . $file;

            if (!file_exists($full_path)) {
                continue;
            }

            $content = file_get_contents($full_path);

            // Look for maybe_unserialize
            if (preg_match('/maybe_unserialize\s*\(/', $content, $matches)) {
                echo TestColors::RED . "  ⚠️  FOUND: {$file} still uses maybe_unserialize()\n" . TestColors::RESET;
                $found_unsafe = true;
            }
        }

        if (!$found_unsafe) {
            echo TestColors::GREEN . "  ✅ PASS: No maybe_unserialize() calls found\n" . TestColors::RESET;
            $this->test_results['maybe_unserialize'] = 'PASS';
        } else {
            echo TestColors::YELLOW . "  ⚠️  WARNING: Found maybe_unserialize() calls (should be replaced)\n" . TestColors::RESET;
            $this->test_results['maybe_unserialize'] = 'WARNING';
        }

        echo "\n";
    }

    /**
     * Test 4: Verify allowed_classes parameter
     */
    private function test_verify_allowed_classes() {
        $this->print_test_header("TEST 4: Verifying allowed_classes => false Parameter");

        $all_files = glob($this->plugin_path . '/includes/**/*.php');
        $all_secure = true;
        $unserialize_count = 0;

        foreach ($all_files as $file) {
            $content = file_get_contents($file);

            // Find all unserialize calls
            preg_match_all('/unserialize\s*\([^;]+;/s', $content, $matches);

            foreach ($matches[0] as $match) {
                $unserialize_count++;

                // Check if it has allowed_classes
                if (!preg_match('/\[\s*[\'"]allowed_classes[\'"]\s*=>\s*false\s*\]/', $match)) {
                    // Skip if it's in a comment
                    if (strpos($match, '//') !== false || strpos($match, '/*') !== false) {
                        continue;
                    }

                    echo TestColors::RED . "  ❌ Missing allowed_classes: " . basename($file) . TestColors::RESET . "\n";
                    echo "     " . trim(substr($match, 0, 80)) . "...\n";
                    $all_secure = false;
                }
            }
        }

        if ($all_secure && $unserialize_count > 0) {
            echo TestColors::GREEN . "  ✅ PASS: All {$unserialize_count} unserialize() calls have allowed_classes => false\n" . TestColors::RESET;
            $this->test_results['allowed_classes'] = 'PASS';
        } elseif ($unserialize_count == 0) {
            echo TestColors::YELLOW . "  ⚠️  WARNING: No unserialize() calls found\n" . TestColors::RESET;
            $this->test_results['allowed_classes'] = 'WARNING';
        } else {
            echo TestColors::RED . "  ❌ FAIL: Some unserialize() calls missing allowed_classes\n" . TestColors::RESET;
            $this->test_results['allowed_classes'] = 'FAIL';
        }

        echo "\n";
    }

    /**
     * Print test summary
     */
    private function print_summary() {
        $this->print_header("TEST SUMMARY");

        $total_tests = count($this->test_results);
        $passed = 0;
        $failed = 0;
        $warnings = 0;

        foreach ($this->test_results as $test => $result) {
            $label = str_pad(ucwords(str_replace('_', ' ', $test)), 30);

            if ($result === 'PASS') {
                echo TestColors::GREEN . "  ✅ {$label}: PASS" . TestColors::RESET . "\n";
                $passed++;
            } elseif ($result === 'FAIL') {
                echo TestColors::RED . "  ❌ {$label}: FAIL" . TestColors::RESET . "\n";
                $failed++;
            } else {
                echo TestColors::YELLOW . "  ⚠️  {$label}: {$result}" . TestColors::RESET . "\n";
                $warnings++;
            }
        }

        echo "\n";
        echo str_repeat("=", 70) . "\n";
        echo "Total Tests: {$total_tests}\n";
        echo TestColors::GREEN . "Passed: {$passed}" . TestColors::RESET . "\n";
        echo TestColors::RED . "Failed: {$failed}" . TestColors::RESET . "\n";
        echo TestColors::YELLOW . "Warnings: {$warnings}" . TestColors::RESET . "\n";
        echo str_repeat("=", 70) . "\n\n";

        // Final verdict
        if ($failed > 0) {
            echo TestColors::RED . TestColors::BOLD . "❌ VERDICT: VULNERABLE - Patch incomplete or not applied" . TestColors::RESET . "\n\n";
            exit(1);
        } elseif ($warnings > 0) {
            echo TestColors::YELLOW . TestColors::BOLD . "⚠️  VERDICT: PARTIALLY SECURE - Some improvements needed" . TestColors::RESET . "\n\n";
            exit(0);
        } else {
            echo TestColors::GREEN . TestColors::BOLD . "✅ VERDICT: SECURE - All tests passed!" . TestColors::RESET . "\n\n";
            exit(0);
        }
    }

    private function print_header($text) {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo TestColors::BOLD . "  " . $text . TestColors::RESET . "\n";
        echo str_repeat("=", 70) . "\n\n";
    }

    private function print_test_header($text) {
        echo str_repeat("-", 70) . "\n";
        echo TestColors::BOLD . $text . TestColors::RESET . "\n";
        echo str_repeat("-", 70) . "\n";
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    if ($argc < 2) {
        echo "Usage: php automated-test.php /path/to/weforms/plugin\n";
        echo "Example: php automated-test.php /home/user/wp-content/plugins/weforms\n";
        exit(1);
    }

    $plugin_path = $argv[1];
    $tester = new WeFormsSecurityTester($plugin_path);
    $tester->run_tests();
} else {
    die("This script must be run from the command line.\n");
}

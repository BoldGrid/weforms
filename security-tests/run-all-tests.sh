#!/bin/bash

###############################################################################
# weForms Security Testing - Quick Start Script
#
# This script runs all security tests for the weForms vulnerability patch
###############################################################################

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color
BOLD='\033[1m'

# Print header
print_header() {
    echo ""
    echo "========================================================================"
    echo -e "${BOLD}  $1${NC}"
    echo "========================================================================"
    echo ""
}

# Print section
print_section() {
    echo ""
    echo "------------------------------------------------------------------------"
    echo -e "${BOLD}$1${NC}"
    echo "------------------------------------------------------------------------"
}

# Check if PHP is available
check_php() {
    if ! command -v php &> /dev/null; then
        echo -e "${RED}Error: PHP is not installed or not in PATH${NC}"
        exit 1
    fi
    echo -e "${GREEN}✓ PHP found: $(php -v | head -n1)${NC}"
}

# Get plugin path
get_plugin_path() {
    if [ -z "$1" ]; then
        echo -e "${YELLOW}No plugin path provided. Using current directory...${NC}"
        PLUGIN_PATH="$(pwd)"
    else
        PLUGIN_PATH="$1"
    fi

    if [ ! -d "$PLUGIN_PATH" ]; then
        echo -e "${RED}Error: Plugin path does not exist: $PLUGIN_PATH${NC}"
        exit 1
    fi

    if [ ! -f "$PLUGIN_PATH/weforms.php" ]; then
        echo -e "${RED}Error: Not a weForms plugin directory: $PLUGIN_PATH${NC}"
        echo "Expected to find: $PLUGIN_PATH/weforms.php"
        exit 1
    fi

    echo -e "${GREEN}✓ Plugin path: $PLUGIN_PATH${NC}"
}

# Main execution
main() {
    print_header "WEFORMS SECURITY VULNERABILITY - AUTOMATED TESTING"

    # Check prerequisites
    print_section "Checking Prerequisites"
    check_php
    get_plugin_path "$1"

    # Test 1: Basic PoC
    print_section "TEST 1: Running Basic Proof-of-Concept"
    echo "This test demonstrates the vulnerability with a safe payload..."
    php "$(dirname "$0")/exploit-poc.php"

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ PoC test completed${NC}"
    else
        echo -e "${RED}✗ PoC test failed${NC}"
    fi

    # Test 2: Automated plugin scan
    print_section "TEST 2: Running Automated Plugin Security Scan"
    php "$(dirname "$0")/automated-test.php" "$PLUGIN_PATH"
    TEST_RESULT=$?

    # Summary
    print_section "TEST SUMMARY"

    if [ $TEST_RESULT -eq 0 ]; then
        echo -e "${GREEN}${BOLD}✅ ALL TESTS PASSED!${NC}"
        echo ""
        echo "The weForms plugin is secure against PHP Object Injection."
        echo "The patch has been successfully applied and verified."
        echo ""
    else
        echo -e "${RED}${BOLD}❌ TESTS FAILED!${NC}"
        echo ""
        echo "The weForms plugin may still be vulnerable."
        echo "Please review the test output above for details."
        echo ""
        echo "Common issues:"
        echo "  - Patch not applied correctly"
        echo "  - Wrong branch checked out"
        echo "  - Incomplete patch"
        echo ""
    fi

    # Additional checks
    print_section "Additional Verification"

    # Check git branch
    if [ -d "$PLUGIN_PATH/.git" ]; then
        CURRENT_BRANCH=$(cd "$PLUGIN_PATH" && git branch --show-current 2>/dev/null)
        if [ "$CURRENT_BRANCH" = "fix-access-vulnerable" ]; then
            echo -e "${GREEN}✓ On patched branch: $CURRENT_BRANCH${NC}"
        elif [ "$CURRENT_BRANCH" = "master" ]; then
            echo -e "${YELLOW}⚠ On vulnerable branch: $CURRENT_BRANCH${NC}"
            echo "  Switch to fix-access-vulnerable branch to test the patch"
        else
            echo -e "${BLUE}ℹ On branch: $CURRENT_BRANCH${NC}"
        fi
    fi

    # Count safe patterns
    SAFE_COUNT=$(grep -r "allowed_classes.*false" "$PLUGIN_PATH/includes/" 2>/dev/null | wc -l)
    echo -e "${BLUE}ℹ Found $SAFE_COUNT safe deserialization patterns${NC}"

    # Check for unsafe patterns
    UNSAFE_COUNT=$(grep -rn "unserialize\s*(" "$PLUGIN_PATH/includes/" 2>/dev/null | grep -v "allowed_classes" | grep -v "//" | wc -l)
    if [ "$UNSAFE_COUNT" -gt 0 ]; then
        echo -e "${RED}⚠ Found $UNSAFE_COUNT potentially unsafe unserialize() calls${NC}"
    else
        echo -e "${GREEN}✓ No unsafe unserialize() calls found${NC}"
    fi

    echo ""
    print_header "TESTING COMPLETE"

    # Exit with appropriate code
    exit $TEST_RESULT
}

# Show usage if help requested
if [ "$1" = "-h" ] || [ "$1" = "--help" ]; then
    echo "Usage: $0 [plugin_path]"
    echo ""
    echo "Tests the weForms plugin for PHP Object Injection vulnerabilities"
    echo "and verifies that the security patch has been applied correctly."
    echo ""
    echo "Arguments:"
    echo "  plugin_path    Path to weForms plugin directory (optional)"
    echo "                 If not provided, uses current directory"
    echo ""
    echo "Examples:"
    echo "  $0"
    echo "  $0 /var/www/html/wp-content/plugins/weforms"
    echo "  $0 ~/wordpress/wp-content/plugins/weforms"
    echo ""
    exit 0
fi

# Run main function
main "$@"

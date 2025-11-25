/**
 * ===================================================================
 * CURRENCY FORMAT HELPER - STANDARD UNTUK SELURUH SISTEM
 * ===================================================================
 * 
 * Format: 1,234.56 (comma = thousand separator, period = decimal)
 * Gunakan fungsi-fungsi ini untuk konsistensi di seluruh aplikasi
 * 
 * @version 1.0
 * @date 2025-11-22
 */

// ============================================
// 1. CURRENCY FORMATTING
// ============================================

/**
 * Format angka ke format currency display
 * @param {number|string} value - Nilai yang akan diformat
 * @param {number} decimals - Jumlah desimal (default: 2)
 * @returns {string} - Formatted string (e.g., "1,234.56")
 */
function formatCurrency(value, decimals = 2) {
    // Remove any existing formatting
    let numStr = String(value).replace(/[^\d.]/g, '');
    
    // Handle multiple decimal points
    const parts = numStr.split('.');
    if (parts.length > 2) {
        numStr = parts[0] + '.' + parts.slice(1).join('');
    }
    
    // Limit decimal places
    if (parts.length === 2 && parts[1].length > decimals) {
        numStr = parts[0] + '.' + parts[1].substring(0, decimals);
    }
    
    const num = parseFloat(numStr);
    if (isNaN(num)) return '';
    
    return num.toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

/**
 * Parse formatted currency string ke raw number
 * @param {string} formattedValue - Formatted currency (e.g., "1,234.56")
 * @returns {number} - Raw numeric value
 */
function parseCurrency(formattedValue) {
    if (!formattedValue) return 0;
    // Remove commas (thousand separator)
    const cleaned = String(formattedValue).replace(/,/g, '');
    return parseFloat(cleaned) || 0;
}

/**
 * Validate currency input
 * @param {number} value - Value to validate
 * @param {object} options - Validation options
 * @returns {object} - {valid: boolean, error: string}
 */
function validateCurrencyInput(value, options = {}) {
    const {
        min = 0,
        max = 9999999999999.99, // PostgreSQL NUMERIC(15,2)
        allowZero = false,
        allowNegative = false
    } = options;
    
    const num = parseFloat(value);
    
    if (isNaN(num)) {
        return { valid: false, error: 'Nilai harus berupa angka' };
    }
    
    if (!allowZero && num === 0) {
        return { valid: false, error: 'Nilai tidak boleh nol' };
    }
    
    if (!allowNegative && num < 0) {
        return { valid: false, error: 'Nilai tidak boleh negatif' };
    }
    
    if (num < min) {
        return { valid: false, error: `Nilai minimal: ${formatCurrency(min)}` };
    }
    
    if (num > max) {
        return { valid: false, error: `Nilai maksimal: ${formatCurrency(max)}` };
    }
    
    return { valid: true, error: null };
}

// ============================================
// 2. DUAL INPUT SYSTEM SETUP
// ============================================

/**
 * Setup dual input system untuk currency input
 * @param {string} displayInputId - ID untuk input yang terlihat
 * @param {string} hiddenInputId - ID untuk hidden input yang dikirim ke server
 * @param {object} options - Configuration options
 */
function setupCurrencyInput(displayInputId, hiddenInputId, options = {}) {
    const {
        decimals = 2,
        allowNegative = false,
        maxValue = 9999999999999.99,
        onUpdate = null // Callback function saat value berubah
    } = options;
    
    const displayInput = document.getElementById(displayInputId);
    const hiddenInput = document.getElementById(hiddenInputId);
    
    if (!displayInput || !hiddenInput) {
        console.error('Currency input elements not found:', displayInputId, hiddenInputId);
        return;
    }
    
    // Set inputmode for mobile numeric keyboard
    displayInput.setAttribute('inputmode', 'decimal');
    
    // Real-time formatting on input
    displayInput.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Remove non-numeric except period (and minus if allowed)
        const pattern = allowNegative ? /[^\d.-]/g : /[^\d.]/g;
        let rawValue = value.replace(pattern, '');
        
        // Store cursor position
        let cursorPos = e.target.selectionStart;
        let commasBefore = (value.substring(0, cursorPos).match(/,/g) || []).length;
        
        // Format the value
        let formatted = formatCurrency(rawValue, decimals);
        e.target.value = formatted;
        
        // Parse and update hidden input
        let parsedValue = parseCurrency(formatted);
        
        // Validate max value
        if (parsedValue > maxValue) {
            parsedValue = maxValue;
            formatted = formatCurrency(maxValue, decimals);
            e.target.value = formatted;
        }
        
        hiddenInput.value = parsedValue.toFixed(decimals);
        
        // Restore cursor position (accounting for added/removed commas)
        let commasAfter = (formatted.substring(0, cursorPos).match(/,/g) || []).length;
        let newCursorPos = cursorPos + (commasAfter - commasBefore);
        e.target.setSelectionRange(newCursorPos, newCursorPos);
        
        // Trigger callback
        if (onUpdate && typeof onUpdate === 'function') {
            onUpdate(parsedValue, formatted);
        }
    });
    
    // Format on blur
    displayInput.addEventListener('blur', function(e) {
        if (e.target.value) {
            let rawValue = e.target.value.replace(/[^\d.-]/g, '');
            let formatted = formatCurrency(rawValue, decimals);
            e.target.value = formatted;
            
            let parsedValue = parseCurrency(formatted);
            hiddenInput.value = parsedValue.toFixed(decimals);
            
            if (onUpdate && typeof onUpdate === 'function') {
                onUpdate(parsedValue, formatted);
            }
        }
    });
    
    // Handle paste
    displayInput.addEventListener('paste', function(e) {
        e.preventDefault();
        let pastedText = (e.clipboardData || window.clipboardData).getData('text');
        let rawValue = pastedText.replace(/[^\d.-]/g, '');
        let formatted = formatCurrency(rawValue, decimals);
        e.target.value = formatted;
        
        let parsedValue = parseCurrency(formatted);
        hiddenInput.value = parsedValue.toFixed(decimals);
        
        if (onUpdate && typeof onUpdate === 'function') {
            onUpdate(parsedValue, formatted);
        }
    });
    
    // Initialize with existing value if any
    if (hiddenInput.value) {
        displayInput.value = formatCurrency(hiddenInput.value, decimals);
    }
}

/**
 * Setup multiple currency inputs at once
 * @param {Array} configs - Array of {displayId, hiddenId, options}
 */
function setupMultipleCurrencyInputs(configs) {
    configs.forEach(config => {
        setupCurrencyInput(config.displayId, config.hiddenId, config.options || {});
    });
}

// ============================================
// 3. FORM VALIDATION HELPER
// ============================================

/**
 * Validate all currency inputs in a form before submit
 * @param {HTMLFormElement} form - Form element
 * @param {Array} inputConfigs - Array of {displayId, hiddenId, fieldName, required}
 * @returns {object} - {valid: boolean, errors: Array}
 */
function validateCurrencyForm(form, inputConfigs) {
    const errors = [];
    
    inputConfigs.forEach(config => {
        const displayInput = document.getElementById(config.displayId);
        const hiddenInput = document.getElementById(config.hiddenId);
        const fieldName = config.fieldName || config.displayId;
        
        if (!displayInput || !hiddenInput) {
            errors.push(`Field ${fieldName} tidak ditemukan`);
            return;
        }
        
        // Update hidden input from display
        if (displayInput.value) {
            hiddenInput.value = parseCurrency(displayInput.value);
        }
        
        // Validate if required
        if (config.required && (!hiddenInput.value || hiddenInput.value === '0')) {
            errors.push(`${fieldName} harus diisi dengan benar`);
            displayInput.classList.add('is-invalid');
        } else {
            displayInput.classList.remove('is-invalid');
        }
        
        // Validate with custom rules
        if (config.validate && hiddenInput.value) {
            const validation = validateCurrencyInput(hiddenInput.value, config.validate);
            if (!validation.valid) {
                errors.push(`${fieldName}: ${validation.error}`);
                displayInput.classList.add('is-invalid');
            }
        }
    });
    
    return {
        valid: errors.length === 0,
        errors: errors
    };
}

/**
 * Setup form submit handler with currency validation
 * @param {string} formId - Form element ID
 * @param {Array} inputConfigs - Currency input configurations
 * @param {Function} onValidationError - Callback untuk handle validation error
 */
function setupCurrencyFormSubmit(formId, inputConfigs, onValidationError = null) {
    const form = document.getElementById(formId);
    
    if (!form) {
        console.error('Form not found:', formId);
        return;
    }
    
    form.addEventListener('submit', function(e) {
        const validation = validateCurrencyForm(form, inputConfigs);
        
        if (!validation.valid) {
            e.preventDefault();
            
            if (onValidationError && typeof onValidationError === 'function') {
                onValidationError(validation.errors);
            } else {
                // Default error display
                alert('Error:\n' + validation.errors.join('\n'));
            }
            
            return false;
        }
        
        // Form is valid, continue submission
        console.log('Form validation passed');
    });
}

// ============================================
// 4. DISPLAY HELPERS
// ============================================

/**
 * Format currency untuk display di elemen tertentu
 * @param {string} elementId - Element ID
 * @param {number} value - Value to display
 * @param {object} options - Display options
 */
function displayCurrency(elementId, value, options = {}) {
    const {
        prefix = 'Rp ',
        decimals = 2,
        showSign = false // Show + for positive, - for negative
    } = options;
    
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const num = parseFloat(value);
    if (isNaN(num)) {
        element.textContent = prefix + '0';
        return;
    }
    
    const sign = showSign && num > 0 ? '+' : '';
    const formatted = formatCurrency(Math.abs(num), decimals);
    const negativeSign = num < 0 ? '-' : '';
    
    element.textContent = `${sign}${negativeSign}${prefix}${formatted}`;
}

/**
 * Update multiple display elements at once
 * @param {Array} displays - Array of {elementId, value, options}
 */
function updateMultipleDisplays(displays) {
    displays.forEach(display => {
        displayCurrency(display.elementId, display.value, display.options || {});
    });
}

// ============================================
// 5. UTILITY FUNCTIONS
// ============================================

/**
 * Convert berbagai format number ke standard format
 * Mendukung: Indonesian (1.234,56), International (1,234.56), Raw (1234.56)
 * @param {string|number} value - Value to convert
 * @returns {number} - Standardized number
 */
function normalizeNumber(value) {
    if (typeof value === 'number') return value;
    
    let str = String(value).trim();
    
    // Detect format
    const commaCount = (str.match(/,/g) || []).length;
    const dotCount = (str.match(/\./g) || []).length;
    
    // Indonesian format: 1.234,56
    if (dotCount > 1 || (commaCount === 1 && dotCount >= 1 && str.indexOf('.') < str.indexOf(','))) {
        str = str.replace(/\./g, '').replace(',', '.');
    }
    // International format: 1,234.56
    else if (commaCount > 0) {
        str = str.replace(/,/g, '');
    }
    
    return parseFloat(str) || 0;
}

/**
 * Calculate percentage with formatting
 * @param {number} part - Part value
 * @param {number} total - Total value
 * @param {number} decimals - Decimal places
 * @returns {string} - Formatted percentage (e.g., "25.5%")
 */
function formatPercentage(part, total, decimals = 1) {
    if (total === 0) return '0%';
    const percentage = (part / total) * 100;
    return percentage.toFixed(decimals) + '%';
}

// ============================================
// 6. EXPORT FOR MODULE USAGE (if applicable)
// ============================================

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatCurrency,
        parseCurrency,
        validateCurrencyInput,
        setupCurrencyInput,
        setupMultipleCurrencyInputs,
        validateCurrencyForm,
        setupCurrencyFormSubmit,
        displayCurrency,
        updateMultipleDisplays,
        normalizeNumber,
        formatPercentage
    };
}

// ============================================
// 7. GLOBAL NAMESPACE (for direct <script> inclusion)
// ============================================

window.CurrencyHelper = {
    format: formatCurrency,
    parse: parseCurrency,
    validate: validateCurrencyInput,
    setupInput: setupCurrencyInput,
    setupMultiple: setupMultipleCurrencyInputs,
    validateForm: validateCurrencyForm,
    setupFormSubmit: setupCurrencyFormSubmit,
    display: displayCurrency,
    updateDisplays: updateMultipleDisplays,
    normalize: normalizeNumber,
    formatPercent: formatPercentage
};

console.log('✅ Currency Helper loaded successfully');

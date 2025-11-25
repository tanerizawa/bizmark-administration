/**
 * Currency Formatter & Terbilang (Indonesian Number to Words)
 * For financial input forms
 */

// Format currency with thousand separator
function formatCurrency(input) {
    let value = input.value.replace(/\D/g, ''); // Remove non-digits
    
    if (value) {
        // Format with thousand separator (dots for Indonesian format)
        value = parseInt(value).toLocaleString('id-ID');
        input.value = value;
    }
}

// Convert number to Indonesian words (Terbilang)
function numberToWords(num) {
    if (num === 0) return 'nol';
    
    const ones = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
    const tens = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];
    const teens = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];
    
    function convertLessThanThousand(n) {
        if (n === 0) return '';
        
        if (n < 10) return ones[n];
        
        if (n < 20) return teens[n - 10];
        
        if (n < 100) {
            const ten = Math.floor(n / 10);
            const one = n % 10;
            return tens[ten] + (one > 0 ? ' ' + ones[one] : '');
        }
        
        const hundred = Math.floor(n / 100);
        const remainder = n % 100;
        const hundredWord = hundred === 1 ? 'seratus' : ones[hundred] + ' ratus';
        return hundredWord + (remainder > 0 ? ' ' + convertLessThanThousand(remainder) : '');
    }
    
    if (num < 1000) {
        return convertLessThanThousand(num);
    }
    
    if (num < 1000000) {
        const thousands = Math.floor(num / 1000);
        const remainder = num % 1000;
        const thousandWord = thousands === 1 ? 'seribu' : convertLessThanThousand(thousands) + ' ribu';
        return thousandWord + (remainder > 0 ? ' ' + convertLessThanThousand(remainder) : '');
    }
    
    if (num < 1000000000) {
        const millions = Math.floor(num / 1000000);
        const remainder = num % 1000000;
        const millionWord = millions === 1 ? 'satu juta' : convertLessThanThousand(millions) + ' juta';
        let result = millionWord;
        
        if (remainder >= 1000) {
            const thousands = Math.floor(remainder / 1000);
            const hundredsRemainder = remainder % 1000;
            const thousandWord = thousands === 1 ? 'seribu' : convertLessThanThousand(thousands) + ' ribu';
            result += ' ' + thousandWord;
            if (hundredsRemainder > 0) {
                result += ' ' + convertLessThanThousand(hundredsRemainder);
            }
        } else if (remainder > 0) {
            result += ' ' + convertLessThanThousand(remainder);
        }
        
        return result;
    }
    
    if (num < 1000000000000) {
        const billions = Math.floor(num / 1000000000);
        const remainder = num % 1000000000;
        let result = convertLessThanThousand(billions) + ' miliar';
        
        if (remainder >= 1000000) {
            const millions = Math.floor(remainder / 1000000);
            result += ' ' + convertLessThanThousand(millions) + ' juta';
            const thousandRemainder = remainder % 1000000;
            if (thousandRemainder >= 1000) {
                const thousands = Math.floor(thousandRemainder / 1000);
                result += ' ' + (thousands === 1 ? 'seribu' : convertLessThanThousand(thousands) + ' ribu');
                const finalRemainder = thousandRemainder % 1000;
                if (finalRemainder > 0) {
                    result += ' ' + convertLessThanThousand(finalRemainder);
                }
            } else if (thousandRemainder > 0) {
                result += ' ' + convertLessThanThousand(thousandRemainder);
            }
        } else if (remainder >= 1000) {
            const thousands = Math.floor(remainder / 1000);
            result += ' ' + (thousands === 1 ? 'seribu' : convertLessThanThousand(thousands) + ' ribu');
            const finalRemainder = remainder % 1000;
            if (finalRemainder > 0) {
                result += ' ' + convertLessThanThousand(finalRemainder);
            }
        } else if (remainder > 0) {
            result += ' ' + convertLessThanThousand(remainder);
        }
        
        return result;
    }
    
    return 'angka terlalu besar';
}

// Update terbilang display
function updateTerbilang(value) {
    const numValue = parseInt(value.replace(/\D/g, ''));
    const terbilangBox = document.getElementById('directIncomeTerbilang');
    const terbilangText = document.getElementById('directIncomeTerbilangText');
    
    if (!terbilangBox || !terbilangText) return;
    
    if (numValue > 0) {
        const words = numberToWords(numValue);
        terbilangText.textContent = words.charAt(0).toUpperCase() + words.slice(1) + ' rupiah';
        terbilangBox.classList.remove('hidden');
    } else {
        terbilangBox.classList.add('hidden');
    }
}

// Parse formatted currency to number for form submission
function parseCurrency(formattedValue) {
    return parseInt(formattedValue.replace(/\./g, '')) || 0;
}

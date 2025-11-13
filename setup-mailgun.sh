#!/bin/bash

# Mailgun Setup Script for Bizmark.id
# This script helps you quickly setup Mailgun for email sending

set -e

echo "=========================================="
echo "  Mailgun Setup for Bizmark.id"
echo "=========================================="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "⚠️  Please run as root (use sudo)"
    exit 1
fi

# Navigate to project directory
cd /root/Bizmark.id

echo "📦 Step 1: Installing Mailgun PHP SDK..."
composer require mailgun/mailgun-php symfony/mailgun-mailer guzzlehttp/guzzle --no-interaction

echo ""
echo "⚙️  Step 2: Updating Laravel configuration..."

# Backup config file
cp config/services.php config/services.php.backup

# Check if mailgun config already exists
if grep -q "mailgun" config/services.php; then
    echo "✅ Mailgun config already exists in services.php"
else
    # Add mailgun config
    cat >> config/services.php << 'EOF'

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.eu.mailgun.net'),
    ],
EOF
    echo "✅ Added Mailgun config to services.php"
fi

echo ""
echo "🔧 Step 3: Interactive Configuration"
echo ""

# Ask for Mailgun details
read -p "Enter your Mailgun Domain (e.g., mg.bizmark.id): " MAILGUN_DOMAIN
read -p "Enter your Mailgun API Key: " MAILGUN_SECRET
read -p "Choose region [1=EU (GDPR), 2=US]: " REGION_CHOICE

if [ "$REGION_CHOICE" == "1" ]; then
    MAILGUN_ENDPOINT="api.eu.mailgun.net"
    echo "Selected: EU Region (GDPR Compliant)"
else
    MAILGUN_ENDPOINT="api.mailgun.net"
    echo "Selected: US Region"
fi

read -p "Enter FROM email address (e.g., noreply@bizmark.id): " MAIL_FROM_ADDRESS
read -p "Enter FROM name (e.g., Bizmark.id): " MAIL_FROM_NAME

echo ""
echo "📝 Step 4: Updating .env file..."

# Backup .env
cp .env .env.backup

# Update or add environment variables
sed -i "s/^MAIL_MAILER=.*/MAIL_MAILER=mailgun/" .env
sed -i "s/^MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS=\"${MAIL_FROM_ADDRESS}\"/" .env
sed -i "s/^MAIL_FROM_NAME=.*/MAIL_FROM_NAME=\"${MAIL_FROM_NAME}\"/" .env

# Add Mailgun variables if not exist
if grep -q "MAILGUN_DOMAIN" .env; then
    sed -i "s/^MAILGUN_DOMAIN=.*/MAILGUN_DOMAIN=${MAILGUN_DOMAIN}/" .env
else
    echo "MAILGUN_DOMAIN=${MAILGUN_DOMAIN}" >> .env
fi

if grep -q "MAILGUN_SECRET" .env; then
    sed -i "s/^MAILGUN_SECRET=.*/MAILGUN_SECRET=${MAILGUN_SECRET}/" .env
else
    echo "MAILGUN_SECRET=${MAILGUN_SECRET}" >> .env
fi

if grep -q "MAILGUN_ENDPOINT" .env; then
    sed -i "s/^MAILGUN_ENDPOINT=.*/MAILGUN_ENDPOINT=${MAILGUN_ENDPOINT}/" .env
else
    echo "MAILGUN_ENDPOINT=${MAILGUN_ENDPOINT}" >> .env
fi

echo "✅ .env file updated"

echo ""
echo "🧹 Step 5: Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""
echo "✅ Mailgun setup complete!"
echo ""
echo "=========================================="
echo "  Next Steps:"
echo "=========================================="
echo ""
echo "1. 📧 Add these DNS records to bizmark.id:"
echo ""
echo "   Login to your DNS provider (Cloudflare, etc.) and add:"
echo ""
echo "   Type: TXT"
echo "   Name: ${MAILGUN_DOMAIN}"
echo "   Value: v=spf1 include:mailgun.org ~all"
echo ""
echo "   Type: CNAME"
echo "   Name: email.bizmark.id"
echo "   Value: mailgun.org"
echo ""
echo "   (Get complete DNS records from Mailgun Dashboard)"
echo ""
echo "2. ✅ Verify domain in Mailgun Dashboard"
echo "   https://app.mailgun.com/app/domains"
echo ""
echo "3. 🧪 Test email sending:"
echo "   - Go to: https://bizmark.id/admin/email/settings"
echo "   - Enter test email address"
echo "   - Click 'Send Test Email'"
echo ""
echo "4. 🚀 Start sending campaigns!"
echo ""
echo "=========================================="
echo ""

# Test configuration
echo "📊 Current Configuration:"
echo "  Mail Driver: mailgun"
echo "  Mailgun Domain: ${MAILGUN_DOMAIN}"
echo "  Mailgun Endpoint: ${MAILGUN_ENDPOINT}"
echo "  From Email: ${MAIL_FROM_ADDRESS}"
echo "  From Name: ${MAIL_FROM_NAME}"
echo ""

read -p "Would you like to send a test email now? (y/n): " TEST_EMAIL

if [ "$TEST_EMAIL" == "y" ] || [ "$TEST_EMAIL" == "Y" ]; then
    read -p "Enter your email address to receive test: " TEST_TO
    
    echo ""
    echo "📧 Sending test email to ${TEST_TO}..."
    
    php artisan tinker --execute="
        \Mail::raw('This is a test email from Bizmark.id using Mailgun!', function (\$message) {
            \$message->to('${TEST_TO}')
                    ->subject('Test Email from Bizmark.id');
        });
        echo 'Test email sent!';
    "
    
    echo ""
    echo "✅ Test email sent! Check your inbox (and spam folder)."
fi

echo ""
echo "🎉 Setup Complete! Your email system is ready."
echo ""

# Show DNS instructions again
echo "⚠️  IMPORTANT: Make sure to add DNS records shown above!"
echo "   Without DNS records, emails will NOT be delivered."
echo ""

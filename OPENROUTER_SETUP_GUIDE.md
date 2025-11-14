# OpenRouter API Setup Guide

## Quick Setup (5 minutes)

### 1. Get API Key

Visit: https://openrouter.ai/keys

**Steps:**
1. Create account or login
2. Click "Create Key"
3. Name it: "BizMark Production"
4. Copy the key (starts with `sk-or-v1-`)

**Important:** Save the key immediately - you can't view it again!

---

### 2. Add to Production .env

SSH into production server:

```bash
ssh root@srv1125853.hstgr.cloud
cd /var/www/bizmark.id
nano .env
```

Add these lines (or update if they exist):

```bash
# OpenRouter AI Configuration
OPENROUTER_API_KEY=sk-or-v1-YOUR_ACTUAL_KEY_HERE
OPENROUTER_MODEL=anthropic/claude-3.5-sonnet
OPENROUTER_FALLBACK_MODEL=google/gemini-pro-1.5
```

**Save:** CTRL+O, ENTER, CTRL+X

---

### 3. Clear Config Cache

```bash
cd /var/www/bizmark.id
php artisan config:clear
php artisan config:cache
```

---

### 4. Test the Integration

Visit production URL:
```
https://bizmark.id/client/services
```

**Test Flow:**
1. Search for a KBLI code (e.g., "46311")
2. Click on result
3. Select business scale and location
4. Click "Dapatkan Rekomendasi"
5. Wait 5-10 seconds (first time)
6. Verify AI results display

**Expected Output:**
- KBLI header with confidence score
- 3 summary cards (permits, cost, timeline)
- Mandatory permits list
- Required documents
- Timeline phases

---

### 5. Verify Configuration

Check if API key is loaded:

```bash
cd /var/www/bizmark.id
php artisan tinker
```

```php
config('services.openrouter.api_key')
// Should output: "sk-or-v1-..."

config('services.openrouter.model')
// Should output: "anthropic/claude-3.5-sonnet"
```

Exit: `exit`

---

## Cost Monitoring

### Check Current Costs

Visit: https://openrouter.ai/activity

**Metrics to Monitor:**
- Total requests
- Total cost
- Average cost per request
- Error rate

**Expected Usage:**
- First month: 300-500 requests (new KBLI codes)
- Ongoing: 50-100 requests/month (new codes only)
- Monthly cost: $4-8

---

## Troubleshooting

### Error: "Invalid API Key"

**Solution:**
```bash
# Check .env file
cat .env | grep OPENROUTER

# Ensure no quotes around key
OPENROUTER_API_KEY=sk-or-v1-xxxxx  # ✅ Correct
OPENROUTER_API_KEY="sk-or-v1-xxxxx"  # ❌ Wrong

# Clear cache
php artisan config:clear
```

---

### Error: "Rate Limit Exceeded"

**Free Tier Limits:**
- 200 requests/day
- 6000 requests/month

**Solution:** Upgrade to paid plan ($10 credit)

---

### Error: "Model Not Available"

**Solution:** Switch to fallback model

```bash
# In .env
OPENROUTER_MODEL=google/gemini-pro-1.5  # Cheaper fallback
```

---

### AI Returns Empty Response

**Check:**
1. Internet connection from server
2. Firewall allows outbound HTTPS
3. API key valid
4. Sufficient credits

**Test with curl:**
```bash
curl https://openrouter.ai/api/v1/chat/completions \
  -H "Authorization: Bearer sk-or-v1-YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "model": "anthropic/claude-3.5-sonnet",
    "messages": [{"role": "user", "content": "Test"}]
  }'
```

---

## Security Best Practices

### 1. Restrict API Key
- Set spending limit on OpenRouter dashboard
- Enable IP whitelisting (if supported)
- Rotate key every 90 days

### 2. Monitor Usage
- Set up email alerts for high costs
- Review activity weekly
- Check for suspicious requests

### 3. .env Protection
```bash
# Verify .env permissions
ls -l /var/www/bizmark.id/.env
# Should be: -rw-r----- (640)

# If not, fix:
chmod 640 /var/www/bizmark.id/.env
chown www-data:www-data /var/www/bizmark.id/.env
```

---

## Model Selection Guide

### Primary Model: Claude 3.5 Sonnet
**Best for:**
- Complex permit analysis
- Indonesian language understanding
- Structured JSON output
- High accuracy requirements

**Cost:** $3 per 1M input tokens, $15 per 1M output tokens  
**Average per request:** ~$0.015

---

### Fallback Model: Gemini Pro 1.5
**Best for:**
- Cost-sensitive applications
- Simpler KBLI codes
- High-volume usage

**Cost:** $1.25 per 1M input tokens, $5 per 1M output tokens  
**Average per request:** ~$0.005

---

### Alternative Models

If budget is very tight:

```bash
# Ultra-budget option
OPENROUTER_MODEL=google/gemini-flash-1.5
OPENROUTER_FALLBACK_MODEL=meta-llama/llama-3.1-70b-instruct

# Cost: ~$0.001 per request
# Trade-off: Lower accuracy, less detailed responses
```

---

## Cost Calculator

### Scenario 1: Startup Phase (Month 1)
- 500 unique KBLI searches
- 80% cache miss (400 AI calls)
- Model: Claude 3.5 Sonnet

```
400 calls × $0.015 = $6.00
```

---

### Scenario 2: Steady State (Month 2+)
- 1000 total searches
- 95% cache hit (50 AI calls)
- Model: Claude 3.5 Sonnet

```
50 calls × $0.015 = $0.75/month
```

---

### Scenario 3: High Volume (Month 6+)
- 5000 total searches
- 98% cache hit (100 AI calls)
- Model: Claude 3.5 Sonnet

```
100 calls × $0.015 = $1.50/month
```

**Conclusion:** System becomes cheaper over time as cache grows.

---

## Backup Plan (If OpenRouter Down)

### Option 1: Use Fallback Model
System automatically switches to Gemini Pro 1.5 if Claude fails.

### Option 2: Manual Override
```bash
# Temporarily disable AI, use static templates
cd /var/www/bizmark.id
nano .env

# Add:
AI_SYSTEM_ENABLED=false
```

Controller will show cached results only.

---

## Performance Optimization

### 1. Reduce Token Usage
- Current prompt: ~2000 tokens
- Potential optimization: ~1500 tokens (-25%)

**Edit:** `app/Services/OpenRouterService.php`

```php
private function buildPrompt(...)
{
    // Remove unnecessary examples
    // Shorten instructions
    // Keep format specification
}
```

---

### 2. Batch Processing (Future)
Process multiple KBLI codes in one API call:

```json
{
  "messages": [
    {
      "role": "user",
      "content": "Analyze these 10 KBLI codes: ..."
    }
  ]
}
```

**Savings:** 50% reduction in API calls

---

## Support Contacts

### OpenRouter Support
- Discord: https://discord.gg/openrouter
- Email: support@openrouter.ai
- Docs: https://openrouter.ai/docs

### BizMark Dev Team
- Check logs: `/var/www/bizmark.id/storage/logs/laravel.log`
- Database: `kbli_permit_recommendations` table
- AI logs: `ai_query_logs` table

---

## Configuration Reference

**Location:** `config/services.php`

```php
'openrouter' => [
    'api_key' => env('OPENROUTER_API_KEY'),
    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
    'model' => env('OPENROUTER_MODEL', 'anthropic/claude-3.5-sonnet'),
    'fallback_model' => env('OPENROUTER_FALLBACK_MODEL', 'google/gemini-pro-1.5'),
    'timeout' => env('OPENROUTER_TIMEOUT', 60),
    'max_tokens' => env('OPENROUTER_MAX_TOKENS', 4000),
],
```

**Default Values:**
- Base URL: https://openrouter.ai/api/v1
- Timeout: 60 seconds
- Max tokens: 4000

---

## Testing Commands

### Test KBLI Search API
```bash
curl -X GET "https://bizmark.id/api/kbli/search?q=perdagangan"
```

### Test AI Generation (via tinker)
```bash
php artisan tinker
```

```php
$service = app(\App\Services\KbliPermitCacheService::class);
$result = $service->getRecommendations('46311', 'kecil', 'perkotaan', 1);
dump($result->toArray());
```

### Check Cache Stats
```php
$service = app(\App\Services\KbliPermitCacheService::class);
dump($service->getCacheStats());
```

---

## Setup Checklist

- [ ] OpenRouter account created
- [ ] API key generated
- [ ] Key added to production .env
- [ ] Config cache cleared
- [ ] Spending limit set ($20/month recommended)
- [ ] Test search performed
- [ ] AI recommendation generated successfully
- [ ] Cache verified working
- [ ] Monitoring dashboard bookmarked
- [ ] Team notified of setup completion

---

**Document Version:** 1.0  
**Last Updated:** 2025-01-XX  
**Estimated Setup Time:** 5 minutes  
**Required Access:** Production server SSH, OpenRouter account

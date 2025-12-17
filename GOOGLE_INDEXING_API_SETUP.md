# Google Indexing API Setup Guide

## 🎯 Purpose
Google Indexing API allows instant URL submission to Google, reducing indexing time from 24-48 hours to 2-6 hours.

**Note:** This is **OPTIONAL**. Sitemap-based indexing (already configured) is sufficient for 99% of websites.

## 📋 Prerequisites
- Google Cloud Console access
- Google Search Console verified for your domain (bizmark.id)

## 🚀 Setup Steps

### Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click **Create Project**
3. Project name: `bizmark-seo-indexing`
4. Click **Create**

### Step 2: Enable Indexing API

1. In your project, go to **APIs & Services** > **Library**
2. Search for `Indexing API`
3. Click on **Web Search Indexing API**
4. Click **Enable**

### Step 3: Create Service Account

1. Go to **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **Service Account**
3. Service account details:
   - Name: `bizmark-indexing-bot`
   - ID: `bizmark-indexing-bot` (auto-generated)
   - Description: `Service account for automatic URL indexing`
4. Click **Create and Continue**
5. Grant role: **Owner** (or create custom role with indexing permissions)
6. Click **Continue** > **Done**

### Step 4: Generate JSON Key

1. Click on the created service account email
2. Go to **Keys** tab
3. Click **Add Key** > **Create new key**
4. Choose **JSON** format
5. Click **Create**
6. JSON file will download automatically (e.g., `bizmark-indexing-bot-xxxxx.json`)

### Step 5: Add Service Account to Search Console

1. Open the downloaded JSON file
2. Copy the `client_email` value (looks like: `bizmark-indexing-bot@project.iam.gserviceaccount.com`)
3. Go to [Google Search Console](https://search.google.com/search-console)
4. Select your property (bizmark.id)
5. Go to **Settings** > **Users and permissions**
6. Click **Add user**
7. Paste the service account email
8. Permission level: **Owner**
9. Click **Add**

### Step 6: Upload JSON Key to Server

1. Rename the downloaded JSON file to: `google-service-account.json`

2. Upload to server:
```bash
# Via SCP (from your local machine)
scp google-service-account.json user@bizmark.id:/home/bizmark/bizmark.id/storage/app/

# Or via server (if file is already on server)
mv google-service-account.json /home/bizmark/bizmark.id/storage/app/

# Set proper permissions
chmod 600 /home/bizmark/bizmark.id/storage/app/google-service-account.json
chown bizmark:bizmark /home/bizmark/bizmark.id/storage/app/google-service-account.json
```

### Step 7: Verify Installation

```bash
cd /home/bizmark/bizmark.id

# Check if file exists
ls -la storage/app/google-service-account.json

# Test the API (publish a test article or update existing one)
php artisan tinker
>>> $article = App\Models\Article::where('status', 'published')->first();
>>> $article->touch(); // This will trigger observer and API call
>>> exit

# Check logs
tail -50 storage/logs/laravel.log | grep -i "indexing"
```

Expected log output:
```
[2024-12-16 16:30:00] local.INFO: ✅ Indexing requested {"url":"https://bizmark.id/blog/..."}
```

## 🔍 Testing

### Test Individual URL
```bash
php artisan tinker

$service = app(App\Services\GoogleIndexingService::class);
$url = 'https://bizmark.id/blog/your-article-slug';
$result = $service->requestIndexing($url, 'URL_UPDATED');

if ($result) {
    echo "✅ Successfully submitted to Google!\n";
} else {
    echo "❌ Failed to submit\n";
}
```

### Test Batch URLs
```bash
php artisan tinker

$service = app(App\Services\GoogleIndexingService::class);
$urls = [
    'https://bizmark.id/blog/article-1',
    'https://bizmark.id/blog/article-2',
    'https://bizmark.id/blog/article-3'
];

$service->batchRequestIndexing($urls);
echo "✅ Batch submitted!\n";
```

## 📊 Monitoring

### Check API Status
```bash
# View recent indexing logs
tail -100 storage/logs/laravel.log | grep -E "Indexing|Google"

# Check observer triggers
tail -100 storage/logs/laravel.log | grep "Article published"
```

### Google Search Console
1. Go to [Google Search Console](https://search.google.com/search-console)
2. Select bizmark.id property
3. Check **Coverage** section
4. Look for indexed URLs (should increase after API setup)

## 🐛 Troubleshooting

### Error: "Permission denied"
**Problem:** Service account email not added to Search Console

**Solution:**
1. Verify service account email in Search Console > Settings > Users
2. Ensure permission level is **Owner**

### Error: "Invalid credentials"
**Problem:** JSON file corrupted or wrong format

**Solution:**
1. Download fresh JSON key from Google Cloud Console
2. Ensure file is named `google-service-account.json`
3. Check file permissions (600)

### Error: "API not enabled"
**Problem:** Indexing API not enabled in Google Cloud

**Solution:**
1. Go to Google Cloud Console > APIs & Services > Library
2. Search "Web Search Indexing API"
3. Click Enable

### No log entries after article publish
**Problem:** Observer not triggering or API calls failing silently

**Solution:**
```bash
# Check if observer is registered
php artisan tinker
>>> Event::hasListeners('eloquent.updated: App\Models\Article');
>>> exit

# Restart queue worker
php artisan queue:restart

# Check for error logs
grep -i "error" storage/logs/laravel.log | tail -20
```

## 📈 Expected Results

### Before API Setup (Sitemap Only)
- Indexing time: 24-48 hours
- Google discovers URLs by crawling sitemap
- Good for most websites

### After API Setup (Instant Indexing)
- Indexing time: 2-6 hours
- Direct notification to Google
- Better for time-sensitive content (news, promotions)

## 💡 Tips

1. **Rate Limits:**
   - 200 requests per day (quota)
   - System automatically adds 200ms delay between requests
   - Batch submissions recommended for multiple URLs

2. **When to Use:**
   - Breaking news or time-sensitive content
   - Important product launches
   - Critical updates to existing pages

3. **When NOT to Use:**
   - Sitemap method is already working well
   - Content is not time-sensitive
   - Don't want to manage Google Cloud setup

## 🔒 Security

- JSON key contains sensitive credentials
- File permissions set to 600 (owner read/write only)
- Never commit JSON file to git (already in .gitignore)
- Rotate keys periodically (every 90 days recommended)

## 📚 Resources

- [Google Indexing API Documentation](https://developers.google.com/search/apis/indexing-api/v3/quickstart)
- [Google Cloud Console](https://console.cloud.google.com/)
- [Google Search Console](https://search.google.com/search-console)

---

**Status:** Optional Enhancement  
**Priority:** Low (sitemap method already working)  
**Setup Time:** 15-20 minutes  
**Benefit:** Faster indexing (24-48h → 2-6h)

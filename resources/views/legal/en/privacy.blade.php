<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Bizmark.ID</title>
    <meta name="description" content="Bizmark.ID Privacy Policy regarding the collection, use, and protection of your personal data.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .prose h2 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-top: 3rem;
            margin-bottom: 1.25rem;
            color: #1e293b;
            letter-spacing: -0.025em;
            line-height: 1.2;
        }
        .prose h3 {
            font-size: 1.375rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #334155;
            letter-spacing: -0.015em;
        }
        .prose p {
            margin-bottom: 1.25rem;
            line-height: 1.75;
            color: #475569;
            font-size: 1rem;
        }
        .prose ul, .prose ol {
            margin-top: 1rem;
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }
        .prose li {
            margin-bottom: 0.75rem;
            line-height: 1.7;
            color: #475569;
        }
        .prose strong {
            font-weight: 600;
            color: #1e293b;
        }
        .toc-link {
            transition: all 0.2s ease;
        }
        .toc-link:hover {
            color: #2563eb;
            transform: translateX(4px);
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-50 to-white">

<!-- Header -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('landing.en') }}" class="text-xl font-bold text-blue-900">
                <i class="fas fa-certificate text-blue-600 mr-2"></i>Bizmark.ID
            </a>
            <a href="{{ route('landing.en') }}" class="text-sm text-gray-600 hover:text-blue-600">
                <i class="fas fa-arrow-left mr-2"></i>Back to Home
            </a>
        </div>
    </div>
</header>

<!-- Hero -->
<section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full mb-6">
                <i class="fas fa-shield-alt"></i>
                <span class="text-sm font-semibold">Legal Document</span>
            </div>
            <h1 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight" style="letter-spacing: -0.02em;">Privacy Policy</h1>
            <div class="flex items-center gap-8 text-blue-100">
                <div>
                    <p class="text-sm font-medium mb-1 opacity-75">Published by</p>
                    <p class="text-lg font-semibold">PT Cangah Pajaratan Mandiri</p>
                </div>
                <div class="h-12 w-px bg-white/20"></div>
                <div>
                    <p class="text-sm font-medium mb-1 opacity-75">Last updated</p>
                    <p class="text-lg font-semibold">{{ now()->format('F d, Y') }}</p>
                </div>
                <div class="h-12 w-px bg-white/20"></div>
                <div>
                    <p class="text-sm font-medium mb-1 opacity-75">Version</p>
                    <p class="text-lg font-semibold">2.0</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="grid lg:grid-cols-4 gap-12">
                <!-- Table of Contents -->
                <aside class="lg:col-span-1">
                    <div class="lg:sticky lg:top-24">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 mb-4">Contents</h3>
                        <nav class="space-y-2 text-sm">
                            <a href="#introduction" class="toc-link block text-gray-600 hover:text-blue-600">Introduction</a>
                            <a href="#information-collect" class="toc-link block text-gray-600 hover:text-blue-600">1. Information We Collect</a>
                            <a href="#how-we-use" class="toc-link block text-gray-600 hover:text-blue-600">2. How We Use Information</a>
                            <a href="#sharing" class="toc-link block text-gray-600 hover:text-blue-600">3. Information Sharing</a>
                            <a href="#security" class="toc-link block text-gray-600 hover:text-blue-600">4. Data Security</a>
                            <a href="#retention" class="toc-link block text-gray-600 hover:text-blue-600">5. Data Retention</a>
                            <a href="#your-rights" class="toc-link block text-gray-600 hover:text-blue-600">6. Your Rights</a>
                            <a href="#cookies" class="toc-link block text-gray-600 hover:text-blue-600">7. Cookies & Tracking</a>
                            <a href="#international" class="toc-link block text-gray-600 hover:text-blue-600">8. International Transfers</a>
                            <a href="#children" class="toc-link block text-gray-600 hover:text-blue-600">9. Children's Privacy</a>
                            <a href="#changes" class="toc-link block text-gray-600 hover:text-blue-600">10. Policy Changes</a>
                            <a href="#contact" class="toc-link block text-gray-600 hover:text-blue-600">11. Contact Us</a>
                        </nav>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <div class="prose prose-lg max-w-none">
            
            <div id="introduction" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">Introduction</h2>
                <p class="text-lg leading-relaxed">PT Cangah Pajaratan Mandiri (<strong>"Bizmark.ID"</strong>, <strong>"we"</strong>, <strong>"us"</strong>, or <strong>"our"</strong>) is committed to protecting the privacy and security of your personal information. This Privacy Policy explains how we collect, use, disclose, and protect information you provide when using our industrial permit consultation services, accessing our digital platform, or using the free digital tools available on our website.</p>
                <p>By using our services, platform, or digital tools, you agree to the collection and use of information in accordance with this policy. If you do not agree with this policy, please do not use our services.</p>
            </div>

            <div id="information-collect" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">1. Information We Collect</h2>
                <h3 class="text-blue-900">1.1 Personal Information</h3>
            <p>We collect the following types of personal information:</p>
            <ul>
                <li><strong>Contact Information:</strong> Name, email address, phone number, company address</li>
                <li><strong>Company Information:</strong> Company name, business registration number (NIB), industry sector, business scale</li>
                <li><strong>Service Information:</strong> Type of permits requested, project details, investment amount (for PMA clients)</li>
                <li><strong>Financial Information:</strong> Bank account details for payment processing</li>
                <li><strong>Communication Records:</strong> Emails, messages, meeting notes, and consultation records</li>
            </ul>

            <h3>1.2 Information from Free Digital Tools</h3>
            <p>When you use our free digital tools (such as the Polygon SHP Maker or Permit Calculator), we collect:</p>
            <ul>
                <li><strong>Applicant Data:</strong> Company name, contact person name, email address, and WhatsApp/phone number</li>
                <li><strong>Project/Location Data:</strong> Land or project name, administrative address (province, city, district, village), geographic coordinates (latitude/longitude), area measurements, and project description</li>
                <li><strong>Consent Data:</strong> Timestamp of Terms & Conditions acceptance when using the digital tools</li>
            </ul>

            <h3>1.3 Automatically Collected Information</h3>
            <ul>
                <li>IP address and browser information</li>
                <li>Device information and operating system (user agent)</li>
                <li>Access time and duration</li>
                <li>Pages visited on our website</li>
                <li>Cookies and similar tracking technologies</li>
            </ul>

            <h3>1.4 Local Storage (localStorage)</h3>
            <p>Some of our digital tools use the localStorage feature of your browser to automatically save data temporarily (auto-save), such as form data you are filling out. This data:</p>
            <ul>
                <li>Is stored locally on your device, not on our servers</li>
                <li>Can be cleared through browser settings or the "Clear Data" feature in the respective digital tool</li>
                <li>Is used to recover your work if the browser is closed unexpectedly</li>
                <li>Is not transmitted to our servers until you actively submit the form</li>
            </ul>
            </div>

            <div id="how-we-use" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">2. How We Use Your Information</h2>
            <p>We use the collected information for the following purposes:</p>
            <ul>
                <li><strong>Service Delivery:</strong> Process permit applications, prepare required documents, communicate with government agencies</li>
                <li><strong>Client Communication:</strong> Provide updates, answer inquiries, send service notifications</li>
                <li><strong>Payment Processing:</strong> Issue invoices, process payments, maintain transaction records</li>
                <li><strong>Legal Compliance:</strong> Fulfill legal obligations, maintain records as required by Indonesian law</li>
                <li><strong>Service Improvement:</strong> Analyze service usage and digital tools, improve our offerings, develop new services</li>
                <li><strong>Marketing:</strong> Send relevant information about our services (with your consent)</li>
                <li><strong>Lead Follow-up:</strong> Data collected through free digital tools (Polygon SHP Maker, Permit Calculator) will be used to contact you to offer consultation services relevant to your project or needs</li>
            </ul>
            </div>

            <div id="sharing" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">3. Information Sharing and Disclosure</h2>
            <p>We may share your information with:</p>
            <ul>
                <li><strong>Government Authorities:</strong> Ministry of Environment, BKPM, OSS, and other relevant agencies for permit processing</li>
                <li><strong>Service Providers:</strong> Third-party consultants, legal advisors, technical experts assisting in service delivery</li>
                <li><strong>Payment Processors:</strong> Banks and payment gateways for transaction processing</li>
                <li><strong>Legal Requirements:</strong> When required by law, court order, or government regulation</li>
            </ul>
            <p><strong>We do not sell, rent, or trade your personal information to third parties for marketing purposes.</strong></p>
            </div>

            <div id="security" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">4. Data Security</h2>
            <p>We implement appropriate technical and organizational measures to protect your personal information:</p>
            <ul>
                <li>Encryption of sensitive data in transit and at rest</li>
                <li>Secure access controls and authentication</li>
                <li>Regular security assessments and updates</li>
                <li>Employee training on data protection</li>
                <li>Confidentiality agreements with staff and partners</li>
            </ul>
            <p>However, no method of transmission over the Internet or electronic storage is 100% secure. While we strive to protect your information, we cannot guarantee absolute security.</p>
            </div>

            <div id="retention" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">5. Data Retention</h2>
            <p>We retain your personal information for:</p>
            <ul>
                <li><strong>Active Services:</strong> Duration of service engagement plus any warranty period</li>
                <li><strong>Legal Requirements:</strong> Minimum 10 years as required by Indonesian accounting and tax regulations</li>
                <li><strong>Archive Records:</strong> Longer retention periods for permits and compliance documentation</li>
            </ul>
            </div>

            <div id="your-rights" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">6. Your Rights</h2>
            <p>Under Indonesian data protection regulations, you have the right to:</p>
            <ul>
                <li><strong>Access:</strong> Request copies of your personal information</li>
                <li><strong>Correction:</strong> Request correction of inaccurate or incomplete data</li>
                <li><strong>Deletion:</strong> Request deletion of your data (subject to legal retention requirements)</li>
                <li><strong>Objection:</strong> Object to processing of your data for marketing purposes</li>
                <li><strong>Data Portability:</strong> Request transfer of your data to another service provider</li>
                <li><strong>Withdraw Consent:</strong> Withdraw consent for data processing at any time</li>
            </ul>
            </div>

            <div id="cookies" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">7. Cookies and Tracking</h2>
            <p>We use cookies and similar technologies to:</p>
            <ul>
                <li>Remember your preferences and settings</li>
                <li>Analyze website traffic and usage patterns</li>
                <li>Improve user experience</li>
                <li>Provide personalized content</li>
            </ul>
            <p>You can control cookies through your browser settings. However, disabling cookies may limit some website functionality.</p>
            </div>

            <div id="international" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">8. International Data Transfers</h2>
            <p>For foreign investment (PMA) clients, we may transfer data internationally for:</p>
            <ul>
                <li>Communication with parent companies or overseas partners</li>
                <li>Compliance with foreign investment regulations</li>
                <li>International payment processing</li>
            </ul>
            <p>We ensure adequate safeguards are in place for international transfers.</p>
            </div>

            <div id="children" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">9. Children's Privacy</h2>
            <p>Our services are not directed to individuals under 18 years of age. We do not knowingly collect personal information from children.</p>
            </div>

            <div id="changes" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">10. Changes to This Policy</h2>
            <p>We may update this Privacy Policy periodically. We will notify you of significant changes by:</p>
            <ul>
                <li>Posting the updated policy on our website</li>
                <li>Sending email notification to registered clients</li>
                <li>Displaying a prominent notice on our website</li>
            </ul>
            <p>Continued use of our services after changes indicates acceptance of the updated policy.</p>
            </div>

            <div id="contact" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">11. Contact Us</h2>
                <p>For questions, concerns, or requests regarding this Privacy Policy or your personal data, please contact us:</p>
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl border-2 border-blue-200 not-prose shadow-lg"
                <h3 class="text-lg font-bold mb-4">PT Cangah Pajaratan Mandiri</h3>
                <div class="space-y-2 text-sm">
                    <p><i class="fas fa-envelope text-blue-600 w-5"></i> <strong>Email:</strong> cs@bizmark.id</p>
                    <p><i class="fas fa-phone text-blue-600 w-5"></i> <strong>Phone:</strong> +62 838 7960 2855</p>
                    <p><i class="fas fa-map-marker-alt text-blue-600 w-5"></i> <strong>Address:</strong> Jln Lingkar Luar Karawang. Ruko Permata Sari Indah No.2, Karawang, Jawa Barat 41314</p>
                </div>
            </div>

            <div class="mt-12 p-8 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-2xl shadow-xl">
                <div class="flex items-start gap-4">
                    <i class="fas fa-info-circle text-2xl mt-1"></i>
                    <div>
                        <p class="font-semibold text-lg mb-2">Legal Compliance</p>
                        <p class="text-blue-100 text-sm leading-relaxed mb-0">This Privacy Policy is governed by Indonesian law and complies with <strong>Law No. 27 of 2022 on Personal Data Protection (UU PDP)</strong> and international best practices including GDPR principles.</p>
                    </div>
                </div>
            </div>
            </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-300 py-8">
    <div class="container mx-auto px-4 text-center">
        <p class="text-sm">&copy; {{ date('Y') }} Bizmark.ID. All rights reserved.</p>
        <div class="mt-4 space-x-4">
            <a href="{{ route('privacy.policy.en') }}" class="text-sm hover:text-white">Privacy Policy</a>
            <a href="{{ route('terms.conditions.en') }}" class="text-sm hover:text-white">Terms & Conditions</a>
            <a href="{{ route('landing.en') }}" class="text-sm hover:text-white">Home</a>
        </div>
    </div>
</footer>

</body>
</html>

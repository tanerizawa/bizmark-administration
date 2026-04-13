<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - Bizmark.ID</title>
    <meta name="description" content="Terms and Conditions for using Bizmark.ID permit consultation services.">
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
<section class="bg-gradient-to-br from-indigo-600 to-indigo-800 text-white py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full mb-6">
                <i class="fas fa-file-contract"></i>
                <span class="text-sm font-semibold">Legal Document</span>
            </div>
            <h1 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight" style="letter-spacing: -0.02em;">Terms & Conditions</h1>
            <div class="flex items-center gap-8 text-indigo-100">
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
                            <a href="#introduction" class="toc-link block text-gray-600 hover:text-indigo-600">Introduction</a>
                            <a href="#services" class="toc-link block text-gray-600 hover:text-indigo-600">1. Services Provided</a>
                            <a href="#limitations" class="toc-link block text-gray-600 hover:text-indigo-600">2. Service Limitations</a>
                            <a href="#obligations" class="toc-link block text-gray-600 hover:text-indigo-600">3. Client Obligations</a>
                            <a href="#fees" class="toc-link block text-gray-600 hover:text-indigo-600">4. Fees & Payment</a>
                            <a href="#timeline" class="toc-link block text-gray-600 hover:text-indigo-600">5. Service Timeline</a>
                            <a href="#confidentiality" class="toc-link block text-gray-600 hover:text-indigo-600">6. Confidentiality</a>
                            <a href="#intellectual" class="toc-link block text-gray-600 hover:text-indigo-600">7. Intellectual Property</a>
                            <a href="#liability" class="toc-link block text-gray-600 hover:text-indigo-600">8. Limitation of Liability</a>
                            <a href="#termination" class="toc-link block text-gray-600 hover:text-indigo-600">9. Termination</a>
                            <a href="#dispute" class="toc-link block text-gray-600 hover:text-indigo-600">10. Dispute Resolution</a>
                            <a href="#force-majeure" class="toc-link block text-gray-600 hover:text-indigo-600">11. Force Majeure</a>
                            <a href="#amendments" class="toc-link block text-gray-600 hover:text-indigo-600">12. Amendments</a>
                            <a href="#digital-tools" class="toc-link block text-gray-600 hover:text-indigo-600">13. Digital Tools Usage</a>
                            <a href="#contact" class="toc-link block text-gray-600 hover:text-indigo-600">Contact Information</a>
                        </nav>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <div class="prose prose-lg max-w-none">
            
            <div id="introduction" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">Introduction</h2>
                <p class="text-lg leading-relaxed">Welcome to <strong>Bizmark.ID</strong>, a service operated by <strong>PT Cangah Pajaratan Mandiri</strong>. These Terms and Conditions (<strong>"Terms"</strong>) govern your use of our industrial permit and environmental consultation services, digital platform, and free digital tools available on our website.</p>
                <p>By engaging our services or using any of our digital tools, you agree to be bound by these Terms. Please read them carefully before proceeding with any service request or tool usage.</p>
            </div>

            <div id="services" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">1. Services Provided</h2>
                <h3 class="text-indigo-900">1.1 Scope of Services</h3>
                <p>Bizmark.ID provides professional consultation services for:</p>
                <ul>
                    <li>Industrial and environmental permits (AMDAL, UKL-UPL, SPPL)</li>
                    <li>B3 Waste Management permits</li>
                    <li>Foreign Investment (PMA) establishment and permits</li>
                    <li>Business licensing (OSS, NIB)</li>
                    <li>Building permits (PBG, SLF)</li>
                    <li>Import licenses and customs clearance</li>
                    <li>Compliance consulting and audit</li>
                    <li>Digital environmental monitoring (IoT-based real-time systems)</li>
                </ul>

                <h3 class="text-indigo-900">1.2 Free Digital Tools</h3>
                <p>In addition to paid services, Bizmark.ID provides free digital tools accessible through our platform:</p>
                <ul>
                    <li><strong>Polygon SHP Maker:</strong> Create ESRI Shapefile (.shp) files for OSS submissions, land planning, and project mapping</li>
                    <li><strong>Permit Calculator:</strong> Estimate costs and timelines for permit processing based on industry type and permit category</li>
                </ul>
                <p>Free digital tools are provided "as-is" to assist users. Usage of these tools is subject to these Terms and our Privacy Policy.</p>
            </div>

            <div id="limitations" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">2. Service Limitations</h2>
            <p>Our services are limited to:</p>
            <ul>
                <li>Consultation and advisory services</li>
                <li>Document preparation and submission</li>
                <li>Liaison with government authorities</li>
                <li>Progress monitoring and reporting</li>
            </ul>
            <p><strong>We do not:</strong> Guarantee permit approval, engage in illegal activities, or provide legal representation in court.</p>
            </div>

            <div id="obligations" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">3. Client Obligations</h2>
                <h3 class="text-indigo-900">3.1 Information Accuracy</h3>
            <p>Clients must:</p>
            <ul>
                <li>Provide accurate and complete information</li>
                <li>Submit required documents promptly</li>
                <li>Inform us of any changes to provided information</li>
                <li>Ensure all information is truthful and verifiable</li>
            </ul>

            <h3 class="text-indigo-900">3.2 Compliance</h3>
            <p>Clients must:</p>
            <ul>
                <li>Comply with all applicable Indonesian laws and regulations</li>
                <li>Maintain valid business registration</li>
                <li>Obtain necessary internal approvals before engaging our services</li>
                <li>Cooperate with government inspections and audits</li>
            </ul>
            </div>

            <div id="fees" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">4. Fees and Payment</h2>
                <h3 class="text-indigo-900">4.1 Service Fees</h3>
            <ul>
                <li><strong>Consultation Fee:</strong> As quoted in the service proposal</li>
                <li><strong>Government Fees:</strong> PNBP and other official charges (separate from our fees)</li>
                <li><strong>Third-Party Costs:</strong> Laboratory tests, surveys, legal opinions (if required)</li>
                <li><strong>Travel Expenses:</strong> For on-site visits and document submission</li>
            </ul>

            <h3 class="text-indigo-900">4.2 Payment Terms</h3>
            <ul>
                <li><strong>Down Payment:</strong> 30-50% upon service agreement signing</li>
                <li><strong>Progress Payment:</strong> As specified in the agreement</li>
                <li><strong>Final Payment:</strong> Before permit delivery</li>
                <li><strong>Payment Method:</strong> Bank transfer to our registered account</li>
            </ul>

            <h3 class="text-indigo-900">4.3 Late Payment</h3>
            <p>Late payments may result in:</p>
            <ul>
                <li>Suspension of service delivery</li>
                <li>Late payment interest of 1% per month</li>
                <li>Additional administrative fees</li>
            </ul>
            </div>

            <div id="timeline" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">5. Service Timeline</h2>
            <h3 class="text-indigo-900">5.1 Estimated Timeline</h3>
            <p>Service timelines are estimates based on normal circumstances and government processing times. Actual timelines may vary due to:</p>
            <ul>
                <li>Complexity of the permit application</li>
                <li>Completeness of submitted documents</li>
                <li>Government agency workload and policies</li>
                <li>Required additional studies or inspections</li>
                <li>Public holidays and government schedule changes</li>
            </ul>

            <h3 class="text-indigo-900">5.2 Delays</h3>
            <p>We are not liable for delays caused by:</p>
            <ul>
                <li>Government processing delays</li>
                <li>Changes in regulations or policies</li>
                <li>Client-side delays in document submission</li>
                <li>Force majeure events</li>
            </ul>
            </div>

            <div id="confidentiality" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">6. Confidentiality</h2>
            <h3 class="text-indigo-900">6.1 Non-Disclosure</h3>
            <p>We maintain strict confidentiality of:</p>
            <ul>
                <li>Client business information</li>
                <li>Trade secrets and proprietary data</li>
                <li>Financial information</li>
                <li>Strategic plans and investment details</li>
            </ul>

            <h3 class="text-indigo-900">6.2 Permitted Disclosure</h3>
            <p>Information may be disclosed to:</p>
            <ul>
                <li>Government authorities (as required for permit processing)</li>
                <li>Third-party consultants (under confidentiality agreements)</li>
                <li>Legal authorities (when legally required)</li>
            </ul>
            </div>

            <div id="intellectual" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">7. Intellectual Property</h2>
            <ul>
                <li><strong>Work Product:</strong> Documents prepared for permit applications belong to the client upon full payment</li>
                <li><strong>Methodology:</strong> Our consultation methods, templates, and processes remain our intellectual property</li>
                <li><strong>Permits:</strong> Issued permits are government property granted to the client</li>
            </ul>
            </div>

            <div id="liability" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">8. Limitation of Liability</h2>
                <h3 class="text-indigo-900">8.1 Service Liability</h3>
            <p>Our liability is limited to:</p>
            <ul>
                <li>Refund of fees paid for undelivered services</li>
                <li>Resubmission of rejected applications (one revision included)</li>
                <li>Professional errors covered by our insurance policy</li>
            </ul>

            <h3 class="text-indigo-900">8.2 Exclusions</h3>
            <p>We are not liable for:</p>
            <ul>
                <li>Permit rejection due to client non-compliance</li>
                <li>Business losses or opportunity costs</li>
                <li>Changes in government regulations after service commencement</li>
                <li>Third-party actions or negligence</li>
                <li>Force majeure events</li>
            </ul>
            </div>

            <div id="termination" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">9. Termination</h2>
                <h3 class="text-indigo-900">9.1 By Client</h3>
            <p>Clients may terminate services with:</p>
            <ul>
                <li>30 days written notice</li>
                <li>Payment for work completed to date</li>
                <li>Reimbursement of expenses incurred</li>
            </ul>

            <h3 class="text-indigo-900">9.2 By Bizmark.ID</h3>
            <p>We may terminate services if:</p>
            <ul>
                <li>Client fails to provide required information or documents</li>
                <li>Payment obligations are not met</li>
                <li>Client engages in illegal activities</li>
                <li>Service becomes impossible due to regulatory changes</li>
            </ul>
            </div>

            <div id="dispute" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">10. Dispute Resolution</h2>
            <h3 class="text-indigo-900">10.1 Amicable Resolution</h3>
            <p>Disputes should first be resolved through:</p>
            <ul>
                <li>Direct negotiation between parties</li>
                <li>Mediation by mutually agreed mediator</li>
            </ul>

            <h3 class="text-indigo-900">10.2 Legal Action</h3>
            <p>If amicable resolution fails:</p>
            <ul>
                <li>Disputes shall be resolved through Indonesian courts</li>
                <li>Jurisdiction: Jakarta District Court</li>
                <li>Governing Law: Laws of the Republic of Indonesia</li>
            </ul>
            </div>

            <div id="force-majeure" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">11. Force Majeure</h2>
            <p>Neither party is liable for failure to perform obligations due to:</p>
            <ul>
                <li>Natural disasters (earthquakes, floods, etc.)</li>
                <li>Government actions (policy changes, lockdowns)</li>
                <li>Civil unrest or strikes</li>
                <li>Pandemics or health emergencies</li>
                <li>Internet or communication system failures</li>
            </ul>
            </div>

            <div id="amendments" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">12. Amendments</h2>
            <p>We reserve the right to modify these terms at any time. Changes will be communicated through:</p>
            <ul>
                <li>Website announcement</li>
                <li>Email notification to active clients</li>
            </ul>
            <p>Continued use of services after changes indicates acceptance.</p>
            </div>

            <div id="digital-tools" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">13. Digital Tools Usage</h2>
                <h3 class="text-indigo-900">13.1 Terms of Use for Free Digital Tools</h3>
            <p>By using the free digital tools available on Bizmark.ID (including the Polygon SHP Maker, Permit Calculator, and any other tools that may be added), you agree that:</p>
            <ul>
                <li>You must provide accurate data when filling out required forms to use the digital tools</li>
                <li>Data you enter (including company name, email, phone number, contact name, and location/project data) will be stored by Bizmark.ID as lead data for commercial follow-up purposes</li>
                <li>Bizmark.ID reserves the right to contact you via email or WhatsApp/phone to offer related services</li>
                <li>You are responsible for the accuracy of data entered into the digital tools</li>
                <li>Results generated by digital tools are estimates and cannot be used as legal basis</li>
            </ul>

            <h3 class="text-indigo-900">13.2 Data Collection through Digital Tools</h3>
            <p>When using our free digital tools, the following data is collected and stored:</p>
            <ul>
                <li><strong>Applicant Data:</strong> Company name, contact person, email address, and WhatsApp/phone number</li>
                <li><strong>Project/Location Data:</strong> Land/project name, administrative address (province, city, district, village), geographic coordinates, area measurements, and project description</li>
                <li><strong>Technical Data:</strong> IP address, browser information (user agent), and terms acceptance timestamp</li>
                <li><strong>Local Storage:</strong> Your browser may store temporary data (localStorage) for auto-save features. This data is stored on your device and can be cleared through browser settings</li>
            </ul>

            <h3 class="text-indigo-900">13.3 Limitation of Liability for Digital Tools</h3>
            <p>Bizmark.ID is not liable for:</p>
            <ul>
                <li>Inaccuracy of calculations or outputs from digital tools</li>
                <li>Losses arising from using digital tool results as a basis for business decisions</li>
                <li>Technical failures, server disruptions, or unavailability of digital tools</li>
                <li>Loss of data stored locally in the user's browser</li>
                <li>Misuse of files or data generated by digital tools by third parties</li>
            </ul>

            <h3 class="text-indigo-900">13.4 Prohibited Uses</h3>
            <p>Users are prohibited from:</p>
            <ul>
                <li>Using digital tools for illegal or unlawful purposes</li>
                <li>Scraping, reverse engineering, or automated exploitation of digital tools</li>
                <li>Entering false or misleading data</li>
                <li>Using digital tools in a manner that may damage, disable, or overload our servers</li>
                <li>Redistributing or modifying the digital tools without written permission</li>
            </ul>
            </div>

            <div id="contact" class="scroll-mt-24">
                <h2 class="border-b-2 border-gray-200 pb-4">Contact Information</h2>
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-8 rounded-2xl border-2 border-indigo-200 not-prose shadow-lg">
                <h3 class="text-lg font-bold mb-4">PT Cangah Pajaratan Mandiri</h3>
                <div class="space-y-2 text-sm">
                    <p><i class="fas fa-envelope text-blue-600 w-5"></i> <strong>Email:</strong> cs@bizmark.id</p>
                    <p><i class="fas fa-phone text-blue-600 w-5"></i> <strong>Phone:</strong> +62 838 7960 2855</p>
                    <p><i class="fas fa-map-marker-alt text-blue-600 w-5"></i> <strong>Address:</strong> Jln Lingkar Luar Karawang. Ruko Permata Sari Indah No.2, Karawang, Jawa Barat 41314</p>
                    <p><i class="fas fa-globe text-blue-600 w-5"></i> <strong>Website:</strong> https://bizmark.id</p>
                </div>
            </div>

            <div class="mt-12 p-8 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-2xl shadow-xl">
                <div class="flex items-start gap-4">
                    <i class="fas fa-gavel text-2xl mt-1"></i>
                    <div>
                        <p class="font-semibold text-lg mb-2">Governing Law</p>
                        <p class="text-indigo-100 text-sm leading-relaxed mb-0">These Terms and Conditions are governed by and construed in accordance with the laws of the Republic of Indonesia. Any dispute arising from these Terms shall be subject to the exclusive jurisdiction of the <strong>District Court of Jakarta</strong>.</p>
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Submitted - Bizmark.ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-sans antialiased bg-gray-50">

<nav class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('landing.en') }}" class="text-xl font-bold text-blue-900">
                <i class="fas fa-certificate text-blue-600 mr-2"></i>Bizmark.ID
            </a>
        </div>
    </div>
</nav>

<div class="min-h-screen py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            
            <!-- Success Icon -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-100 mb-6">
                    <i class="fas fa-check-circle text-5xl text-green-600"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Inquiry Submitted Successfully!</h1>
                <p class="text-xl text-gray-600">
                    Thank you for your interest in investing in Indonesia
                </p>
            </div>

            <!-- Inquiry Details -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Inquiry Number</div>
                            <div class="text-2xl font-bold text-blue-900">{{ $inquiry->inquiry_number }}</div>
                        </div>
                        <div class="px-4 py-2 bg-green-100 text-green-800 rounded-lg font-semibold">
                            {{ ucfirst($inquiry->status) }}
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-user text-blue-900 mt-1"></i>
                        <div>
                            <div class="text-sm text-gray-500">Contact Person</div>
                            <div class="font-semibold text-gray-900">{{ $inquiry->contact_person }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <i class="fas fa-envelope text-blue-900 mt-1"></i>
                        <div>
                            <div class="text-sm text-gray-500">Email</div>
                            <div class="font-semibold text-gray-900">{{ $inquiry->email }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <i class="fas fa-building text-blue-900 mt-1"></i>
                        <div>
                            <div class="text-sm text-gray-500">Company</div>
                            <div class="font-semibold text-gray-900">{{ $inquiry->company_name }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <i class="fas fa-calendar text-blue-900 mt-1"></i>
                        <div>
                            <div class="text-sm text-gray-500">Submitted</div>
                            <div class="font-semibold text-gray-900">{{ $inquiry->created_at->format('F d, Y \a\t H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- What's Next -->
            <div class="bg-gradient-to-br from-blue-900 to-blue-800 text-white rounded-2xl p-8 mb-8">
                <h2 class="text-2xl font-bold mb-6">What Happens Next?</h2>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-blue-900 flex items-center justify-center font-bold">1</div>
                        <div>
                            <h3 class="font-bold mb-1">Email Confirmation</h3>
                            <p class="text-blue-100 text-sm">You'll receive a confirmation email within minutes with your inquiry details.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-blue-900 flex items-center justify-center font-bold">2</div>
                        <div>
                            <h3 class="font-bold mb-1">Expert Review (24 Hours)</h3>
                            <p class="text-blue-100 text-sm">Our investment specialists will review your requirements and prepare a customized consultation.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-blue-900 flex items-center justify-center font-bold">3</div>
                        <div>
                            <h3 class="font-bold mb-1">Personalized Response</h3>
                            <p class="text-blue-100 text-sm">We'll contact you via your preferred method with detailed guidance and next steps.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-blue-900 flex items-center justify-center font-bold">4</div>
                        <div>
                            <h3 class="font-bold mb-1">Roadmap & Timeline</h3>
                            <p class="text-blue-100 text-sm">Receive a comprehensive investment roadmap with timelines, costs, and requirements.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-question-circle text-blue-900"></i>
                        Have Questions?
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Our team is available to answer any immediate concerns</p>
                    <div class="space-y-2">
                        @php
                            $contact = (array) data_get(config('landing_metrics'), 'contact', []);
                            $whatsappBase = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
                            $supportEmail = $contact['email'] ?? 'info@bizmark.id';
                            $waText = "Hi, I submitted inquiry {$inquiry->inquiry_number}";
                            $waHref = $whatsappBase . (str_contains($whatsappBase, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
                            $mailSubject = "Inquiry {$inquiry->inquiry_number}";
                            $mailHref = 'mailto:' . $supportEmail . '?subject=' . rawurlencode($mailSubject);
                        @endphp
                        <a href="{{ $waHref }}" 
                           target="_blank"
                           rel="noopener"
                           class="block w-full px-4 py-2 bg-green-600 text-white rounded-lg text-center font-semibold hover:bg-green-700 transition text-sm">
                            <i class="fab fa-whatsapp mr-2"></i>WhatsApp Support
                        </a>
                        <a href="{{ $mailHref }}" 
                           class="block w-full px-4 py-2 bg-blue-900 text-white rounded-lg text-center font-semibold hover:bg-blue-800 transition text-sm">
                            <i class="fas fa-envelope mr-2"></i>Email Us
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-book text-blue-900"></i>
                        Learn More
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">While waiting, explore our resources</p>
                    <div class="space-y-2 text-sm">
                        <a href="{{ route('services.index.en') }}" class="block text-blue-900 hover:text-blue-700 transition">
                            <i class="fas fa-arrow-right mr-2"></i>View All Services
                        </a>
                        <a href="{{ route('landing.en') }}#process" class="block text-blue-900 hover:text-blue-700 transition">
                            <i class="fas fa-arrow-right mr-2"></i>Investment Process
                        </a>
                        <a href="{{ route('blog.index.en') }}" class="block text-blue-900 hover:text-blue-700 transition">
                            <i class="fas fa-arrow-right mr-2"></i>Blog & Resources
                        </a>
                    </div>
                </div>
            </div>

            <!-- Save Reference -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-700">
                    <i class="fas fa-bookmark text-yellow-600 mr-2"></i>
                    Save your inquiry number <strong>{{ $inquiry->inquiry_number }}</strong> for reference
                </p>
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-8">
                <a href="{{ route('landing.en') }}" class="inline-flex items-center text-blue-900 hover:text-blue-700 font-semibold transition">
                    <i class="fas fa-home mr-2"></i>Return to Homepage
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>

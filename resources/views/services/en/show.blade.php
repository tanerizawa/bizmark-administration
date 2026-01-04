<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $service['title'] }} - Bizmark.ID</title>
    <meta name="description" content="{{ $service['short_description'] }}">
    <meta name="keywords" content="{{ $service['meta_keywords'] ?? 'Indonesia investment, foreign investment, PMA services' }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $service['title'] }} - Bizmark.ID">
    <meta property="og:description" content="{{ $service['short_description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_US">
    
    <link rel="canonical" href="https://bizmark.id/en/services/{{ $service['slug'] ?? '' }}">
    <link rel="alternate" hreflang="id" href="https://bizmark.id/id/services/{{ $service['slug'] ?? '' }}">
    <link rel="alternate" hreflang="en" href="https://bizmark.id/en/services/{{ $service['slug'] ?? '' }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-sans antialiased bg-white text-gray-900">

<!-- Simple Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('landing.en') }}" class="text-xl font-bold text-blue-900">
                <i class="fas fa-certificate text-blue-600 mr-2"></i>Bizmark.ID
            </a>
            <div class="flex items-center gap-4">
                <x-locale-switcher />
                <a href="{{ route('landing.en') }}" class="text-sm text-gray-600 hover:text-blue-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Breadcrumb -->
<section class="bg-gray-50 py-6 mt-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="{{ route('landing.en') }}" class="hover:text-blue-600 transition">
                <i class="fas fa-home mr-2"></i>Home
            </a>
            <i class="fas fa-chevron-right mx-3 text-gray-400 text-xs"></i>
            <a href="{{ route('services.index.en') }}" class="hover:text-blue-600 transition">Services</a>
            <i class="fas fa-chevron-right mx-3 text-gray-400 text-xs"></i>
            <span class="text-gray-900 font-medium">{{ $service['title'] }}</span>
        </nav>
    </div>
</section>

<!-- Hero Section -->
<section class="py-12 bg-gradient-to-br from-white via-gray-50 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row items-start gap-8">
                <!-- Icon -->
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-3xl flex items-center justify-center flex-shrink-0 shadow-lg" 
                     style="background: linear-gradient(135deg, {{ $service['color'] }}20 0%, {{ $service['color'] }}40 100%);">
                    <i class="fas {{ $service['icon'] }} text-4xl md:text-5xl" style="color: {{ $service['color'] }};"></i>
                </div>
                
                <!-- Content -->
                <div class="flex-1">
                    <span class="inline-block px-4 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-semibold mb-4">
                        Professional Service
                    </span>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-gray-900">
                        {{ $service['title'] }}
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 leading-relaxed mb-6">
                        {{ $service['short_description'] }}
                    </p>
                    
                    <!-- Pricing & Duration -->
                    @if(isset($service['pricing']))
                    <div class="flex flex-wrap items-center gap-6 mb-6 p-6 bg-white rounded-xl border border-gray-200">
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Starting From</div>
                            <div class="text-2xl font-bold text-blue-900">{{ $service['pricing']['display'] }}</div>
                        </div>
                        <div class="h-12 w-px bg-gray-200"></div>
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Processing Time</div>
                            <div class="text-lg font-semibold text-gray-700">{{ $service['duration'] }}</div>
                        </div>
                        @if(isset($service['requirements_count']))
                        <div class="h-12 w-px bg-gray-200"></div>
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Requirements</div>
                            <div class="text-lg font-semibold text-gray-700">{{ $service['requirements_count'] }} documents</div>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Quick Actions -->
                    <div class="flex flex-wrap gap-3">
                        <a href="https://wa.me/6283879602855?text=Hello,%20I'm%20interested%20in%20{{ urlencode($service['title']) }}" 
                           target="_blank" 
                           class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                            <i class="fab fa-whatsapp mr-2"></i>WhatsApp Consultation
                        </a>
                        <a href="mailto:cs@bizmark.id?subject={{ urlencode($service['title']) }}%20Inquiry" 
                           class="px-6 py-3 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                            <i class="fas fa-envelope mr-2"></i>Email Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Details -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="md:col-span-2 space-y-8">
                    
                    <!-- Narrative Introduction -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-l-4 border-blue-600 rounded-r-xl p-8 shadow-sm">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-lightbulb text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">Understanding {{ $service['title'] }}</h2>
                                <p class="text-sm text-blue-900 font-medium">Essential insights for foreign investors in Indonesia</p>
                            </div>
                        </div>
                        
                        @if($service['slug'] === 'investment-registration')
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">Establishing your investment presence in Indonesia begins with BKPM (Badan Koordinasi Penanaman Modal) - the Investment Coordinating Board.</p>
                            
                            <p>For foreign investors, BKPM serves as your primary gateway to Indonesia's market. This government agency coordinates investment applications across multiple ministries and sectors, streamlining what would otherwise be a complex, multi-departmental process. Your investment approval from BKPM is not just a formality—it's the foundation that determines your legal business structure, operational scope, and access to investment incentives.</p>
                            
                            <p>The Indonesian government has made significant strides in attracting foreign direct investment through the Online Single Submission (OSS) system. However, navigating this digital platform requires deep understanding of Indonesia's investment regulations, the Negative Investment List (DNI), sectoral restrictions, and capital requirements. A single misclassification in your business activities (KBLI codes) can delay your approval by weeks or even lead to rejection.</p>
                            
                            <p><strong>Why this matters for your business:</strong> Your BKPM approval determines whether you can access tax holidays (up to 20 years for certain sectors), import duty exemptions, expedited permits, and preferential land rights. It also establishes your investment realization obligations—commitments you must fulfill within specified timeframes to maintain your legal status.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">Our experience shows that well-prepared investment applications with proper KBLI alignment, realistic capital commitments, and complete documentation receive approval 40% faster than those submitted without professional guidance. We ensure your application positions you for both immediate approval and long-term operational flexibility.</p>
                        </div>
                        @elseif($service['slug'] === 'company-establishment')
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">Incorporating a PT PMA (Perseroan Terbatas Penanaman Modal Asing) is the legal foundation for your foreign investment company in Indonesia.</p>
                            
                            <p>Unlike company registration in Western jurisdictions, Indonesian company establishment involves multiple critical steps that must be executed in precise sequence. The process begins with a notarized deed of establishment, which must comply with both Indonesian corporate law and your parent company's governance requirements. This deed becomes your company's constitution—defining shareholder rights, management authority, and operational parameters for years to come.</p>
                            
                            <p>The Ministry of Law and Human Rights must approve your company structure before you can proceed with any operational activities. This approval process examines your articles of association for compliance with Indonesia's Company Law (Law No. 40/2007), foreign ownership regulations, and sector-specific requirements. Any inconsistencies between your proposed structure and Indonesian legal requirements will result in rejection and costly amendments.</p>
                            
                            <p><strong>Critical considerations:</strong> Your initial capital structure affects not only your BKPM commitments but also your tax obligations, banking relationships, and future fundraising flexibility. Indonesia requires paid-up capital to be deposited and verified—you cannot simply declare authorized capital without actual funds. Additionally, director appointments must consider residency requirements, immigration status, and banking signature authority.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">We've seen companies delay operations by 2-3 months due to poorly structured articles of association that require post-establishment amendments. Our bilingual legal team ensures your company structure meets both Indonesian compliance standards and international corporate governance best practices from day one.</p>
                        </div>
                        @elseif($service['slug'] === 'tax-fiscal')
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">Indonesia's tax system for foreign investors operates on both territorial and global taxation principles, requiring strategic structuring from the outset.</p>
                            
                            <p>Your company NPWP (Tax Identification Number) is not merely a registration formality—it activates your obligations under Indonesian tax law and determines your eligibility for bilateral tax treaties. Indonesia has tax treaties with over 70 countries, potentially reducing withholding tax rates on dividends, interest, and royalties from 20% to as low as 10% or even 0% for certain transactions.</p>
                            
                            <p>PKP (Pengusaha Kena Pajak) registration for VAT is required once your annual revenue exceeds IDR 4.8 billion (approximately USD 300,000). However, many foreign investors voluntarily register earlier to reclaim input VAT on capital expenditures and maintain credibility with enterprise customers who require VAT invoices. The decision when to register affects your cash flow significantly—input VAT on imported equipment and construction can represent millions of dollars in temporarily locked capital.</p>
                            
                            <p><strong>Strategic tax planning opportunities:</strong> Indonesia offers substantial tax incentives for qualifying investments—tax holidays (10-20 years corporate income tax exemption), investment allowances (30% of investment value deductible over 6 years), and special economic zone benefits. However, these incentives require application timing, sectoral qualification, and minimum investment thresholds. Missing the application window can cost millions in lost tax benefits.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">Our tax specialists integrate your NPWP/PKP registration with broader tax optimization strategies. We've helped clients secure tax holidays worth USD 5-15 million over project lifecycles by properly structuring applications and documenting qualification criteria during the investment registration phase.</p>
                        </div>
                        @elseif($service['slug'] === 'immigration-services')
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">Indonesia's immigration framework balances foreign expertise needs with local employment protection, requiring careful navigation for expatriate deployment.</p>
                            
                            <p>The IMTA (Work Permit) system operates on a quota basis—your company receives an allocation of expatriate positions based on investment value and employment creation. Each work permit requires justification: why this position cannot be filled by Indonesian nationals, what skills transfer will occur, and how many local staff will receive training. The Ministry of Manpower scrutinizes these justifications closely, particularly for positions that appear to compete with available Indonesian talent.</p>
                            
                            <p>KITAS (Limited Stay Permit) processing has become more streamlined through online systems, but still requires precise documentation sequencing. Your work permit must be approved before applying for KITAS, and both must align with your employment contract terms. Any discrepancy—in job title, salary declaration, or work location—can trigger rejection or compliance audits that disrupt operations.</p>
                            
                            <p><strong>Compliance obligations extend beyond initial approval:</strong> Monthly MERP (expatriate reporting) submissions, annual DP5 notifications, and DKPTKA (expatriate compensation fund) payments of USD 1,200 per expatriate per year. Non-compliance results in denial of renewal applications and potential immigration blacklisting. Additionally, each expatriate must demonstrably train Indonesian understudies—failure to document this skills transfer jeopardizes future work permit allocations.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">We manage complete immigration lifecycles for clients—from initial RPTKA planning through annual renewals and family dependent processing. Our proactive calendar management has maintained 100% renewal success rates for clients over the past 3 years, avoiding the operational disruptions that occur when expatriates must leave Indonesia due to missed deadlines.</p>
                        </div>
                        @elseif($service['slug'] === 'environmental-compliance')
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">Environmental compliance in Indonesia has evolved from bureaucratic requirement to strategic business imperative, particularly for foreign investors facing ESG scrutiny from global stakeholders.</p>
                            
                            <p>The AMDAL (Environmental Impact Assessment) process is mandatory for 17 categories of large-scale or high-impact projects—from manufacturing facilities above certain capacity thresholds to infrastructure development in sensitive areas. This is not a desktop study; it requires comprehensive field surveys, laboratory analysis, public consultation, and expert assessment across environmental, social, and health dimensions. Projects cannot commence construction until environmental approval is secured, making AMDAL timeline management critical for investment schedules.</p>
                            
                            <p>For medium-impact projects, UKL-UPL (Environmental Management and Monitoring) provides a simplified pathway but still demands technical rigor. Your environmental documents must demonstrate not only compliance with Indonesian environmental law but also integration with international standards if your parent company reports under frameworks like GRI, TCFD, or SASB. Increasingly, Indonesian banks require environmental clearance before financing, and international buyers audit supplier environmental compliance.</p>
                            
                            <p><strong>Beyond permit acquisition:</strong> Your environmental approval establishes ongoing obligations—quarterly monitoring reports, annual performance evaluations, third-party audits every two years. The documents you submit become your operational commitments—promised pollution control equipment must be installed, committed water treatment capacity must be maintained, stated waste management procedures must be followed. Non-compliance can result in operational suspensions that cost hundreds of thousands of dollars per day in lost production.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">Our environmental team includes certified AMDAL practitioners across multiple disciplines—air quality engineers, water resource specialists, biodiversity experts, and social impact professionals. We've supported clients in aligning Indonesian environmental permits with ISO 14001 certification and parent company ESG reporting requirements, creating seamless compliance across multiple regulatory frameworks.</p>
                        </div>
                        @elseif($service['slug'] === 'land-building')
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">Land acquisition and building development in Indonesia involves navigating complex property rights structures that differ fundamentally from Western freehold systems.</p>
                            
                            <p>Foreign entities cannot own land under Hak Milik (freehold title)—your options are Hak Guna Bangunan (HGB, building rights for 30 years, renewable), Hak Pakai (right to use, typically 25 years), or long-term lease arrangements. Each structure affects your asset valuation, financing options, and exit strategy. HGB on state land provides stronger rights than HGB on private land, while HGB in industrial estates often comes with pre-established infrastructure and streamlined permitting.</p>
                            
                            <p>Building permit (PBG) processing requires coordination across multiple agencies—local spatial planning authority (for zoning compliance), environmental agency (for UKL-UPL clearance), fire department (for safety compliance), and often utilities providers (for connection capacity). Your architectural plans must comply with Indonesian building codes (SNI standards) while meeting your operational requirements. International firms often need to partner with locally-licensed Indonesian engineers to certify structural calculations and MEP designs.</p>
                            
                            <p><strong>The SLF (Certificate of Building Worthiness) is often overlooked but critical:</strong> Without SLF, your building cannot legally operate, insurance coverage may be void, and you cannot obtain operational licenses. SLF requires third-party inspection of completed construction—if your as-built differs from approved plans, you face costly rectification or acceptance of limited operational certificates.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">We've prevented millions in remediation costs by conducting pre-acquisition land due diligence that uncovered ownership disputes, zoning violations, and environmental contamination. Our integrated approach coordinates legal (land rights), technical (building engineering), and regulatory (permit processing) expertise to de-risk your property development from site selection through operational handover.</p>
                        </div>
                        @elseif($service['slug'] === 'operational-licenses')
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">Operational licensing in Indonesia operates across a fragmented regulatory landscape where sector-specific ministries maintain independent permit systems beyond the centralized OSS framework.</p>
                            
                            <p>Manufacturing operations require industrial operating permits (IUI) that specify production capacity, raw materials, and output products. Any deviation from approved specifications—changing suppliers, altering formulations, increasing capacity—requires permit amendments that can take months. For imported raw materials, your API (Import Identification Number) must align precisely with HS codes and your approved business activities; misalignment results in customs clearance delays and potential penalties.</p>
                            
                            <p>Product registration requirements vary dramatically by sector. Food and beverage products need BPOM (Food and Drug Authority) registration involving laboratory testing, factory audits, and labeling approvals—a process typically requiring 6-12 months. Electronics require SDPPI certification from the Ministry of Communication. Cosmetics need separate BPOM protocols. Pharmaceutical products face the longest timelines with multiple clinical testing phases. Each regulatory body operates independently with distinct requirements and timelines.</p>
                            
                            <p><strong>Strategic licensing timing affects market entry:</strong> You can often initiate product registration before completing facility construction, allowing approval timelines to run in parallel with capital deployment. However, some certifications require facility inspections, creating critical path dependencies. Import permits for trial samples need different procedures than commercial import licenses. Understanding these sequencing opportunities can accelerate your time-to-market by 3-6 months.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">Our sector specialists maintain current knowledge of evolving requirements across 15+ regulated industries. We've developed regulatory roadmaps for clients that synchronize capital deployment, permit applications, and market entry to minimize time-to-revenue while maintaining full compliance. This integrated planning has helped clients achieve operational status 4-6 months faster than industry averages.</p>
                        </div>
                        @elseif($service['slug'] === 'ongoing-compliance')
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">Maintaining compliance in Indonesia's dynamic regulatory environment requires continuous attention—regulations change, reporting requirements evolve, and permit renewal windows are unforgiving.</p>
                            
                            <p>Most foreign investors focus intensely on initial setup but underestimate ongoing obligations. Environmental permits require quarterly monitoring reports. Work permits need annual renewals starting 60 days before expiration. Tax returns have monthly, quarterly, and annual filing requirements with strict deadlines. BKPM investment realization reports (LKPM) are due quarterly, comparing actual progress against committed timelines and capital deployment. Missing any deadline can trigger compliance reviews, operational suspensions, or blacklisting from future permits.</p>
                            
                            <p>Regulatory changes occur frequently in Indonesia—new ministerial decrees, revised standards, updated online systems. A regulation that affects your operations might be published in Bahasa Indonesia on a government website with 30-day implementation timeline. Foreign companies often miss these changes until non-compliance creates operational problems. Additionally, beneficial regulatory changes like new tax incentives, simplified procedures, or expanded business scopes require proactive application—they don't automatically apply to existing permit holders.</p>
                            
                            <p><strong>The cost of compliance failure extends beyond penalties:</strong> If your work permits lapse, expatriates must leave Indonesia immediately—losing key personnel during critical project phases. If environmental reporting falls behind, you cannot renew operational licenses, forcing production shutdowns. If tax filings are late, your banking transactions can be frozen. The operational impact of compliance failures typically costs 10-100x more than the direct penalties.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">Our retainer clients receive automated calendar alerts 90, 60, and 30 days before any compliance deadline, complete with document preparation checklists. We monitor regulatory changes across 12 government agencies and proactively notify clients of relevant updates. This systematic approach has maintained zero compliance failures for our retainer portfolio over the past 24 months, providing operational continuity that allows management to focus on business growth rather than permit crises.</p>
                        </div>
                        @elseif($service['slug'] === 'intellectual-property')
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">Intellectual property protection in Indonesia requires proactive registration—unlike some jurisdictions, Indonesia follows a first-to-file system where registration establishes rights, not first use in commerce.</p>
                            
                            <p>Trademark protection is critical for foreign brands entering Indonesia. If you delay registration, local actors can file your brand name first, then demand payment to transfer the trademark or prevent your market entry entirely. This \"trademark squatting\" is a documented problem, particularly for well-known international brands that haven't registered in Indonesia. Once a conflicting trademark is registered, challenging it requires expensive legal proceedings that can take 2-3 years with uncertain outcomes.</p>
                            
                            <p>Patent protection in Indonesia covers inventions (20-year protection) and simple patents for incremental improvements (10-year protection). Indonesia is a member of the Patent Cooperation Treaty (PCT), allowing international applications to extend into Indonesia. However, PCT applications must be nationalized within 31 months—missing this deadline means losing patent rights in Indonesia even if you have protection elsewhere. For technology companies, patent protection is essential before market entry to prevent reverse engineering and unauthorized manufacturing.</p>
                            
                            <p><strong>Copyright registration, while not mandatory, provides critical enforcement advantages:</strong> Registered copyrights enable border enforcement—customs can seize counterfeit products using your copyright certificate. Without registration, you must prove ownership in each enforcement action, creating delays that allow counterfeiters to continue operations. For software companies, copyright registration combined with end-user license agreements provides legal framework for piracy prevention.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">We've helped clients prevent competitive trademark filings by conducting comprehensive conflict searches and securing registrations before market announcement. Our IP strategy integrates trademark class selection (Nice Classification), patent claim drafting, and copyright documentation to create multi-layered protection that withstands enforcement challenges. This proactive approach costs 5-10% of responding to IP conflicts after they occur.</p>
                        </div>
                        @else
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">{{ $service['long_description'] ?? $service['short_description'] }}</p>
                            
                            <p>Operating a business in Indonesia's regulatory environment requires deep understanding of both written regulations and practical implementation procedures. While Indonesia has made significant progress in streamlining business processes through digital platforms, navigating the system successfully still demands local expertise, established government relationships, and proactive compliance management.</p>
                            
                            <p><strong>Why professional guidance matters:</strong> Indonesian regulations often provide general frameworks, with detailed requirements emerging through ministerial decrees, technical guidelines, and evolving interpretations. What appears straightforward in legislation can involve complex requirements in practice. Professional consultants bridge this gap between regulatory text and operational reality.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">Our experience serving international clients across diverse industries has built comprehensive knowledge of Indonesia's regulatory landscape. We don't just process applications—we strategically position your business for long-term operational success, helping you avoid common pitfalls that delay or derail foreign investment projects.</p>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Deliverables -->
                    @if(isset($service['deliverables']) && count($service['deliverables']) > 0)
                    <div>
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                            <i class="fas fa-box-open mr-3" style="color: {{ $service['color'] }};"></i>
                            What You'll Receive
                        </h2>
                        <div class="grid sm:grid-cols-2 gap-3">
                            @foreach($service['deliverables'] as $deliverable)
                            <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-green-50 to-white rounded-lg border border-green-100 hover:shadow-md transition">
                                <i class="fas fa-check-double text-green-600 mt-1 flex-shrink-0"></i>
                                <span class="text-gray-700 text-sm leading-relaxed">{{ $deliverable }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Process Steps -->
                    @if(isset($service['process_steps']) && count($service['process_steps']) > 0)
                    <div>
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                            <i class="fas fa-route mr-3" style="color: {{ $service['color'] }};"></i>
                            Our Process
                        </h2>
                        <div class="space-y-4">
                            @foreach($service['process_steps'] as $index => $step)
                            <div class="flex gap-4 p-5 bg-gradient-to-r from-blue-50 to-white rounded-lg border border-blue-100 hover:shadow-md transition">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-blue-900 to-blue-700 text-white flex items-center justify-center font-bold text-lg shadow-lg">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 pt-2">
                                    <h3 class="font-semibold text-gray-900 mb-1">{{ $step }}</h3>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Long Description -->
                    @if(isset($service['description']))
                    <div>
                        <h2 class="text-2xl font-bold mb-4 text-gray-900">Service Overview</h2>
                        <div class="prose max-w-none text-gray-600 leading-relaxed">
                            {!! nl2br(e($service['description'])) !!}
                        </div>
                    </div>
                    @endif
                    
                    <!-- Features/Benefits -->
                    @if(isset($service['features']) && count($service['features']) > 0)
                    <div>
                        <h2 class="text-2xl font-bold mb-6 text-gray-900">What's Included</h2>
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach($service['features'] as $feature)
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Process/Steps -->
                    @if(isset($service['process']) && count($service['process']) > 0)
                    <div>
                        <h2 class="text-2xl font-bold mb-6 text-gray-900">Our Process</h2>
                        <div class="space-y-4">
                            @foreach($service['process'] as $index => $step)
                            <div class="flex gap-4 p-5 bg-gradient-to-r from-blue-50 to-white rounded-lg border border-blue-100">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-900 text-white flex items-center justify-center font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 mb-1">{{ $step['title'] ?? $step }}</h3>
                                    @if(isset($step['description']))
                                    <p class="text-sm text-gray-600">{{ $step['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Requirements -->
                    @if(isset($service['requirements']) && count($service['requirements']) > 0)
                    <div>
                        <h2 class="text-2xl font-bold mb-6 text-gray-900">Required Documents</h2>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                            <ul class="space-y-3">
                                @foreach($service['requirements'] as $requirement)
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-file-alt text-yellow-600 mt-1"></i>
                                    <span class="text-gray-700">{{ $requirement }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Contact Card -->
                    <div class="bg-gradient-to-br from-blue-900 to-blue-800 text-white rounded-xl p-6 shadow-lg">
                        <h3 class="text-xl font-bold mb-4">Need Assistance?</h3>
                        <p class="text-blue-100 text-sm mb-6">Our investment consultants are ready to help you navigate Indonesia's regulatory landscape.</p>
                        <div class="space-y-3">
                            <a href="https://wa.me/6283879602855" class="block w-full px-4 py-3 bg-green-600 text-white rounded-lg text-center font-semibold hover:bg-green-700 transition">
                                <i class="fab fa-whatsapp mr-2"></i>Chat on WhatsApp
                            </a>
                            <a href="mailto:cs@bizmark.id" class="block w-full px-4 py-3 bg-white text-blue-900 rounded-lg text-center font-semibold hover:bg-blue-50 transition">
                                <i class="fas fa-envelope mr-2"></i>Send Email
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Info -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h3 class="font-bold text-gray-900 mb-4">Service Information</h3>
                        <div class="space-y-3 text-sm">
                            @if(isset($service['pricing']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">Investment Range:</span>
                                <span class="font-semibold text-gray-900">{{ $service['pricing']['display'] }}</span>
                            </div>
                            @endif
                            @if(isset($service['duration']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">Timeline:</span>
                                <span class="font-semibold text-gray-900">{{ $service['duration'] }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Market:</span>
                                <span class="font-semibold text-gray-900">Foreign Investment (PMA)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Consultation:</span>
                                <span class="font-semibold text-green-600">Free</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Related Services -->
                    @if(isset($relatedServices) && count($relatedServices) > 0)
                    <div class="bg-white rounded-xl p-6 border border-gray-200">
                        <h3 class="font-bold text-gray-900 mb-4">Related Services</h3>
                        <div class="space-y-3">
                            @foreach(array_slice($relatedServices, 0, 3, true) as $slug => $related)
                            <a href="{{ route('services.show.en', $slug) }}" class="block p-3 rounded-lg hover:bg-gray-50 transition border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <i class="fas {{ $related['icon'] }} text-lg" style="color: {{ $related['color'] }};"></i>
                                    <span class="text-sm font-medium text-gray-700 hover:text-blue-900">{{ $related['title'] }}</span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-br from-blue-900 to-blue-800 text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Start Your Investment Journey?</h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Get a free consultation and customized roadmap for your business in Indonesia
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="https://wa.me/6283879602855?text=Hello,%20I%20need%20consultation%20for%20{{ urlencode($service['title']) }}" 
               class="px-8 py-4 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition">
                <i class="fab fa-whatsapp mr-2"></i>Free Consultation
            </a>
            <a href="{{ route('services.index.en') }}" 
               class="px-8 py-4 bg-white text-blue-900 rounded-lg font-bold hover:bg-blue-50 transition">
                <i class="fas fa-th mr-2"></i>View All Services
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-300 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="mb-4">
            <span class="text-xl font-bold text-white">
                <i class="fas fa-certificate text-blue-400 mr-2"></i>Bizmark.ID
            </span>
        </div>
        <p class="text-sm">&copy; {{ date('Y') }} Bizmark.ID - Your Trusted Investment Partner in Indonesia</p>
        <div class="mt-4 flex justify-center gap-6 text-sm">
            <a href="{{ route('landing.en') }}" class="hover:text-white transition">Home</a>
            <a href="{{ route('services.index.en') }}" class="hover:text-white transition">Services</a>
            <a href="mailto:cs@bizmark.id" class="hover:text-white transition">Contact</a>
        </div>
    </div>
</footer>

</body>
</html>

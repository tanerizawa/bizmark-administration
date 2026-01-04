<?php

/**
 * Investment-specific translations for PMA market
 */

return [
    // FAQ specific to PMA/Foreign Investment
    'faq' => [
        'timeline' => [
            'question' => 'What is the timeline for PMA company establishment?',
            'answer' => 'A basic PT PMA (foreign investment company) can be established in 30-45 days, covering company registration, tax ID, and business license (NIB). Additional permits such as environmental approval or sector-specific licenses may require 60-180 days depending on the complexity and government processing times.',
        ],
        'capital' => [
            'question' => 'What are the minimum capital requirements for foreign investment?',
            'answer' => 'For most business sectors, the minimum paid-up capital for PT PMA is IDR 10 billion (approximately USD 650,000). However, this varies significantly by sector and business activity. Some sectors have lower thresholds, while strategic or restricted sectors may require higher capital. We provide detailed guidance based on your specific business plan.',
        ],
        'ownership' => [
            'question' => 'Can foreign investors have 100% ownership in Indonesia?',
            'answer' => 'It depends on your business sector. Indonesia maintains a Negative Investment List (DNI/DPMPTSP) that restricts or limits foreign ownership in certain sectors. Many sectors allow 100% foreign ownership, while others require Indonesian partnership (typically 51% foreign, 49% local). Restricted sectors include certain retail activities, small-scale industries, and public services. We help you navigate these regulations and structure your investment appropriately.',
        ],
        'work_permits' => [
            'question' => 'How do you handle visas and work permits for our expatriate staff?',
            'answer' => 'We manage the complete immigration process including: IMTA (work permit) application to the Ministry of Manpower, visa recommendation letter (VTT), visa processing, KITAS (limited stay permit) for 1-2 years, MERP (expatriate reporting) registration, and DP5 notifications. Processing typically takes 14-21 working days. We also handle family dependent visas and provide ongoing renewal management.',
        ],
        'taxes' => [
            'question' => 'What are the tax implications for foreign investors in Indonesia?',
            'answer' => 'Indonesia has a corporate income tax rate of 22% (with potential reduction based on certain criteria). Indonesia has tax treaties with over 70 countries to prevent double taxation. We help structure your investment for tax efficiency, apply for available tax incentives (tax holidays, investment allowances), and ensure compliance with transfer pricing regulations. Withholding taxes apply to dividends (15-20%), interest (15-20%), and royalties (15-20%).',
        ],
        'compliance' => [
            'question' => 'Do you provide ongoing compliance and permit renewal services?',
            'answer' => 'Yes, we offer comprehensive annual retainer packages starting from USD 500/month. This includes: automated permit renewal management (work permits, business licenses), annual statutory reporting (BKPM investment realization, tax filing support), quarterly compliance health checks, monthly regulatory change monitoring and alerts, and priority access to our advisory team for consultation.',
        ],
        'restrictions' => [
            'question' => 'Which business sectors have foreign investment restrictions?',
            'answer' => 'The Negative Investment List (PP 10/2021) identifies restricted and conditional sectors. Fully closed to foreign investment: small-scale retail, traditional markets, certain fisheries, and some cultural activities. Limited foreign ownership: broadcasting media (20%), telecommunications (49-67%), construction services (67%), and certain healthcare services (67%). We maintain updated sector guidelines and can advise on alternative business structures or partnership arrangements.',
        ],
        'repatriation' => [
            'question' => 'How can we repatriate profits and dividends to our parent company?',
            'answer' => 'Profit repatriation is freely permitted after fulfilling corporate tax obligations (22% income tax plus 15-20% dividend withholding tax). Dividends can be remitted abroad through authorized foreign exchange banks. Required documentation includes: tax clearance certificate, board resolution approving dividend distribution, audited financial statements, and proof of tax payment. We assist with complete documentation and ensure compliance with Bank Indonesia foreign exchange regulations.',
        ],
        'incentives' => [
            'question' => 'What investment incentives are available for foreign investors?',
            'answer' => 'Indonesia offers various incentives including: Tax Holiday (5-20 years corporate tax exemption for pioneer industries with min. IDR 500 billion investment), Tax Allowance (30% deduction on fixed assets, accelerated depreciation), Import duty exemptions for capital goods and raw materials, and Special Economic Zone benefits (lower tax rates, streamlined licensing). Eligibility depends on sector, investment value, and location. We conduct comprehensive incentive assessments for your project.',
        ],
    ],

    // Process Steps for Investment
    'process' => [
        'discovery' => [
            'title' => 'Discovery & Assessment',
            'description' => 'We analyze your investment plan, assess regulatory requirements, evaluate sector restrictions, and provide feasibility recommendations.',
            'deliverables' => [
                'Investment feasibility assessment',
                'Regulatory requirement mapping',
                'Timeline and budget projection',
                'Risk identification and mitigation',
            ],
        ],
        'roadmap' => [
            'title' => 'Compliance Roadmap',
            'description' => 'Development of comprehensive permit roadmap showing dependencies, priorities, timeline milestones, and cost breakdown.',
            'deliverables' => [
                'Detailed permit dependency chart',
                'Phased timeline with critical path',
                'Comprehensive budget breakdown',
                'Resource allocation plan',
            ],
        ],
        'preparation' => [
            'title' => 'Document Preparation',
            'description' => 'Professional preparation of all required documents including notarization, legalization, and certified translation services.',
            'deliverables' => [
                'Notarized company documents',
                'Legalized foreign documents',
                'Certified Indonesian translations',
                'Completed application forms',
            ],
        ],
        'liaison' => [
            'title' => 'Government Liaison',
            'description' => 'Direct coordination with BKPM, ministries, and local authorities. We handle all submissions, follow-ups, and technical clarifications.',
            'deliverables' => [
                'BKPM investment approval',
                'Ministry approvals and permits',
                'Local government clearances',
                'Inter-agency coordination',
            ],
        ],
        'monitoring' => [
            'title' => 'Progress Monitoring',
            'description' => 'Weekly progress reports, proactive issue resolution, stakeholder updates, and transparent timeline tracking.',
            'deliverables' => [
                'Weekly status reports (English)',
                'Real-time progress dashboard',
                'Issue escalation and resolution',
                'Stakeholder communication',
            ],
        ],
        'support' => [
            'title' => 'Post-Approval Support',
            'description' => 'Complete handover documentation, compliance calendar setup, renewal management, and ongoing advisory access.',
            'deliverables' => [
                'Organized permit documentation',
                'Compliance calendar and reminders',
                'Operational guidelines',
                'Ongoing support access',
            ],
        ],
    ],

    // Investment Packages
    'packages' => [
        'starter' => [
            'name' => 'Starter Package',
            'tagline' => 'Perfect for small foreign-owned businesses',
            'price' => 'USD 3,500',
            'duration' => '30 days',
            'ideal_for' => 'Representative offices, consulting firms, small trading companies',
            'description' => 'Essential company setup package for foreign investors starting their Indonesian operations. Covers basic legal and tax requirements.',
            'features' => [
                'Company establishment (PT PMA)',
                'Ministry of Law approval',
                'Tax registration (NPWP)',
                'Business license (NIB)',
                '1 work permit (KITAS) processing',
                'Bank account opening support',
                'Free initial consultation (1 hour)',
            ],
            'not_included' => [
                'Environmental permits',
                'Sector-specific licenses',
                'Office lease negotiation',
            ],
        ],
        'business' => [
            'name' => 'Business Package',
            'tagline' => 'Comprehensive setup for medium operations',
            'price' => 'USD 8,500',
            'duration' => '60 days',
            'ideal_for' => 'Manufacturing, trading, distribution, or service companies',
            'description' => 'Complete business establishment with environmental and operational permits. Most popular choice for manufacturing and trading operations.',
            'popular' => true,
            'features' => [
                'Everything in Starter Package',
                'BKPM investment principle approval',
                'Environmental permit (UKL-UPL)',
                'Import license (API) if applicable',
                '3 work permits (KITAS) processing',
                'Office lease negotiation assistance',
                'Customs clearance consultation',
                'Free compliance review (quarterly for 3 months)',
            ],
            'not_included' => [
                'AMDAL (high-impact projects)',
                'Land acquisition services',
                'Construction permits',
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise Package',
            'tagline' => 'Full-service solution for large investments',
            'price' => 'From USD 15,000',
            'duration' => '90+ days',
            'ideal_for' => 'Large-scale manufacturing, infrastructure, mining, or property development',
            'description' => 'Premium package for major investments requiring comprehensive permits, ongoing compliance, and dedicated support.',
            'features' => [
                'Everything in Business Package',
                'Full compliance and regulatory setup',
                'Land acquisition advisory and support',
                'AMDAL processing (if required)',
                '5+ work permits (KITAS) processing',
                'Building permit (PBG) coordination',
                'Ongoing compliance support (3 months)',
                'Dedicated account manager',
                'Priority processing and support',
                'Monthly regulatory briefings',
            ],
            'not_included' => [
                'Actual land purchase costs',
                'Construction management',
                'Long-term retainer (available separately)',
            ],
        ],
        'custom' => [
            'name' => 'Custom Package',
            'tagline' => 'Tailored to your specific needs',
            'price' => 'Quote-based',
            'duration' => 'Varies',
            'ideal_for' => 'Unique projects, complex structures, or specific industry requirements',
            'description' => 'Flexible package customized to your investment requirements, sector, and timeline. Ideal for complex or multi-phase projects.',
            'features' => [
                'Customized scope of work',
                'Flexible service selection',
                'Phased implementation options',
                'Dedicated project team',
                'Custom reporting and communication',
            ],
            'cta' => 'Contact us for a customized proposal',
        ],
    ],

    // Sector Information
    'sectors' => [
        'title' => 'We Serve All Major Sectors',
        'manufacturing' => [
            'name' => 'Manufacturing',
            'description' => 'Industrial production, processing, assembly',
            'examples' => 'Automotive, electronics, food processing, chemicals',
        ],
        'trading' => [
            'name' => 'Trading & Distribution',
            'description' => 'Import, export, wholesale, distribution',
            'examples' => 'Consumer goods, industrial supplies, commodities',
        ],
        'services' => [
            'name' => 'Services',
            'description' => 'Professional and business services',
            'examples' => 'Consulting, IT, logistics, education',
        ],
        'property' => [
            'name' => 'Property & Construction',
            'description' => 'Real estate development and construction',
            'examples' => 'Commercial buildings, industrial estates, residential',
        ],
        'energy' => [
            'name' => 'Energy & Resources',
            'description' => 'Renewable energy, mining, oil & gas',
            'examples' => 'Solar, geothermal, mining, petroleum services',
        ],
        'hospitality' => [
            'name' => 'Hospitality & Tourism',
            'description' => 'Hotels, restaurants, travel services',
            'examples' => 'Hotels, resorts, restaurants, tour operators',
        ],
    ],

    // Benefits
    'benefits' => [
        'title' => 'Benefits of Working with Us',
        'expert_knowledge' => [
            'title' => 'Expert Local Knowledge',
            'description' => 'Deep understanding of Indonesian regulations, government processes, and business culture.',
        ],
        'international_standards' => [
            'title' => 'International Standards',
            'description' => 'ISO 9001:2015 certified processes ensuring quality and reliability.',
        ],
        'english_support' => [
            'title' => 'Full English Support',
            'description' => 'All services, documentation, and communication available in English.',
        ],
        'proven_track' => [
            'title' => 'Proven Track Record',
            'description' => '98% success rate with permits approved on first submission.',
        ],
        'transparent' => [
            'title' => 'Transparent Pricing',
            'description' => 'Clear, upfront pricing with no hidden fees. You know exactly what you\'re paying for.',
        ],
        'dedicated_support' => [
            'title' => 'Dedicated Support',
            'description' => 'Personal account manager and direct access to our expert team.',
        ],
    ],
];

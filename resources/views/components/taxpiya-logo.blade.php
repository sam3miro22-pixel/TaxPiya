@props(['size' => 88])
<div class="txp-brand-mark" style="width:{{ $size }}px;height:{{ $size }}px" aria-hidden="true">
    <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="txpLogoGrad" x1="0" y1="0" x2="120" y2="120">
                <stop offset="0%" stop-color="#ffd166"/>
                <stop offset="100%" stop-color="#ff8c00"/>
            </linearGradient>
            <linearGradient id="txpLogoGradC" x1="0" y1="0" x2="120" y2="120">
                <stop offset="0%" stop-color="#5eead4"/>
                <stop offset="100%" stop-color="#14b8a6"/>
            </linearGradient>
        </defs>
        <rect width="120" height="120" rx="28" fill="url(#{{ ($conductor ?? false) ? 'txpLogoGradC' : 'txpLogoGrad' }})"/>
        <rect x="8" y="8" width="104" height="104" rx="24" fill="rgba(255,255,255,0.12)"/>
        <path d="M28 72h64l-6 14H34l-6-14z" fill="#1a1208" opacity=".85"/>
        <rect x="30" y="58" width="60" height="16" rx="6" fill="#fff"/>
        <rect x="36" y="62" width="18" height="8" rx="2" fill="#94a3b8"/>
        <rect x="66" y="62" width="18" height="8" rx="2" fill="#94a3b8"/>
        <path d="M38 58V46c0-6 5-10 11-10h22c6 0 11 4 11 10v12" stroke="#fff" stroke-width="5" stroke-linecap="round"/>
        <circle cx="42" cy="74" r="7" fill="#1a1208"/>
        <circle cx="78" cy="74" r="7" fill="#1a1208"/>
        <circle cx="42" cy="74" r="3" fill="#ffd166"/>
        <circle cx="78" cy="74" r="3" fill="#ffd166"/>
    </svg>
</div>

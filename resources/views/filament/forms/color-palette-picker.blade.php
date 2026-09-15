@php
    $selected = $selected ?? 'custom';
    $palettes = $palettes ?? [];
@endphp

<style>
    .share3a-palette-picker {
        --pp-radius: 14px;
        --pp-border: rgb(0 0 0 / 0.08);
        --pp-text: #0f172a;
        --pp-muted: #64748b;
        --pp-surface: #ffffff;
        --pp-active: #059669;
        font-family: inherit;
    }

    .dark .share3a-palette-picker {
        --pp-border: rgb(255 255 255 / 0.12);
        --pp-text: #f8fafc;
        --pp-muted: #94a3b8;
        --pp-surface: rgb(255 255 255 / 0.04);
        --pp-active: #34d399;
    }

    .share3a-palette-picker__header {
        margin-bottom: 1rem;
    }

    .share3a-palette-picker__title {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--pp-text);
    }

    .share3a-palette-picker__desc {
        margin: 0.35rem 0 0;
        font-size: 0.78rem;
        line-height: 1.5;
        color: var(--pp-muted);
    }

    .share3a-palette-picker__grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 0.85rem;
    }

    @media (min-width: 640px) {
        .share3a-palette-picker__grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .share3a-palette-picker__grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .share3a-palette-card {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        width: 100%;
        padding: 0.85rem;
        border: 2px solid var(--pp-border);
        border-radius: var(--pp-radius);
        background: var(--pp-surface);
        text-align: start;
        cursor: pointer;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .share3a-palette-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px -12px rgb(0 0 0 / 0.28);
        border-color: rgb(5 150 105 / 0.35);
    }

    .share3a-palette-card.is-active {
        border-color: var(--pp-active);
        box-shadow: 0 0 0 3px rgb(5 150 105 / 0.15);
    }

    .share3a-palette-card__top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .share3a-palette-card__name {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--pp-text);
    }

    .share3a-palette-card__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.18rem 0.55rem;
        border-radius: 999px;
        background: var(--pp-active);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .share3a-palette-card__mockup {
        display: grid;
        grid-template-columns: 28% 1fr;
        height: 72px;
        overflow: hidden;
        border-radius: 10px;
        border: 1px solid rgb(0 0 0 / 0.08);
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.25);
    }

    .dark .share3a-palette-card__mockup {
        border-color: rgb(255 255 255 / 0.08);
    }

    .share3a-palette-card__sidebar {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 8px 6px;
    }

    .share3a-palette-card__sidebar-line {
        height: 4px;
        border-radius: 999px;
        background: rgb(255 255 255 / 0.22);
    }

    .share3a-palette-card__sidebar-line.is-accent {
        background: rgb(255 255 255 / 0.85);
        width: 70%;
    }

    .share3a-palette-card__main {
        display: flex;
        flex-direction: column;
        padding: 8px;
        gap: 6px;
    }

    .share3a-palette-card__hero {
        height: 18px;
        border-radius: 6px;
    }

    .share3a-palette-card__content-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        flex: 1;
    }

    .share3a-palette-card__block {
        border-radius: 5px;
        border: 1px solid rgb(0 0 0 / 0.05);
    }

    .share3a-palette-card__strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.35rem;
    }

    .share3a-palette-card__swatch {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.3rem;
        min-width: 0;
    }

    .share3a-palette-card__color {
        width: 100%;
        height: 28px;
        border-radius: 8px;
        border: 1px solid rgb(0 0 0 / 0.1);
        box-shadow: 0 1px 2px rgb(0 0 0 / 0.08);
    }

    .dark .share3a-palette-card__color {
        border-color: rgb(255 255 255 / 0.12);
    }

    .share3a-palette-card__label {
        font-size: 0.62rem;
        font-weight: 600;
        color: var(--pp-muted);
        white-space: nowrap;
    }

    .share3a-palette-card__hex {
        font-size: 0.58rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        color: var(--pp-muted);
        direction: ltr;
        unicode-bidi: plaintext;
    }

    .share3a-palette-card__check {
        position: absolute;
        top: 0.55rem;
        inset-inline-start: 0.55rem;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: var(--pp-active);
        color: #fff;
        box-shadow: 0 2px 8px rgb(0 0 0 / 0.18);
    }

    .share3a-palette-card.is-active .share3a-palette-card__top {
        padding-inline-start: 1.6rem;
    }
</style>

<div class="share3a-palette-picker">
    <div class="share3a-palette-picker__header">
        <h3 class="share3a-palette-picker__title">{{ $heading ?? 'اختر لوحة ألوان جاهزة' }}</h3>
        <p class="share3a-palette-picker__desc">
            {{ $description ?? 'انقر على أي لوحة لمعاينة ألوانها وتطبيقها على كل عناصر لوحة التحكم.' }}
        </p>
    </div>

    <div class="share3a-palette-picker__grid">
        @foreach ($palettes as $palette)
            @php
                $isActive = $selected === $palette['id'];
                $primary = $palette['primary'];
                $sidebar = $palette['sidebar'];
                $accent = $palette['accent'];
                $background = $palette['background'];
            @endphp

            <button
                type="button"
                wire:click="{{ $applyMethod }}('{{ $palette['id'] }}')"
                wire:key="palette-{{ $palette['id'] }}"
                class="share3a-palette-card {{ $isActive ? 'is-active' : '' }}"
                aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                title="{{ $palette['label'] }}"
            >
                @if ($isActive)
                    <span class="share3a-palette-card__check" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.5 6L5 8.5L9.5 3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                @endif

                <div class="share3a-palette-card__top">
                    <span class="share3a-palette-card__name">{{ $palette['label'] }}</span>
                    @if ($isActive)
                        <span class="share3a-palette-card__badge">✓ محدّد</span>
                    @endif
                </div>

                {{-- Mini dashboard preview --}}
                <div class="share3a-palette-card__mockup" style="background-color: {{ $background }}">
                    <div class="share3a-palette-card__sidebar" style="background-color: {{ $sidebar }}">
                        <span class="share3a-palette-card__sidebar-line is-accent" style="background-color: {{ $accent }}"></span>
                        <span class="share3a-palette-card__sidebar-line"></span>
                        <span class="share3a-palette-card__sidebar-line"></span>
                        <span class="share3a-palette-card__sidebar-line"></span>
                    </div>
                    <div class="share3a-palette-card__main" style="background-color: {{ $background }}">
                        <div class="share3a-palette-card__hero" style="background-color: {{ $primary }}"></div>
                        <div class="share3a-palette-card__content-row">
                            <div class="share3a-palette-card__block" style="background-color: {{ $accent }}; opacity: 0.28"></div>
                            <div class="share3a-palette-card__block" style="background-color: {{ $primary }}; opacity: 0.18"></div>
                        </div>
                    </div>
                </div>

                {{-- Color swatches with hex codes --}}
                <div class="share3a-palette-card__strip">
                    @foreach ([
                        ['color' => $primary, 'label' => 'أساسي'],
                        ['color' => $sidebar, 'label' => 'جانبي'],
                        ['color' => $accent, 'label' => 'تمييز'],
                        ['color' => $background, 'label' => 'خلفية'],
                    ] as $swatch)
                        <div class="share3a-palette-card__swatch">
                            <div
                                class="share3a-palette-card__color"
                                style="background-color: {{ $swatch['color'] }}"
                            ></div>
                            <span class="share3a-palette-card__label">{{ $swatch['label'] }}</span>
                            <span class="share3a-palette-card__hex">{{ strtoupper($swatch['color']) }}</span>
                        </div>
                    @endforeach
                </div>
            </button>
        @endforeach
    </div>
</div>

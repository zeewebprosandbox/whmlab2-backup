@extends($activeTemplate . 'layouts.frontend')

@section('content')
<div class="zod-article-page py-5 bg-slate-50">
    <div class="container" style="max-width: 960px;">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0 m-0 text-xs">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-slate-500 text-decoration-none hover:text-indigo-600">@lang('Home')</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blogs') }}" class="text-slate-500 text-decoration-none hover:text-indigo-600">@lang('Knowledgebase')</a></li>
                @if(@$blog->data_values->category)
                    <li class="breadcrumb-item"><a href="{{ route('blogs', ['category' => @$blog->data_values->category]) }}" class="text-slate-500 text-decoration-none hover:text-indigo-600">{{ @$blog->data_values->category }}</a></li>
                @endif
                <li class="breadcrumb-item active text-slate-800" aria-current="page">{{ Str::limit(@$blog->data_values->title, 40) }}</li>
            </ol>
        </nav>

        <article class="bg-white rounded-2xl border border-slate-200 p-4 p-md-5 shadow-sm">
            <!-- Article Header -->
            <header class="zod-article-header pb-4 mb-4 border-bottom border-slate-100">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="px-2.5 py-1 rounded bg-indigo-50 text-indigo-700 text-xs font-semibold">
                        {{ @$blog->data_values->category ?? 'Infrastructure' }}
                    </span>
                    <span class="text-xs text-slate-400 d-inline-flex align-items-center gap-1">
                        <i data-lucide="clock" style="width: 13px; height: 13px;"></i>
                        {{ @$blog->data_values->reading_time ?? '6 min read' }}
                    </span>
                    <span class="text-xs text-slate-400 d-inline-flex align-items-center gap-1">
                        <i data-lucide="calendar" style="width: 13px; height: 13px;"></i>
                        {{ showDateTime(@$blog->created_at, 'M d, Y') }}
                    </span>
                </div>

                <h1 class="zod-article-title text-2xl md:text-4xl font-bold text-slate-900 leading-tight mb-3">
                    {{ __(@$blog->data_values->title) }}
                </h1>

                <div class="d-flex align-items-center gap-2 pt-2 text-xs text-slate-500">
                    <div class="rounded-full bg-slate-900 text-white d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-weight: bold;">
                        Z
                    </div>
                    <span>@lang('Published by') <strong>{{ gs('site_name') }} @lang('Engineering Team')</strong></span>
                </div>
            </header>

            <!-- AI Key Takeaways Box -->
            <div class="zod-takeaways-box p-3.5 mb-4 rounded-xl bg-slate-50 border border-slate-200">
                <div class="d-flex align-items-center gap-2 mb-2 text-slate-900 font-semibold text-xs text-uppercase tracking-wider">
                    <i data-lucide="sparkles" class="text-amber-500" style="width: 16px; height: 16px;"></i>
                    <span>@lang('Executive Summary & Key Takeaways')</span>
                </div>
                <p class="text-xs text-slate-600 m-0 leading-relaxed">
                    {{ @$seoContents->description ?? Str::limit(strip_tags(@$blog->data_values->description), 200) }}
                </p>
            </div>

            <!-- Main Article Body Content -->
            <div class="zod-article-body text-slate-700 text-sm leading-relaxed">
                @php echo @$blog->data_values->description; @endphp
            </div>

            <!-- Tags -->
            @if(@$blog->data_values->tags)
                <div class="zod-article-tags pt-4 mt-4 border-top border-slate-100 d-flex flex-wrap align-items-center gap-2">
                    <span class="text-xs text-slate-400 font-medium"><i data-lucide="tag" style="width: 13px; height: 13px;"></i> @lang('Tags'):</span>
                    @foreach(explode(',', @$blog->data_values->tags) as $tag)
                        <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-600">
                            #{{ trim($tag) }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Bottom Action / CTA Banner -->
            <div class="zod-article-cta p-4 mt-5 rounded-xl bg-slate-900 text-white d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="text-base font-semibold text-white m-0">@lang('Ready to deploy high-performance hosting?')</h4>
                    <p class="text-xs text-slate-400 m-0 mt-1">@lang('Get ultra-fast PCIe Gen4 NVMe storage, 99.9% uptime SLA, and free 1-click SSL.')</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('service.category') }}" class="btn btn-light btn-sm text-xs font-semibold px-3 py-2">
                        @lang('Explore Hosting Plans')
                    </a>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <a href="{{ route('blogs') }}" class="btn btn-outline-secondary btn-sm text-xs d-inline-flex align-items-center gap-1.5">
                    <i data-lucide="arrow-left" style="width: 13px; height: 13px;"></i>
                    @lang('Back to All Guides')
                </a>
            </div>
        </article>

        <!-- Related Articles Section -->
        @if(isset($relatedBlogs) && $relatedBlogs->count())
            <div class="zod-related-section mt-5">
                <h3 class="text-lg font-bold text-slate-900 mb-3">@lang('Related Technical Guides')</h3>
                <div class="row g-3">
                    @foreach($relatedBlogs as $rel)
                        <div class="col-md-4">
                            <div class="bg-white p-3 rounded-xl border border-slate-200 h-100 d-flex flex-column">
                                <span class="text-xs font-medium text-indigo-600 mb-1">{{ @$rel->data_values->category ?? 'Guide' }}</span>
                                <h4 class="text-xs font-semibold text-slate-900 mb-2 line-clamp-2">
                                    <a href="{{ route('blog.details', [slug(@$rel->data_values->title)]) }}" class="text-slate-900 text-decoration-none hover:text-indigo-600">
                                        {{ __(@$rel->data_values->title) }}
                                    </a>
                                </h4>
                                <a href="{{ route('blog.details', [slug(@$rel->data_values->title)]) }}" class="text-xs text-indigo-600 font-semibold text-decoration-none mt-auto d-inline-flex align-items-center gap-1">
                                    @lang('Read Guide') <i data-lucide="chevron-right" style="width: 12px; height: 12px;"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.zod-article-body h2 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #0f172a;
    margin-top: 1.8rem;
    margin-bottom: 0.8rem;
}
.zod-article-body h3 {
    font-size: 1.15rem;
    font-weight: 600;
    color: #1e293b;
    margin-top: 1.4rem;
    margin-bottom: 0.6rem;
}
.zod-article-body p {
    margin-bottom: 1.1rem;
    color: #334155;
    line-height: 1.7;
}
.zod-article-body ul, .zod-article-body ol {
    margin-bottom: 1.2rem;
    padding-left: 1.4rem;
}
.zod-article-body li {
    margin-bottom: 0.4rem;
    color: #334155;
}
.zod-article-body pre {
    background: #0f172a;
    color: #f8fafc;
    padding: 1rem 1.2rem;
    border-radius: 8px;
    overflow-x: auto;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.82rem;
    margin-bottom: 1.3rem;
    border: 1px solid #1e293b;
}
.zod-article-body code {
    color: #4f46e5;
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.85em;
}
.zod-article-body pre code {
    color: #f8fafc;
    background: transparent;
    padding: 0;
}
.zod-article-body table {
    width: 100%;
    margin-bottom: 1.3rem;
    border-collapse: collapse;
    font-size: 0.85rem;
}
.zod-article-body th, .zod-article-body td {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
}
.zod-article-body th {
    background: #f8fafc;
    font-weight: 600;
}
</style>
@endsection

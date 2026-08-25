@php
    if (request()->routeIs('home')) {
        $blogs = App\Models\Frontend::where('data_keys', 'blog.element')->latest()->take(3)->get();
    }
@endphp

<section class="zod-kb-section py-5">
    <div class="container">
        @if(!request()->routeIs('home'))
            <!-- KB Header & Filter Bar -->
            <div class="zod-kb-header mb-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-6">
                        <div class="zod-kb-kicker d-inline-flex align-items-center gap-2 px-2.5 py-1 mb-2 rounded bg-indigo-50 text-indigo-700 text-xs font-semibold">
                            <i data-lucide="book-open" style="width: 14px; height: 14px;"></i>
                            <span>@lang('Knowledgebase & Engineering Guides')</span>
                        </div>
                        <h1 class="zod-kb-title text-2xl md:text-3xl font-bold text-slate-900 m-0">@lang('Technical Tutorials & Infrastructure Guides')</h1>
                        <p class="text-slate-500 text-sm mt-1 mb-0">@lang('Master cloud hosting, NVMe performance tuning, DNS architecture, security, and developer deployment workflows.')</p>
                    </div>
                    <div class="col-lg-6">
                        <form action="{{ route('blogs') }}" method="GET" class="zod-kb-search-form d-flex gap-2">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-slate-400">
                                    <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0 text-sm" placeholder="@lang('Search topics, NVMe, Redis, Docker, DNS, SSL...')" value="{{ request('search') }}">
                            </div>
                            <button type="submit" class="btn btn-primary px-3 text-xs font-medium" style="height: 38px;">
                                @lang('Search')
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Category Filter Pills -->
                @if(isset($categories) && count($categories))
                    <div class="zod-kb-categories d-flex flex-wrap gap-2 mt-4 pt-2 border-top">
                        <a href="{{ route('blogs') }}" class="zod-cat-pill {{ !request('category') ? 'active' : '' }}">
                            @lang('All Topics')
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('blogs', ['category' => $cat, 'search' => request('search')]) }}" class="zod-cat-pill {{ request('category') == $cat ? 'active' : '' }}">
                                {{ __($cat) }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="zod-section-head text-center mb-5">
                <span class="zod-kicker">@lang('Engineering Blog & Guides')</span>
                <h2>@lang('Latest Hosting & Infrastructure Knowledge')</h2>
                <p>@lang('In-depth technical guides written by engineers to help you scale fast.')</p>
            </div>
        @endif

        <!-- Blog Cards Grid -->
        <div class="row g-4">
            @forelse($blogs as $blog)
                <div class="col-lg-4 col-md-6">
                    <article class="zod-kb-card h-100 d-flex flex-column bg-white rounded-xl border border-slate-200 p-4 transition-all hover:shadow-md">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <span class="zod-kb-tag px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ @$blog->data_values->category ?? 'Web Hosting' }}
                            </span>
                            <span class="text-xs text-slate-400 d-flex align-items-center gap-1">
                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                {{ @$blog->data_values->reading_time ?? '5 min read' }}
                            </span>
                        </div>

                        <h3 class="zod-kb-card-title text-base font-semibold text-slate-900 mb-2 line-clamp-2">
                            <a href="{{ route('blog.details', [slug(@$blog->data_values->title)]) }}" class="text-slate-900 hover:text-indigo-600 text-decoration-none">
                                {{ __(@$blog->data_values->title) }}
                            </a>
                        </h3>

                        <p class="zod-kb-card-desc text-xs text-slate-500 line-clamp-3 mb-4 flex-grow-1">
                            {{ Str::limit(strip_tags(@$blog->data_values->description), 130) }}
                        </p>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top border-slate-100 mt-auto">
                            <span class="text-xs text-slate-400">
                                {{ showDateTime(@$blog->created_at, 'M d, Y') }}
                            </span>
                            <a href="{{ route('blog.details', [slug(@$blog->data_values->title)]) }}" class="zod-read-more text-xs font-semibold text-indigo-600 hover:text-indigo-800 d-inline-flex align-items-center gap-1 text-decoration-none">
                                <span>@lang('Read Guide')</span>
                                <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                            </a>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-slate-50 rounded-xl border border-slate-200">
                        <i data-lucide="search-x" class="text-slate-400 mx-auto mb-3" style="width: 40px; height: 40px;"></i>
                        <h4 class="text-slate-800 text-base font-semibold">@lang('No matching articles found')</h4>
                        <p class="text-slate-500 text-xs mt-1 mb-3">@lang('Try searching with different keywords or browse all categories.')</p>
                        <a href="{{ route('blogs') }}" class="btn btn-outline-secondary btn-sm text-xs">
                            @lang('Reset Search')
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        @if (!request()->routeIs('home') && method_exists($blogs, 'links'))
            <div class="d-flex justify-content-center mt-5">
                {{ $blogs->links() }}
            </div>
        @endif
    </div>
</section>

<style>
.zod-kb-card {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
}
.zod-kb-card:hover {
    transform: translateY(-2px);
    border-color: #cbd5e1;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
}
.zod-cat-pill {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    color: #475569;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    transition: all 0.15s ease;
}
.zod-cat-pill:hover {
    color: #0f172a;
    background: #f8fafc;
    border-color: #cbd5e1;
}
.zod-cat-pill.active {
    color: #ffffff !important;
    background: #0f172a !important;
    border-color: #0f172a !important;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

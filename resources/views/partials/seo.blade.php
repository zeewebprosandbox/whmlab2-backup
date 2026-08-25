@if($seo)

    <meta name="title" Content="{{ gs()->siteName(__($pageTitle)) }}">
    <meta name="description" content="{{ @$seoContents->description ?? $seo->description }}">
    <meta name="keywords" content="{{ is_array(@$seoContents->keywords ?? $seo->keywords) ? implode(',', @$seoContents->keywords ?? $seo->keywords) : (@$seoContents->keywords ?? $seo->keywords) }}">
    <link rel="shortcut icon" href="{{ siteFavicon() }}" type="image/x-icon">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta name="robots" content="{{ @$seoContents->meta_robots ?? ($seo->meta_robots ?? 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1') }}" />
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />

    {{--<!-- Apple & PWA Tags -->--}}
    <link rel="apple-touch-icon" href="{{ siteLogo() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ gs()->siteName($pageTitle) }}">

    {{--<!-- Google / Search Engine Meta -->--}}
    <meta itemprop="name" content="{{ gs()->siteName($pageTitle) }}">
    <meta itemprop="description" content="{{ @$seoContents->description ?? $seo->description }}">
    <meta itemprop="image" content="{{ $seoImage ?? getImage(getFilePath('seo') .'/'. $seo->image) }}">

    {{--<!-- OpenGraph Social Tags -->--}}
    <meta property="og:type" content="{{ isset($blog) ? 'article' : 'website' }}">
    <meta property="og:site_name" content="{{ gs('site_name') }}">
    <meta property="og:title" content="{{ @$seoContents->social_title ?? $seo->social_title ?? gs()->siteName($pageTitle) }}">
    <meta property="og:description" content="{{ @$seoContents->social_description ?? $seo->social_description ?? (@$seoContents->description ?? $seo->description) }}">
    <meta property="og:image" content="{{ $seoImage ?? getImage(getFilePath('seo') .'/'. $seo->image) }}">
    <meta property="og:url" content="{{ url()->current() }}">

    {{--<!-- Twitter Card Tags -->--}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ @$seoContents->social_title ?? $seo->social_title ?? gs()->siteName($pageTitle) }}">
    <meta name="twitter:description" content="{{ @$seoContents->social_description ?? $seo->social_description ?? (@$seoContents->description ?? $seo->description) }}">
    <meta name="twitter:image" content="{{ $seoImage ?? getImage(getFilePath('seo') .'/'. $seo->image) }}">

    {{--<!-- JSON-LD Structured Data for AI & Search Engines -->--}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "WebSite",
          "@id": "{{ url('/') }}#website",
          "url": "{{ url('/') }}",
          "name": "{{ gs('site_name') }}",
          "description": "{{ @$seo->description }}",
          "publisher": {
            "@id": "{{ url('/') }}#organization"
          },
          "potentialAction": [
            {
              "@type": "SearchAction",
              "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ url('/register/domain') }}?domain={search_term_string}"
              },
              "query-input": "required name=search_term_string"
            }
          ]
        },
        {
          "@type": "Organization",
          "@id": "{{ url('/') }}#organization",
          "name": "{{ gs('site_name') }}",
          "url": "{{ url('/') }}",
          "logo": {
            "@type": "ImageObject",
            "url": "{{ siteLogo() }}",
            "caption": "{{ gs('site_name') }} Logo"
          },
          "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "{{ gs('contact_phone') ?? '+1-800-ZOD-HOST' }}",
            "contactType": "customer service",
            "availableLanguage": "English"
          }
        }
        @if(isset($blog))
        ,{
          "@type": "BlogPosting",
          "@id": "{{ url()->current() }}#article",
          "headline": "{{ @$blog->data_values->title }}",
          "description": "{{ @$seoContents->description ?? Str::limit(strip_tags(@$blog->data_values->description), 160) }}",
          "mainEntityOfPage": "{{ url()->current() }}",
          "datePublished": "{{ @$blog->created_at ? @$blog->created_at->toIso8601String() : date('c') }}",
          "dateModified": "{{ @$blog->updated_at ? @$blog->updated_at->toIso8601String() : date('c') }}",
          "publisher": {
            "@id": "{{ url('/') }}#organization"
          },
          "author": {
            "@type": "Organization",
            "name": "{{ gs('site_name') }} Editorial Team"
          },
          "keywords": "{{ is_array(@$seoContents->keywords) ? implode(',', @$seoContents->keywords) : (@$seoContents->keywords ?? '') }}"
        }
        @endif
        ,{
          "@type": "BreadcrumbList",
          "@id": "{{ url()->current() }}#breadcrumb",
          "itemListElement": [
            {
              "@type": "ListItem",
              "position": 1,
              "name": "Home",
              "item": "{{ url('/') }}"
            }
            @if(isset($blog))
            ,{
              "@type": "ListItem",
              "position": 2,
              "name": "Announcements & Knowledgebase",
              "item": "{{ route('blogs') }}"
            },
            {
              "@type": "ListItem",
              "position": 3,
              "name": "{{ @$blog->data_values->title }}",
              "item": "{{ url()->current() }}"
            }
            @else
            ,{
              "@type": "ListItem",
              "position": 2,
              "name": "{{ __($pageTitle) }}",
              "item": "{{ url()->current() }}"
            }
            @endif
          ]
        }
      ]
    }
    </script>
@endif

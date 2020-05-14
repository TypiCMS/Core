@if (app()->environment('production') and config('typicms.google_analytics_code'))

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('typicms.google_analytics_code') }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '{{ config('typicms.google_analytics_code') }}');
</script>

@endif

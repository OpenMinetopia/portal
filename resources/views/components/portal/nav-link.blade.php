@props(['href', 'active' => false, 'icon'])
{{-- A sidebar link: icon, label, and the green square on the active page. --}}
<li>
    <a href="{{ $href }}" class="omt-nav-link" @if($active) aria-current="page" @endif>
        <x-dynamic-component :component="'heroicon-o-'.$icon" />
        <span>{{ $slot }}</span>
    </a>
</li>

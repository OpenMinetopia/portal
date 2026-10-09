{{-- The flashed status message on the guest pages (sent reset link, changed password). --}}
@if (session('status'))
    <div {{ $attributes->class('mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900 dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-200') }} role="status">
        {{ session('status') }}
    </div>
@endif

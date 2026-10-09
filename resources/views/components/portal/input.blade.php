@props(['name', 'label', 'type' => 'text', 'hint' => null, 'value' => null])
{{-- A labelled input with its validation error. Extra attributes go to the <input>. --}}
<div>
    <label for="{{ $name }}" class="block text-sm font-semibold text-gray-900 dark:text-white">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
           @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
           {{ $attributes->class([
               'mt-1.5 block w-full rounded-md border-0 bg-white px-3 py-2.5 text-gray-900 ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:text-white dark:focus:ring-indigo-400',
               'ring-gray-300 dark:ring-gray-700' => ! $errors->has($name),
               'ring-red-600 dark:ring-red-400' => $errors->has($name),
           ]) }}>
    @if ($hint)
        <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-400">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
    {{ $slot }}
</div>
